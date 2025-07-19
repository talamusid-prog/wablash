const express = require('express');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const cors = require('cors');
const helmet = require('helmet');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const compression = require('compression');
const rateLimit = require('express-rate-limit');
const NodeCache = require('node-cache');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;
const API_KEY = process.env.API_KEY || 'your_api_key';

// Performance monitoring
const performanceCache = new NodeCache({ stdTTL: 300 }); // 5 minutes cache
const contactCache = new NodeCache({ stdTTL: 1800 }); // 30 minutes cache for contacts
const groupCache = new NodeCache({ stdTTL: 1800 }); // 30 minutes cache for groups

// Rate limiting
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
    message: {
        success: false,
        error: 'Too many requests from this IP, please try again later.'
    },
    standardHeaders: true,
    legacyHeaders: false,
});

// Configure multer with memory storage for better performance
const storage = multer.memoryStorage();
const upload = multer({
    storage: storage,
    limits: {
        fileSize: 16 * 1024 * 1024, // 16MB limit
        files: 1
    },
    fileFilter: (req, file, cb) => {
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt'];
        const fileExtension = path.extname(file.originalname).toLowerCase();
        
        if (allowedExtensions.includes(fileExtension)) {
            cb(null, true);
        } else {
            cb(new Error('File type not allowed'), false);
        }
    }
});

// Middleware
app.use(helmet({
    contentSecurityPolicy: false, // Disable CSP for WhatsApp Web
    crossOriginEmbedderPolicy: false
}));
app.use(cors());
app.use(compression());
app.use(express.json({ limit: '16mb' }));
app.use(express.urlencoded({ extended: true, limit: '16mb' }));
app.use(limiter);

// Optimized session storage using WeakMap for better memory management
const sessions = new Map();
const sessionTimers = new Map();

// Puppeteer connection pool configuration
const puppeteerConfig = {
    headless: true,
    executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || undefined,
    args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu',
        '--disable-background-timer-throttling',
        '--disable-backgrounding-occluded-windows',
        '--disable-renderer-backgrounding',
        '--disable-features=TranslateUI',
        '--disable-ipc-flooding-protection',
        '--memory-pressure-off',
        '--max_old_space_size=4096',
        '--disable-web-security',
        '--disable-features=VizDisplayCompositor',
        '--disable-extensions',
        '--disable-plugins',
        '--disable-default-apps',
        '--disable-sync',
        '--disable-translate',
        '--hide-scrollbars',
        '--mute-audio',
        '--no-default-browser-check',
        '--safebrowsing-disable-auto-update',
        '--ignore-certificate-errors',
        '--ignore-ssl-errors',
        '--ignore-certificate-errors-spki-list',
        '--disable-blink-features=AutomationControlled',
        '--disable-features=VizDisplayCompositor',
        '--disable-ipc-flooding-protection',
        '--disable-renderer-backgrounding',
        '--disable-backgrounding-occluded-windows',
        '--disable-background-timer-throttling',
        '--disable-features=TranslateUI',
        '--disable-ipc-flooding-protection',
        '--disable-features=VizDisplayCompositor',
        '--disable-extensions',
        '--disable-plugins',
        '--disable-default-apps',
        '--disable-sync',
        '--disable-translate',
        '--hide-scrollbars',
        '--mute-audio',
        '--no-default-browser-check',
        '--safebrowsing-disable-auto-update',
        '--ignore-certificate-errors',
        '--ignore-ssl-errors',
        '--ignore-certificate-errors-spki-list',
        '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        '--window-size=1920,1080',
        '--start-maximized',
        '--disable-notifications',
        '--disable-popup-blocking',
        '--disable-prompt-on-repost',
        '--disable-hang-monitor',
        '--disable-client-side-phishing-detection',
        '--disable-component-update',
        '--disable-domain-reliability',
        '--disable-features=AudioServiceOutOfProcess'
    ],
    defaultViewport: {
        width: 1920,
        height: 1080
    }
};

// Authentication middleware with caching
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

// Session cleanup utility
const cleanupSession = async (sessionId) => {
    const session = sessions.get(sessionId);
    if (session) {
        try {
            if (session.client && session.client.destroy) {
                await session.client.destroy();
            }
            sessions.delete(sessionId);
            
            // Clear related caches
            contactCache.del(`contacts_${sessionId}`);
            groupCache.del(`groups_${sessionId}`);
            
            console.log(`Session ${sessionId} cleaned up successfully`);
        } catch (error) {
            console.error(`Error cleaning up session ${sessionId}:`, error);
        }
    }
};

