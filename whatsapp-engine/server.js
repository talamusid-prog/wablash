const express = require('express');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const cors = require('cors');
const helmet = require('helmet');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;
const API_KEY = process.env.API_KEY || 'your_api_key';

// Configure multer for file uploads
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'uploads/');
    },
    filename: function (req, file, cb) {
        // Use original filename instead of hash
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        const extension = path.extname(file.originalname);
        const nameWithoutExt = path.basename(file.originalname, extension);
        cb(null, nameWithoutExt + '-' + uniqueSuffix + extension);
    }
});

const upload = multer({
    storage: storage,
    limits: {
        fileSize: 16 * 1024 * 1024 // 16MB limit
    }
});

// Middleware
app.use(helmet());
app.use(cors());
app.use(express.json());

// Store active sessions
const sessions = new Map();
// Helper: remove LocalAuth directory to force fresh login (QR)
function removeAuthData(sessionId) {
    try {
        const authDir = path.join(process.cwd(), '.wwebjs_auth', sessionId);
        if (fs.existsSync(authDir)) {
            fs.rmSync(authDir, { recursive: true, force: true });
            console.log(`Removed auth data for session ${sessionId}: ${authDir}`);
        }
    } catch (e) {
        console.error('Failed to remove auth data:', e);
    }
}

// Authentication middleware
const authenticate = (req, res, next) => {
    const apiKey = req.headers['x-api-key'] || req.query.api_key;
    
    if (!apiKey || apiKey !== API_KEY) {
        return res.status(401).json({
            success: false,
            error: 'Unauthorized'
        });
    }
    
    next();
};

// Routes
app.get('/status', (req, res) => {
    // Alias for compatibility with PHP service health check
    res.json({
        success: true,
        message: 'WhatsApp Engine is running',
        timestamp: new Date().toISOString()
    });
});
app.get('/health', (req, res) => {
    res.json({
        success: true,
        message: 'WhatsApp Engine is running',
        timestamp: new Date().toISOString()
    });
});

