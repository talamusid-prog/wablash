/**
 * Configuration file untuk WhatsApp Engine Optimization
 * Sesuaikan nilai-nilai ini berdasarkan kebutuhan dan environment
 */

module.exports = {
    // Memory Management
    memory: {
        // Memory threshold untuk garbage collection (MB)
        gcThreshold: 500,
        
        // Memory check interval (ms)
        gcCheckInterval: 300000, // 5 menit
        
        // Session auto cleanup timeout (ms)
        sessionCleanupTimeout: 24 * 60 * 60 * 1000, // 24 jam
        
        // Max heap size untuk Node.js (MB)
        maxHeapSize: 4096
    },

    // Cache Configuration
    cache: {
        // Contact cache TTL (seconds)
        contactTTL: 1800, // 30 menit
        
        // Group cache TTL (seconds)
        groupTTL: 1800, // 30 menit
        
        // Performance cache TTL (seconds)
        performanceTTL: 300, // 5 menit
        
        // QR code cache TTL (seconds)
        qrCodeTTL: 60 // 1 menit
    },

    // Rate Limiting
    rateLimit: {
        // Time window (ms)
        windowMs: 15 * 60 * 1000, // 15 menit
        
        // Max requests per IP per window
        maxRequests: 100,
        
        // Skip successful requests
        skipSuccessfulRequests: false,
        
        // Skip failed requests
        skipFailedRequests: false
    },

    // Puppeteer Configuration
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
            '--disable-background-timer-throttling',
            '--disable-backgrounding-occluded-windows',
            '--disable-renderer-backgrounding',
            '--disable-features=TranslateUI',
            '--disable-ipc-flooding-protection',
            '--memory-pressure-off',
            '--max_old_space_size=4096',
            '--disable-extensions',
            '--disable-plugins',
            '--disable-images',
            '--disable-javascript',
            '--disable-web-security',
            '--disable-features=VizDisplayCompositor'
        ],
        defaultViewport: {
            width: 800,
            height: 600
        },
        timeout: 60000 // 60 seconds
    },

    // File Upload Configuration
    fileUpload: {
        // Max file size (bytes)
        maxFileSize: 16 * 1024 * 1024, // 16MB
        
        // Allowed file extensions
        allowedExtensions: [
            '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp',
            '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt',
            '.mp4', '.avi', '.mov', '.wmv', '.flv', '.webm'
        ],
        
        // Max files per request
        maxFiles: 1,
        
        // Temporary file cleanup timeout (ms)
        cleanupTimeout: 300000 // 5 menit
    },

    // Retry Configuration
    retry: {
        // Max retry attempts
        maxAttempts: 3,
        
        // Delay between retries (ms)
        delay: 1000,
        
        // Exponential backoff multiplier
        backoffMultiplier: 2
    },

    // Performance Monitoring
    performance: {
        // Enable performance monitoring
        enabled: true,
        
        // Performance data retention (seconds)
        retentionPeriod: 3600, // 1 jam
        
        // Response time threshold for warnings (ms)
        responseTimeThreshold: 1000,
        
        // Memory usage threshold for warnings (MB)
        memoryThreshold: 500
    },

    // Security Configuration
    security: {
        // Enable CORS
        enableCORS: true,
        
        // CORS origin (use '*' for development)
        corsOrigin: process.env.CORS_ORIGIN || '*',
        
        // Enable Helmet security headers
        enableHelmet: true,
        
        // Disable CSP for WhatsApp Web compatibility
        disableCSP: true,
        
        // API key validation
        requireApiKey: true
    },

    // Compression Configuration
    compression: {
        // Enable compression
        enabled: true,
        
        // Compression level (1-9)
        level: 6,
        
        // Threshold for compression (bytes)
        threshold: 1024,
        
        // Filter function for compression
        filter: (req, res) => {
            // Don't compress responses with this request header
            if (req.headers['x-no-compression']) {
                return false;
            }
            // Use compression for all other responses
            return compression.filter(req, res);
        }
    },

    // Logging Configuration
    logging: {
        // Enable detailed logging
        enabled: true,
        
        // Log level
        level: process.env.LOG_LEVEL || 'info',
        
        // Log performance metrics
        logPerformance: true,
        
        // Log memory usage
        logMemory: true,
        
        // Log session events
        logSessions: true
    },

    // Environment-specific configurations
    environments: {
        development: {
            memory: {
                gcThreshold: 300,
                sessionCleanupTimeout: 60 * 60 * 1000 // 1 jam
            },
            cache: {
                contactTTL: 300, // 5 menit
                groupTTL: 300, // 5 menit
                performanceTTL: 60 // 1 menit
            },
            rateLimit: {
                maxRequests: 1000
            },
            logging: {
                level: 'debug'
            }
        },
        
        production: {
            memory: {
                gcThreshold: 800,
                sessionCleanupTimeout: 24 * 60 * 60 * 1000 // 24 jam
            },
            cache: {
                contactTTL: 3600, // 1 jam
                groupTTL: 3600, // 1 jam
                performanceTTL: 600 // 10 menit
            },
            rateLimit: {
                maxRequests: 50
            },
            logging: {
                level: 'warn'
            }
        },
        
        testing: {
            memory: {
                gcThreshold: 100,
                sessionCleanupTimeout: 10 * 60 * 1000 // 10 menit
            },
            cache: {
                contactTTL: 60, // 1 menit
                groupTTL: 60, // 1 menit
                performanceTTL: 30 // 30 detik
            },
            rateLimit: {
                maxRequests: 10000
            },
            logging: {
                level: 'error'
            }
        }
    },

    // Get configuration based on environment
    getConfig: function() {
        const env = process.env.NODE_ENV || 'development';
        const baseConfig = { ...this };
        const envConfig = this.environments[env] || {};
        
        // Merge configurations
        return this.mergeConfig(baseConfig, envConfig);
    },

    // Merge configuration objects
    mergeConfig: function(base, override) {
        const result = { ...base };
        
        for (const key in override) {
            if (override.hasOwnProperty(key)) {
                if (typeof override[key] === 'object' && !Array.isArray(override[key])) {
                    result[key] = this.mergeConfig(result[key] || {}, override[key]);
                } else {
                    result[key] = override[key];
                }
            }
        }
        
        return result;
    }
}; 