// Auto cleanup sessions after 24 hours
const setupSessionCleanup = (sessionId) => {
    const timer = setTimeout(async () => {
        await cleanupSession(sessionId);
        sessionTimers.delete(sessionId);
    }, 24 * 60 * 60 * 1000); // 24 hours
    
    sessionTimers.set(sessionId, timer);
};

// Performance monitoring middleware
const performanceMonitor = (req, res, next) => {
    const start = Date.now();
    const originalSend = res.send;
    
    res.send = function(data) {
        const duration = Date.now() - start;
        const endpoint = req.path;
        
        // Store performance data
        const currentData = performanceCache.get(endpoint) || { count: 0, totalTime: 0, avgTime: 0 };
        currentData.count++;
        currentData.totalTime += duration;
        currentData.avgTime = currentData.totalTime / currentData.count;
        
        performanceCache.set(endpoint, currentData);
        
        // Add performance header
        res.set('X-Response-Time', `${duration}ms`);
        
        originalSend.call(this, data);
    };
    
    next();
};

app.use(performanceMonitor);

// Routes
app.get('/health', (req, res) => {
    const memoryUsage = process.memoryUsage();
    const uptime = process.uptime();
    
    // Get active sessions info
    const activeSessions = Array.from(sessions.entries()).map(([id, session]) => ({
        sessionId: id,
        status: session.status,
        createdAt: session.createdAt,
        lastActivity: session.lastActivity,
        hasQR: !!session.qrCode,
        qrAge: session.qrGeneratedAt ? Math.round((Date.now() - session.qrGeneratedAt.getTime()) / 1000) + 's' : null
    }));
    
    res.json({
        success: true,
        message: 'WhatsApp Engine is running',
        timestamp: new Date().toISOString(),
        performance: {
            memoryUsage: {
                rss: Math.round(memoryUsage.rss / 1024 / 1024) + ' MB',
                heapUsed: Math.round(memoryUsage.heapUsed / 1024 / 1024) + ' MB',
                heapTotal: Math.round(memoryUsage.heapTotal / 1024 / 1024) + ' MB'
            },
            uptime: Math.round(uptime) + ' seconds',
            activeSessions: sessions.size,
            sessions: activeSessions,
            cacheStats: {
                contacts: contactCache.getStats(),
                groups: groupCache.getStats(),
                performance: performanceCache.getStats()
            }
        }
    });
});

// Test connection endpoint - untuk cek apakah server responsive
app.get('/test', (req, res) => {
    res.json({
        success: true,
        message: 'WhatsApp Engine is responsive',
        timestamp: new Date().toISOString(),
        uptime: Math.round(process.uptime()) + ' seconds'
    });
});

// Simple ping endpoint
app.get('/ping', (req, res) => {
    res.json({
        success: true,
        message: 'pong',
        timestamp: new Date().toISOString()
    });
});

