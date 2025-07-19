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
app.get('/health', (req, res) => {
    res.json({
        success: true,
        message: 'WhatsApp Engine is running',
        timestamp: new Date().toISOString()
    });
});

// Create new session
app.post('/sessions/create', authenticate, async (req, res) => {
    try {
        const { sessionId, phoneNumber } = req.body;
        
        if (!sessionId) {
            return res.status(400).json({
                success: false,
                error: 'Session ID is required'
            });
        }

        if (sessions.has(sessionId)) {
            return res.status(400).json({
                success: false,
                error: 'Session already exists'
            });
        }

        const client = new Client({
            authStrategy: new LocalAuth({
                clientId: sessionId
            }),
            puppeteer: {
                headless: true,
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--no-zygote',
                    '--disable-gpu'
                ]
            }
        });

        // Store session
        sessions.set(sessionId, {
            client,
            status: 'connecting',
            phoneNumber,
            qrCode: null,
            createdAt: new Date()
        });

        // Event handlers
        client.on('qr', async (qr) => {
            try {
                const qrCodeDataUrl = await qrcode.toDataURL(qr);
                sessions.get(sessionId).qrCode = qrCodeDataUrl;
                sessions.get(sessionId).status = 'qr_ready';
                
                console.log(`QR Code generated for session: ${sessionId}`);
            } catch (error) {
                console.error('Error generating QR code:', error);
            }
        });

        client.on('ready', () => {
            sessions.get(sessionId).status = 'connected';
            sessions.get(sessionId).qrCode = null;
            console.log(`Session ${sessionId} is ready`);
        });

        client.on('authenticated', () => {
            sessions.get(sessionId).status = 'authenticated';
            console.log(`Session ${sessionId} authenticated`);
        });

        client.on('auth_failure', (msg) => {
            sessions.get(sessionId).status = 'auth_failed';
            console.error(`Session ${sessionId} auth failure:`, msg);
        });

        client.on('disconnected', (reason) => {
            sessions.get(sessionId).status = 'disconnected';
            console.log(`Session ${sessionId} disconnected:`, reason);
        });

        // Initialize client
        await client.initialize();

        res.json({
            success: true,
            message: 'Session created successfully',
            data: {
                sessionId,
                status: 'connecting'
            }
        });

    } catch (error) {
        console.error('Error creating session:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to create session'
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

// Get session status
app.get('/sessions/:sessionId/status', authenticate, (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        res.json({
            success: true,
            data: {
                sessionId,
                status: session.status,
                phoneNumber: session.phoneNumber,
                createdAt: session.createdAt
            }
        });

    } catch (error) {
        console.error('Error getting session status:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get session status'
        });
    }
});

// Reconnect session
app.post('/sessions/:sessionId/reconnect', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        // Check if client is already connected
        if (session.status === 'connected') {
            return res.json({
                success: true,
                message: 'Session is already connected',
                data: {
                    sessionId,
                    status: session.status
                }
            });
        }

        // Update status to connecting
        session.status = 'connecting';
        session.qrCode = null;

        console.log(`Attempting to reconnect session: ${sessionId}`);

        // Try to initialize the client
        try {
            await session.client.initialize();
            
            res.json({
                success: true,
                message: 'Session reconnection initiated',
                data: {
                    sessionId,
                    status: session.status
                }
            });
        } catch (error) {
            console.error('Error reconnecting session:', error);
            session.status = 'error';
            
            res.status(500).json({
                success: false,
                error: 'Failed to reconnect session: ' + error.message
            });
        }

    } catch (error) {
        console.error('Error in reconnect endpoint:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to reconnect session'
        });
    }
});

// Send message
app.post('/sessions/:sessionId/send', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const { to, message, type = 'text' } = req.body;

        if (!to || !message) {
            return res.status(400).json({
                success: false,
                error: 'Recipient and message are required'
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

        let result;
        switch (type) {
            case 'text':
                result = await session.client.sendMessage(formattedNumber, message);
                break;
            case 'image':
                // Handle image message
                result = await session.client.sendMessage(formattedNumber, message);
                break;
            default:
                result = await session.client.sendMessage(formattedNumber, message);
        }

        res.json({
            success: true,
            message: 'Message sent successfully',
            data: {
                messageId: result.id._serialized,
                to: formattedNumber,
                type: type
            }
        });

    } catch (error) {
        console.error('Error sending message:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to send message'
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