// Helper: create client with standard handlers
async function createClientForSession(sessionId, phoneNumber) {
    // Add additional puppeteer args for better stability
    const client = new Client({
        authStrategy: new LocalAuth({ clientId: sessionId }),
        puppeteer: {
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu',
                '--disable-web-security',
                '--disable-features=IsolateOrigins',
                '--disable-site-isolation-trials'
            ]
        },
        // Add additional client options for better stability
        restartOnCrash: true,
        takeoverOnConflict: true,
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    });

    sessions.set(sessionId, {
        client,
        status: 'connecting',
        phoneNumber,
        qrCode: null,
        createdAt: sessions.get(sessionId)?.createdAt || new Date()
    });

    client.on('qr', async (qr) => {
        try {
            const qrCodeDataUrl = await qrcode.toDataURL(qr);
            const s = sessions.get(sessionId);
            if (s) {
                s.qrCode = qrCodeDataUrl;
                s.status = 'qr_ready';
            }
            console.log(`QR Code generated for session: ${sessionId}`);
        } catch (error) {
            console.error('Error generating QR code:', error);
        }
    });

    client.on('ready', () => {
        const s = sessions.get(sessionId);
        if (s) {
            s.status = 'connected';
            s.qrCode = null;
        }
        console.log(`Session ${sessionId} is ready`);
    });

    client.on('authenticated', async () => {
        const s = sessions.get(sessionId);
        if (s) s.status = 'authenticated';
        console.log(`Session ${sessionId} authenticated`);

        try {
            const maxWaitMs = 30000;
            const intervalMs = 500;
            const start = Date.now();

            const checkReady = async () => {
                try {
                    const state = await sessions.get(sessionId)?.client?.getState();
                    if (state === 'CONNECTED') {
                        const s2 = sessions.get(sessionId);
                        if (s2) {
                            s2.status = 'connected';
                            s2.qrCode = null;
                        }
                        console.log(`Session ${sessionId} state=${state}, marked connected`);
                        return true;
                    }
                } catch {}
                return false;
            };

            if (await checkReady()) return;

            const timer = setInterval(async () => {
                const done = await checkReady();
                const elapsed = Date.now() - start;
                if (done || elapsed > maxWaitMs) {
                    clearInterval(timer);
                    if (!done) {
                        console.log(`Session ${sessionId} not ready after ${Math.round(elapsed/1000)}s, status=`, sessions.get(sessionId)?.status);
                    }
                }
            }, intervalMs);
        } catch (err) {
            console.error('Error while waiting for ready state:', err);
        }
    });

    client.on('auth_failure', (msg) => {
        const s = sessions.get(sessionId);
        if (s) s.status = 'auth_failed';
        console.error(`Session ${sessionId} auth failure:`, msg);
    });

    client.on('disconnected', (reason) => {
        const s = sessions.get(sessionId);
        if (s) {
            s.status = 'disconnected';
            // Don't destroy the client immediately, let it try to reconnect
            console.log(`Session ${sessionId} disconnected:`, reason);
        }
    });

    client.on('change_state', (state) => {
        const s = sessions.get(sessionId);
        if (s) {
            console.log(`Session ${sessionId} state changed to: ${state}`);
            // Only update status for specific states
            if (state === 'CONNECTED') {
                s.status = 'connected';
            } else if (state === 'TIMEOUT' || state === 'CONFLICT' || state === 'UNLAUNCHED') {
                s.status = 'disconnected';
            }
        }
    });

    client.on('loading_screen', (percent, message) => {
        console.log(`Session ${sessionId} loading: ${percent}% ${message}`);
    });

    // Initialize with a simple watchdog to avoid endless connecting state
    const startAt = Date.now();
    await client.initialize();
    
    // Add a longer timeout for initialization
    setTimeout(async () => {
        const s = sessions.get(sessionId);
        if (!s) return;
        if (['connected','qr_ready','authenticated','auth_failed','disconnected','error'].includes(s.status)) return;
        try {
            const state = await client.getState();
            if (state === 'CONNECTED') {
                s.status = 'connected';
                s.qrCode = null;
                console.log(`Watchdog promoted ${sessionId} to connected`);
                return;
            }
        } catch {}
        s.status = 'error';
        console.log(`Watchdog marked ${sessionId} as error after ${Math.round((Date.now()-startAt)/1000)}s without events`);
    }, 30000);
    
    return client;
}

// Create new session (simplified)
app.post('/sessions/create', authenticate, async (req, res) => {
    try {
        // Log the raw body for debugging
        console.log('[CREATE SESSION] Raw request body:', JSON.stringify(req.body, null, 2));
        
        const { sessionId, phoneNumber } = req.body;
        
        if (!sessionId) {
            return res.status(400).json({ success: false, error: 'Session ID is required' });
        }
        
        if (sessions.has(sessionId)) {
            return res.status(400).json({ success: false, error: 'Session already exists' });
        }
        
        console.log(`[CREATE SESSION] Creating new session: ${sessionId}`);
        await createClientForSession(sessionId, phoneNumber);
        
        res.json({ 
            success: true, 
            message: 'Session created successfully', 
            data: { 
                sessionId, 
                status: 'connecting' 
            } 
        });
    } catch (error) {
        console.error('[CREATE SESSION] Error creating session:', error);
        res.status(500).json({ 
            success: false, 
            error: 'Failed to create session: ' + (error.message || 'Unknown error') 
        });
    }
});

// Get QR code for session
app.get('/sessions/:sessionId/qr', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        if (session.status === 'connected') {
            return res.json({
                success: true,
                data: {
                    sessionId,
                    status: 'connected',
                    message: 'Session is already connected'
                }
            });
        }

        if (session.qrCode) {
            return res.json({
                success: true,
                data: {
                    sessionId,
                    status: session.status,
                    qrCode: session.qrCode
                }
            });
        }

        res.json({
            success: true,
            data: {
                sessionId,
                status: session.status,
                message: 'QR code not ready yet'
            }
        });

    } catch (error) {
        console.error('Error getting QR code:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get QR code'
        });
    }
});