// Create new session with optimized configuration
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
                clientId: sessionId,
                dataPath: './.wwebjs_auth'
            }),
            puppeteer: puppeteerConfig,
            webVersion: '2.2402.5',
            webVersionCache: {
                type: 'local'
            },
            takeoverOnConflict: true,
            takeoverTimeoutMs: 180000,
            qrMaxRetries: 5,
            authTimeoutMs: 300000,
            qrQualityOptions: {
                quality: 0.8,
                margin: 1
            }
        });

        // Store session with optimized data structure
        sessions.set(sessionId, {
            client,
            status: 'connecting',
            phoneNumber,
            qrCode: null,
            createdAt: new Date(),
            lastActivity: new Date()
        });

        // Setup auto cleanup
        setupSessionCleanup(sessionId);

        // Event handlers with optimized error handling

        client.on('ready', () => {
            const session = sessions.get(sessionId);
            if (session) {
                session.status = 'connected';
                session.qrCode = null;
                session.lastActivity = new Date();
            }
            console.log(`Session ${sessionId} is ready`);
        });

        client.on('authenticated', () => {
            const session = sessions.get(sessionId);
            if (session) {
                session.status = 'authenticated';
                session.lastActivity = new Date();
            }
            console.log(`Session ${sessionId} authenticated`);
        });

        client.on('auth_failure', (msg) => {
            const session = sessions.get(sessionId);
            if (session) {
                session.status = 'auth_failed';
                session.lastActivity = new Date();
            }
            console.error(`Session ${sessionId} auth failure:`, msg);
        });

        client.on('disconnected', (reason) => {
            const session = sessions.get(sessionId);
            if (session) {
                session.status = 'disconnected';
                session.lastActivity = new Date();
            }
            console.log(`Session ${sessionId} disconnected:`, reason);
        });

        client.on('loading_screen', (percent, message) => {
            console.log(`Session ${sessionId} loading: ${percent}% - ${message}`);
        });

        client.on('qr', async (qr) => {
            try {
                console.log(`Generating QR code for session: ${sessionId}`);
                
                // Optimized QR code generation with better error handling
                const qrCodeDataUrl = await qrcode.toDataURL(qr, { 
                    errorCorrectionLevel: 'M',
                    type: 'image/png',
                    quality: 0.8,
                    margin: 1,
                    width: 256,
                    color: {
                        dark: '#000000',
                        light: '#FFFFFF'
                    }
                });
                
                const session = sessions.get(sessionId);
                if (session) {
                    session.qrCode = qrCodeDataUrl;
                    session.status = 'qr_ready';
                    session.lastActivity = new Date();
                    session.qrGeneratedAt = new Date();
                    
                    console.log(`✅ QR Code generated successfully for session: ${sessionId}`);
                } else {
                    console.warn(`⚠️  Session ${sessionId} not found when generating QR code`);
                }
            } catch (error) {
                console.error('❌ Error generating QR code:', error);
                
                // Update session status to error
                const session = sessions.get(sessionId);
                if (session) {
                    session.status = 'qr_error';
                    session.lastActivity = new Date();
                }
            }
        });

        // Initialize client with timeout
        const initPromise = client.initialize();
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Initialization timeout - server took too long to respond')), 300000); // 5 minutes timeout
        });

        try {
            await Promise.race([initPromise, timeoutPromise]);
        } catch (timeoutError) {
            console.error('Session initialization timeout:', timeoutError.message);
            throw new Error('Session initialization timeout - please try again');
        }

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
        
        // Cleanup on error - pastikan sessionId ada dalam scope
        const sessionIdToCleanup = req.body?.sessionId;
        if (sessionIdToCleanup && sessions.has(sessionIdToCleanup)) {
            await cleanupSession(sessionIdToCleanup);
        }
        
        // Pastikan response belum dikirim dan format yang benar
        if (!res.headersSent) {
            res.status(500).json({
                success: false,
                error: 'Failed to create session',
                message: error.message
            });
        }
    }
});

// Get QR code status with optimized response
app.get('/sessions/:sessionId/qr-status', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const session = sessions.get(sessionId);

        if (!session) {
            return res.status(404).json({
                success: false,
                error: 'Session not found'
            });
        }

        // Update last activity
        session.lastActivity = new Date();

        // Return status immediately
        const response = {
            success: true,
            data: {
                sessionId,
                status: session.status,
                lastActivity: session.lastActivity
            }
        };

        // Add QR code if available and not expired (5 minutes)
        if (session.qrCode && session.qrGeneratedAt) {
            const qrAge = Date.now() - session.qrGeneratedAt.getTime();
            const qrExpiry = 5 * 60 * 1000; // 5 minutes
            
            if (qrAge < qrExpiry) {
                response.data.qrCode = session.qrCode;
                response.data.qrAge = Math.round(qrAge / 1000) + ' seconds';
            } else {
                // QR code expired, remove it
                session.qrCode = null;
                session.qrGeneratedAt = null;
                response.data.message = 'QR code expired, waiting for new one...';
            }
        }

        res.json(response);

    } catch (error) {
        console.error('Error getting QR status:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get QR status'
        });
    }
});