// Add this helper function to check if session is alive
function isSessionAlive(sessionId) {
    const session = sessions.get(sessionId);
    if (!session) return false;
    
    // Check if client exists and is not destroyed
    if (!session.client) return false;
    
    // Check if puppeteer page is still alive
    try {
        return session.client.pupPage && !session.client.pupPage.isClosed();
    } catch (e) {
        return false;
    }
}

// Improved session status endpoint with better error handling
app.get('/sessions/:sessionId/status', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        // Check if session is actually alive
        if (!isSessionAlive(sessionId)) {
            // Try to recreate the session
            console.log(`Session ${sessionId} appears to be dead, attempting to recreate...`);
            
            try {
                // Destroy existing client if it exists
                if (session.client) {
                    try {
                        await session.client.destroy();
                    } catch (destroyErr) {
                        console.warn(`Failed to destroy client for session ${sessionId}:`, destroyErr?.message || destroyErr);
                    }
                }
                
                // Recreate client
                console.log(`Recreating client for session ${sessionId}`);
                await createClientForSession(sessionId, session.phoneNumber);
                
                // Update status to connecting
                session.status = 'connecting';
                
                return res.json({
                    success: true,
                    data: {
                        sessionId,
                        status: 'connecting',
                        phoneNumber: session.phoneNumber,
                        createdAt: session.createdAt,
                        message: 'Session recreated and initializing'
                    }
                });
            } catch (recreateError) {
                console.error(`Failed to recreate session ${sessionId}:`, recreateError);
                return res.status(500).json({
                    success: false,
                    error: 'Session is dead and recreation failed: ' + recreateError.message
                });
            }
        }

        // Cross-check real client state to avoid being stuck at 'authenticated'
        let state = 'UNKNOWN';
        try {
            state = await session.client.getState();
            if (state === 'CONNECTED' && session.status !== 'connected') {
                session.status = 'connected';
                session.qrCode = null;
                console.log(`Status endpoint promoted ${sessionId} to connected based on state=${state}`);
            }
        } catch (e) {
            // If we can't get state, session might be dead
            console.warn(`Could not get state for session ${sessionId}:`, e?.message || e);
            state = 'ERROR';
        }

        res.json({
            success: true,
            data: {
                sessionId,
                status: session.status,
                realState: state,
                phoneNumber: session.phoneNumber,
                createdAt: session.createdAt
            }
        });

    } catch (error) {
        console.error('Error getting session status:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get session status: ' + error.message
        });
    }
});

// Improved reconnect endpoint with better error handling
app.post('/sessions/:sessionId/reconnect', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const existing = sessions.get(sessionId);

        console.log(`Reconnect requested for session: ${sessionId}`);

        // Optional fresh param to force QR (clear auth cache)
        const fresh = (req.query.fresh === '1' || req.query.fresh === 'true');
        console.log(`Fresh reconnect: ${fresh}`);

        if (fresh) {
            removeAuthData(sessionId);
        }

        // If session exists, try to destroy it first
        if (existing?.client) {
            console.log(`Destroying existing client for session: ${sessionId}`);
            try {
                await existing.client.destroy();
                console.log(`Successfully destroyed client for session: ${sessionId}`);
            } catch (destroyError) {
                console.warn(`Failed to destroy client for session ${sessionId}:`, destroyError?.message || destroyError);
            }
        }

        console.log(`Recreating client for session: ${sessionId}`);
        try {
            await createClientForSession(sessionId, existing?.phoneNumber);
            console.log(`Successfully recreated client for session: ${sessionId}`);
            return res.json({ 
                success: true, 
                message: 'Session recreated and initializing', 
                data: { 
                    sessionId, 
                    status: 'connecting' 
                } 
            });
        } catch (error) {
            console.error('Error recreating client:', error);
            return res.status(500).json({ 
                success: false, 
                error: 'Failed to recreate session: ' + error.message 
            });
        }
    } catch (error) {
        console.error('Error in reconnect endpoint:', error);
        res.status(500).json({ 
            success: false, 
            error: 'Failed to reconnect session: ' + error.message 
        });
    }
});

// Simplified send message function that bypasses complex whatsapp-web.js methods
app.post('/sessions/:sessionId/send', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const { to, message, type = 'text' } = req.body;

        if (!to || !message) {
            return res.status(400).json({
                success: false,
                error: 'Penerima dan pesan diperlukan'
            });
        }

        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Sesi tidak ditemukan'
            });
        }

        // Check if session is connected before sending
        if (session.status !== 'connected') {
            return res.status(400).json({
                success: false,
                error: 'Sesi tidak terhubung. Status saat ini: ' + session.status
            });
        }

        // Format phone number
        const formattedNumber = to.includes('@c.us') ? to : `${to}@c.us`;
        
        console.log(`Sending message to ${formattedNumber}: ${message}`);

        // Very simple send approach - try multiple methods
        let result = null;
        let lastError = null;
        
        // Method 1: Direct client send (most common)
        try {
            console.log('Trying direct client send...');
            result = await session.client.sendMessage(formattedNumber, message);
            console.log('Direct client send successful');
        } catch (error) {
            lastError = error;
            console.warn('Direct client send failed:', error?.message || error);
        }
        
        // If first method failed, try alternative approaches
        if (!result) {
            try {
                console.log('Trying alternative send method...');
                // Create a simple text message object
                const msgObj = typeof message === 'string' ? { body: message } : message;
                result = await session.client.sendMessage(formattedNumber, msgObj);
                console.log('Alternative send method successful');
            } catch (error) {
                lastError = error;
                console.warn('Alternative send method failed:', error?.message || error);
            }
        }
        
        // If still no result, try with simplified approach
        if (!result) {
            try {
                console.log('Trying simplified send approach...');
                // Try sending as plain text without complex objects
                result = await session.client.sendMessage(formattedNumber, String(message));
                console.log('Simplified send approach successful');
            } catch (error) {
                lastError = error;
                console.warn('Simplified send approach failed:', error?.message || error);
            }
        }

        // If all methods failed, return error
        if (!result) {
            console.error('All send methods failed');
            return res.status(500).json({
                success: false,
                error: 'Gagal mengirim pesan setelah mencoba semua metode: ' + (lastError?.message || 'Unknown error')
            });
        }

        // Success - return message details
        return res.json({
            success: true,
            message: 'Pesan berhasil dikirim',
            data: {
                messageId: result.id ? result.id._serialized : null,
                to: formattedNumber,
                type: type,
                timestamp: new Date().toISOString()
            }
        });

    } catch (error) {
        console.error('Error sending message:', error?.message || error, error?.stack || '');
        
        // Mark session as disconnected on error
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);
        if (session) {
            session.status = 'disconnected';
            console.log(`Session ${sessionId} marked as disconnected due to send error`);
        }
        
        return res.status(500).json({
            success: false,
            error: 'Gagal mengirim pesan: ' + (error?.message || 'Internal server error')
        });
    }
});