// Get QR code with caching
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

        // Update last activity
        session.lastActivity = new Date();

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

        // Check if QR code is available and not expired
        if (session.qrCode && session.qrGeneratedAt) {
            const qrAge = Date.now() - session.qrGeneratedAt.getTime();
            const qrExpiry = 5 * 60 * 1000; // 5 minutes
            
            if (qrAge < qrExpiry) {
                return res.json({
                    success: true,
                    data: {
                        sessionId,
                        status: session.status,
                        qrCode: session.qrCode,
                        qrAge: Math.round(qrAge / 1000) + ' seconds'
                    }
                });
            } else {
                // QR code expired, remove it
                session.qrCode = null;
                session.qrGeneratedAt = null;
            }
        }

        res.json({
            success: true,
            data: {
                sessionId,
                status: session.status,
                message: 'QR code not ready yet or expired'
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

        // Update last activity
        session.lastActivity = new Date();

        res.json({
            success: true,
            data: {
                sessionId,
                status: session.status,
                phoneNumber: session.phoneNumber,
                createdAt: session.createdAt,
                lastActivity: session.lastActivity
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

// Optimized send message with retry mechanism
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

        // Update last activity
        session.lastActivity = new Date();

        // Format phone number
        const formattedNumber = to.includes('@c.us') ? to : `${to}@c.us`;

        // Retry mechanism with proper message type handling
        let result;
        let retries = 3;
        
        while (retries > 0) {
            try {
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
                break;
            } catch (error) {
                retries--;
                if (retries === 0) {
                    throw error;
                }
                await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second before retry
            }
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
            error: 'Failed to send message: ' + error.message
        });
    }
});

// Optimized send media message with memory management
app.post('/sessions/:sessionId/send-media', authenticate, upload.single('file'), async (req, res) => {
    let tempFilePath = null;
    
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

        // Update last activity
        session.lastActivity = new Date();

        // Format phone number
        const formattedNumber = to.includes('@c.us') ? to : `${to}@c.us`;

        // Save file temporarily
        tempFilePath = path.join(__dirname, 'uploads', `temp_${Date.now()}_${file.originalname}`);
        fs.writeFileSync(tempFilePath, file.buffer);

        console.log('Sending media message:', {
            sessionId,
            to: formattedNumber,
            message,
            type,
            originalFileName: file.originalname,
            fileSize: file.size,
            mimeType: file.mimetype
        });

        // Create MessageMedia object
        const media = MessageMedia.fromFilePath(tempFilePath);

        // Send message with media
        const result = await session.client.sendMessage(formattedNumber, media, {
            caption: message
        });

        res.json({
            success: true,
            message: 'Media message sent successfully',
            data: {
                messageId: result.id._serialized,
                to: formattedNumber,
                type: type,
                originalFileName: file.originalname,
                fileSize: file.size
            }
        });

    } catch (error) {
        console.error('Error sending media message:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to send media message: ' + error.message
        });
    } finally {
        // Clean up temporary file
        if (tempFilePath && fs.existsSync(tempFilePath)) {
            try {
                fs.unlinkSync(tempFilePath);
                console.log('Cleaned up temporary file:', tempFilePath);
            } catch (cleanupError) {
                console.error('Error cleaning up temporary file:', cleanupError);
            }
        }
    }
});