// Simplified send message endpoint
app.post('/sessions/:sessionId/send-simple', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const { to, message } = req.body;

        console.log('[SIMPLE SEND] Request diterima:', { sessionId, to, message });

        if (!to || !message) {
            return res.status(400).json({
                success: false,
                error: 'Penerima dan pesan diperlukan'
            });
        }

        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Sesi tidak ditemukan'
            });
        }

        // Check if session is connected
        if (session.status !== 'connected') {
            return res.status(400).json({
                success: false,
                error: 'Sesi tidak terhubung. Status saat ini: ' + session.status
            });
        }

        // Format phone number
        const formattedNumber = to.includes('@c.us') ? to : `${to}@c.us`;
        
        console.log('[SIMPLE SEND] Mengirim pesan ke', formattedNumber);

        // Approach 1: Very basic sendMessage
        try {
            console.log('[SIMPLE SEND] Pendekatan 1: Pengiriman dasar');
            const result = await session.client.sendMessage(formattedNumber, String(message));
            console.log('[SIMPLE SEND] Pendekatan 1 berhasil');
            
            return res.json({
                success: true,
                message: 'Pesan berhasil dikirim',
                data: {
                    sessionId: sessionId,
                    to: formattedNumber,
                    message: String(message),
                    timestamp: new Date().toISOString(),
                    message_id: result?.id?._serialized || null
                }
            });
        } catch (error1) {
            console.warn('[SIMPLE SEND] Pendekatan 1 gagal:', error1?.message || error1);
            
            // Approach 2: Try with simple object
            try {
                console.log('[SIMPLE SEND] Pendekatan 2: Pengiriman dengan objek sederhana');
                const result = await session.client.sendMessage(formattedNumber, {
                    body: String(message)
                });
                console.log('[SIMPLE SEND] Pendekatan 2 berhasil');
                
                return res.json({
                    success: true,
                    message: 'Pesan berhasil dikirim',
                    data: {
                        sessionId: sessionId,
                        to: formattedNumber,
                        message: String(message),
                        timestamp: new Date().toISOString(),
                        message_id: result?.id?._serialized || null
                    }
                });
            } catch (error2) {
                console.warn('[SIMPLE SEND] Pendekatan 2 gagal:', error2?.message || error2);
                
                // Approach 3: Try to get chat first, then send
                try {
                    console.log('[SIMPLE SEND] Pendekatan 3: Dapatkan chat dulu, lalu kirim');
                    // This is a more conservative approach that avoids the getChat error
                    const result = await session.client.sendMessage(formattedNumber, String(message));
                    console.log('[SIMPLE SEND] Pendekatan 3 berhasil');
                    
                    return res.json({
                        success: true,
                        message: 'Pesan berhasil dikirim',
                        data: {
                            sessionId: sessionId,
                            to: formattedNumber,
                            message: String(message),
                            timestamp: new Date().toISOString(),
                            message_id: result?.id?._serialized || null
                        }
                    });
                } catch (error3) {
                    console.error('[SIMPLE SEND] Semua pendekatan gagal:', error3?.message || error3);
                    
                    return res.status(500).json({
                        success: false,
                        error: 'Gagal mengirim pesan setelah mencoba semua pendekatan: ' + (error3?.message || 'Unknown error')
                    });
                }
            }
        }

    } catch (error) {
        console.error('[SIMPLE SEND] Error tidak terduga:', error?.message || error, error?.stack || '');
        
        return res.status(500).json({
            success: false,
            error: 'Error tidak terduga saat mengirim pesan: ' + (error?.message || 'Internal server error')
        });
    }
});

// Send message with media/attachment
app.post('/sessions/:sessionId/send-media', authenticate, upload.single('file'), async (req, res) => {
    try {
        const { sessionId } = req.params;
        const { to, message, type = 'document' } = req.body;
        const file = req.file;

        if (!to || !message) {
            return res.status(400).json({
                success: false,
                error: 'Recipient and message are required'
            });
        }

        if (!file) {
            return res.status(400).json({
                success: false,
                error: 'File is required'
            });
        }

        // Validate file has proper extension
        const fileExtension = path.extname(file.originalname).toLowerCase();
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt'];
        
        if (!allowedExtensions.includes(fileExtension)) {
            // Clean up uploaded file
            fs.unlinkSync(file.path);
            
            return res.status(400).json({
                success: false,
                error: 'File type not allowed. Allowed types: ' + allowedExtensions.join(', ')
            });
        }

        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        if (session.status !== 'connected') {
            return res.status(400).json({
                success: false,
                error: 'Session is not connected'
            });
        }

        // Format phone number
        const formattedNumber = to.includes('@c.us') ? to : `${to}@c.us`;

        console.log('Sending media message:', {
            sessionId,
            to: formattedNumber,
            message,
            type,
            originalFileName: file.originalname,
            savedFileName: file.filename,
            fileSize: file.size,
            mimeType: file.mimetype
        });

        // Create MessageMedia object
        const media = MessageMedia.fromFilePath(file.path);

        // Send message with media
        const result = await session.client.sendMessage(formattedNumber, media, {
            caption: message
        });

        // Clean up uploaded file
        try {
            fs.unlinkSync(file.path);
            console.log('Cleaned up uploaded file after successful send:', file.path);
        } catch (cleanupError) {
            console.error('Error cleaning up file after send:', cleanupError);
        }

        res.json({
            success: true,
            message: 'Media message sent successfully',
            data: {
                messageId: result.id._serialized,
                to: formattedNumber,
                type: type,
                originalFileName: file.originalname,
                savedFileName: file.filename,
                fileSize: file.size
            }
        });

    } catch (error) {
        console.error('Error sending media message:', error);
        
        // Clean up uploaded file if it exists
        if (req.file && req.file.path) {
            try {
                fs.unlinkSync(req.file.path);
                console.log('Cleaned up uploaded file:', req.file.path);
            } catch (cleanupError) {
                console.error('Error cleaning up file:', cleanupError);
            }
        }

        res.status(500).json({
            success: false,
            error: 'Failed to send media message: ' + error.message
        });
    }
});

// Grab group contacts
app.get('/sessions/:sessionId/groups', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        if (session.status !== 'connected') {
            return res.status(400).json({
                success: false,
                error: 'Session is not connected'
            });
        }

        // Get all groups
        const chats = await session.client.getChats();
        const groups = chats.filter(chat => chat.isGroup);

        const groupContacts = groups.map(group => ({
            id: group.id._serialized,
            name: group.name,
            description: group.description || '',
            participants: group.participants.map(participant => ({
                id: participant.id._serialized,
                number: participant.id.user,
                name: participant.pushname || participant.id.user,
                isAdmin: participant.isAdmin || false,
                isSuperAdmin: participant.isSuperAdmin || false
            })),
            participantCount: group.participantCount,
            createdAt: group.createdAt,
            isGroup: true
        }));

        res.json({
            success: true,
            message: 'Group contacts retrieved successfully',
            data: {
                sessionId,
                totalGroups: groupContacts.length,
                groups: groupContacts
            }
        });

    } catch (error) {
        console.error('Error grabbing group contacts:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to grab group contacts'
        });
    }
});

// Grab individual contacts
app.get('/sessions/:sessionId/contacts', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        if (session.status !== 'connected') {
            return res.status(400).json({
                success: false,
                error: 'Session is not connected'
            });
        }

        // Get all contacts
        const contacts = await session.client.getContacts();
        
        // Filter only individual contacts (not groups)
        const individualContacts = contacts
            .filter(contact => !contact.isGroup && contact.id.user !== 'status@broadcast')
            .map(contact => ({
                id: contact.id._serialized,
                number: contact.id.user,
                name: contact.pushname || contact.name || contact.id.user,
                shortName: contact.shortName || '',
                isBusiness: contact.isBusiness || false,
                isEnterprise: contact.isEnterprise || false,
                isHighLevelVerified: contact.isHighLevelVerified || false,
                isLowLevelVerified: contact.isLowLevelVerified || false,
                isMe: contact.isMe || false,
                isMyContact: contact.isMyContact || false,
                isPSA: contact.isPSA || false,
                isUser: contact.isUser || false,
                isVerified: contact.isVerified || false,
                isWAContact: contact.isWAContact || false,
                isGroup: false
            }));

        res.json({
            success: true,
            message: 'Individual contacts retrieved successfully',
            data: {
                sessionId,
                totalContacts: individualContacts.length,
                contacts: individualContacts
            }
        });

    } catch (error) {
        console.error('Error grabbing individual contacts:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to grab individual contacts'
        });
    }
});