// Optimized grab group contacts with caching
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

        // Update last activity
        session.lastActivity = new Date();

        // Check cache first
        const cacheKey = `groups_${sessionId}`;
        const cachedGroups = groupCache.get(cacheKey);
        
        if (cachedGroups) {
            return res.json({
                success: true,
                message: 'Group contacts retrieved from cache',
                data: {
                    sessionId,
                    totalGroups: cachedGroups.length,
                    groups: cachedGroups,
                    cached: true
                }
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

        // Cache the result
        groupCache.set(cacheKey, groupContacts);

        res.json({
            success: true,
            message: 'Group contacts retrieved successfully',
            data: {
                sessionId,
                totalGroups: groupContacts.length,
                groups: groupContacts,
                cached: false
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

// Optimized grab individual contacts with caching
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

        // Update last activity
        session.lastActivity = new Date();

        // Check cache first
        const cacheKey = `contacts_${sessionId}`;
        const cachedContacts = contactCache.get(cacheKey);
        
        if (cachedContacts) {
            return res.json({
                success: true,
                message: 'Individual contacts retrieved from cache',
                data: {
                    sessionId,
                    totalContacts: cachedContacts.length,
                    contacts: cachedContacts,
                    cached: true
                }
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

        // Cache the result
        contactCache.set(cacheKey, individualContacts);

        res.json({
            success: true,
            message: 'Individual contacts retrieved successfully',
            data: {
                sessionId,
                totalContacts: individualContacts.length,
                contacts: individualContacts,
                cached: false
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

// Optimized grab all contacts with caching
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

        // Update last activity
        session.lastActivity = new Date();

        // Check cache first
        const groupsCacheKey = `groups_${sessionId}`;
        const contactsCacheKey = `contacts_${sessionId}`;
        
        const cachedGroups = groupCache.get(groupsCacheKey);
        const cachedContacts = contactCache.get(contactsCacheKey);
        
        if (cachedGroups && cachedContacts) {
            return res.json({
                success: true,
                message: 'All contacts retrieved from cache',
                data: {
                    sessionId,
                    totalGroups: cachedGroups.length,
                    totalContacts: cachedContacts.length,
                    groups: cachedGroups,
                    contacts: cachedContacts,
                    cached: true
                }
            });
        }

        // Get all chats (groups) and contacts in parallel
        const [chats, contacts] = await Promise.all([
            session.client.getChats(),
            session.client.getContacts()
        ]);

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

        // Cache the results
        groupCache.set(groupsCacheKey, groupContacts);
        contactCache.set(contactsCacheKey, individualContacts);

        res.json({
            success: true,
            message: 'All contacts retrieved successfully',
            data: {
                sessionId,
                totalGroups: groupContacts.length,
                totalContacts: individualContacts.length,
                groups: groupContacts,
                contacts: individualContacts,
                cached: false
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

// Clear cache for specific session
app.delete('/sessions/:sessionId/cache', authenticate, (req, res) => {
    try {
        const { sessionId } = req.params;
        
        contactCache.del(`contacts_${sessionId}`);
        groupCache.del(`groups_${sessionId}`);
        
        res.json({
            success: true,
            message: 'Cache cleared successfully'
        });
    } catch (error) {
        console.error('Error clearing cache:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to clear cache'
        });
    }
});

// Delete session with proper cleanup
app.delete('/sessions/:sessionId', authenticate, async (req, res) => {
    try {
        const { sessionId } = req.params;
        
        await cleanupSession(sessionId);
        
        // Clear timer if exists
        const timer = sessionTimers.get(sessionId);
        if (timer) {
            clearTimeout(timer);
            sessionTimers.delete(sessionId);
        }

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

// Get all sessions with performance data
app.get('/sessions', authenticate, (req, res) => {
    try {
        const sessionList = Array.from(sessions.entries()).map(([sessionId, session]) => ({
            sessionId,
            status: session.status,
            phoneNumber: session.phoneNumber,
            createdAt: session.createdAt,
            lastActivity: session.lastActivity
        }));

        res.json({
            success: true,
            data: sessionList,
            performance: {
                activeSessions: sessions.size,
                memoryUsage: {
                    rss: Math.round(process.memoryUsage().rss / 1024 / 1024) + ' MB',
                    heapUsed: Math.round(process.memoryUsage().heapUsed / 1024 / 1024) + ' MB'
                }
            }
        });

    } catch (error) {
        console.error('Error getting sessions:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get sessions'
        });
    }
});

// Get performance statistics
app.get('/performance', authenticate, (req, res) => {
    try {
        const memoryUsage = process.memoryUsage();
        const uptime = process.uptime();
        
        res.json({
            success: true,
            data: {
                memoryUsage: {
                    rss: Math.round(memoryUsage.rss / 1024 / 1024) + ' MB',
                    heapUsed: Math.round(memoryUsage.heapUsed / 1024 / 1024) + ' MB',
                    heapTotal: Math.round(memoryUsage.heapTotal / 1024 / 1024) + ' MB',
                    external: Math.round(memoryUsage.external / 1024 / 1024) + ' MB'
                },
                uptime: Math.round(uptime) + ' seconds',
                activeSessions: sessions.size,
                cacheStats: {
                    contacts: contactCache.getStats(),
                    groups: groupCache.getStats(),
                    performance: performanceCache.getStats()
                },
                endpointPerformance: performanceCache.mget(performanceCache.keys())
            }
        });
    } catch (error) {
        console.error('Error getting performance stats:', error);
        res.status(500).json({
            success: false,
            error: 'Failed to get performance stats'
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
app.listen(PORT, '0.0.0.0', () => {
    console.log(`WhatsApp Engine (Optimized) running on port ${PORT}`);
    console.log(`Health check: http://127.0.0.1:${PORT}/health`);
    console.log(`Performance stats: http://127.0.0.1:${PORT}/performance`);
    console.log(`Server accessible from: http://127.0.0.1:${PORT} or http://localhost:${PORT}`);
});

// Graceful shutdown with cleanup
const gracefulShutdown = async (signal) => {
    console.log(`${signal} received, shutting down gracefully`);
    
    // Clear all timers
    for (const [sessionId, timer] of sessionTimers.entries()) {
        clearTimeout(timer);
    }
    sessionTimers.clear();
    
    // Destroy all sessions
    const cleanupPromises = Array.from(sessions.keys()).map(sessionId => cleanupSession(sessionId));
    await Promise.all(cleanupPromises);
    
    // Clear all caches
    contactCache.flushAll();
    groupCache.flushAll();
    performanceCache.flushAll();
    
    console.log('Cleanup completed, exiting...');
    process.exit(0);
};

process.on('SIGTERM', () => gracefulShutdown('SIGTERM'));
process.on('SIGINT', () => gracefulShutdown('SIGINT'));

// Memory leak prevention
setInterval(() => {
    const memoryUsage = process.memoryUsage();
    const heapUsedMB = memoryUsage.heapUsed / 1024 / 1024;
    
    // If heap usage is too high, force garbage collection
    if (heapUsedMB > 500) { // 500MB threshold
        console.log('High memory usage detected, forcing garbage collection...');
        if (global.gc) {
            global.gc();
        }
    }
}, 300000); // Check every 5 minutes 