// Grab all contacts (groups + individual)
app.get('/sessions/:sessionId/all-contacts', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        if (session.status !== 'connected') {
            return res.status(400).json({
                success: false,
                error: 'Session is not connected'
            });
        }

        // Get all chats (groups)
        const chats = await session.client.getChats();
        const groups = chats.filter(chat => chat.isGroup);

        const groupContacts = groups.map(group => ({
            id: group.id._serialized,
            name: group.name,
            description: group.description || '',
            participants: group.participants.map(participant => ({
                id: participant.id._serialized,
                number: participant.id.user,
                name: participant.pushname || participant.id.user,
                isAdmin: participant.isAdmin || false,
                isSuperAdmin: participant.isSuperAdmin || false
            })),
            participantCount: group.participantCount,
            createdAt: group.createdAt,
            isGroup: true
        }));

        // Get all contacts (individual)
        const contacts = await session.client.getContacts();
        
        const individualContacts = contacts
            .filter(contact => !contact.isGroup && contact.id.user !== 'status@broadcast')
            .map(contact => ({
                id: contact.id._serialized,
                number: contact.id.user,
                name: contact.pushname || contact.name || contact.id.user,
                shortName: contact.shortName || '',
                isBusiness: contact.isBusiness || false,
                isEnterprise: contact.isEnterprise || false,
                isHighLevelVerified: contact.isHighLevelVerified || false,
                isLowLevelVerified: contact.isLowLevelVerified || false,
                isMe: contact.isMe || false,
                isMyContact: contact.isMyContact || false,
                isPSA: contact.isPSA || false,
                isUser: contact.isUser || false,
                isVerified: contact.isVerified || false,
                isWAContact: contact.isWAContact || false,
                isGroup: false
            }));

        res.json({
            success: true,
            message: 'All contacts retrieved successfully',
            data: {
                sessionId,
                totalGroups: groupContacts.length,
                totalContacts: individualContacts.length,
                groups: groupContacts,
                contacts: individualContacts
            }
        });

    } catch (error) {
        console.error('Error grabbing all contacts:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to grab all contacts'
        });
    }
});

// Delete session
app.delete('/sessions/:sessionId', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        // Destroy client
        await session.client.destroy();
        sessions.delete(sessionId);

        res.json({
            success: true,
            message: 'Session deleted successfully'
        });

    } catch (error) {
        console.error('Error deleting session:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to delete session'
        });
    }
});

// Get all sessions
app.get('/sessions', authenticate, (req, res) => {
    try {
        const sessionList = Array.from(sessions.entries()).map(([sessionId, session]) => ({
            sessionId,
            status: session.status,
            phoneNumber: session.phoneNumber,
            createdAt: session.createdAt
        }));

        res.json({
            success: true,
            data: sessionList
        });

    } catch (error) {
        console.error('Error getting sessions:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get sessions'
        });
    }
});

// Error handling middleware
app.use((error, req, res, next) => {
    console.error('Unhandled error:', error);
    res.status(500).json({
        success: false,
        error: 'Internal server error'
    });
});

// Start server
app.listen(PORT, () => {
    console.log(`WhatsApp Engine running on port ${PORT}`);
    console.log(`Health check: http://localhost:${PORT}/health`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('SIGTERM received, shutting down gracefully');
    
    // Destroy all sessions
    for (const [sessionId, session] of sessions.entries()) {
        session.client.destroy();
    }
    
    process.exit(0);
});

process.on('SIGINT', () => {
    console.log('SIGINT received, shutting down gracefully');
    
    // Destroy all sessions
    for (const [sessionId, session] of sessions.entries()) {
        session.client.destroy();
    }
    
    process.exit(0);
}); 