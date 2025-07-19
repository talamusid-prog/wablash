/**
 * Real-time Performance Monitor untuk WhatsApp Engine
 * Script untuk memonitor penggunaan memory, CPU, dan performa secara real-time
 */

const http = require('http');
const https = require('https');
const fs = require('fs');

// Colors untuk output
const colors = {
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    magenta: '\x1b[35m',
    cyan: '\x1b[36m',
    reset: '\x1b[0m'
};

function log(message, color = 'reset') {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

function logHeader(message) {
    console.log('\n' + '='.repeat(60));
    log(message, 'blue');
    console.log('='.repeat(60));
}

// Configuration
const config = {
    serverUrl: 'http://localhost:3000',
    apiKey: process.env.API_KEY || 'your_api_key',
    interval: 5000, // 5 seconds
    maxHistory: 20 // Keep last 20 measurements
};

// Performance history
let performanceHistory = [];
let memoryHistory = [];

// Function untuk mendapatkan memory usage
function getMemoryUsage() {
    const usage = process.memoryUsage();
    return {
        rss: Math.round(usage.rss / 1024 / 1024),
        heapUsed: Math.round(usage.heapUsed / 1024 / 1024),
        heapTotal: Math.round(usage.heapTotal / 1024 / 1024),
        external: Math.round(usage.external / 1024 / 1024),
        timestamp: new Date()
    };
}

// Function untuk membuat HTTP request
function makeRequest(url, options = {}) {
    return new Promise((resolve, reject) => {
        const urlObj = new URL(url);
        const client = urlObj.protocol === 'https:' ? https : http;
        
        const requestOptions = {
            hostname: urlObj.hostname,
            port: urlObj.port || (urlObj.protocol === 'https:' ? 443 : 80),
            path: urlObj.pathname + urlObj.search,
            method: options.method || 'GET',
            headers: {
                'User-Agent': 'Performance-Monitor/1.0',
                'X-API-Key': config.apiKey,
                ...options.headers
            }
        };

        const req = client.request(requestOptions, (res) => {
            let data = '';
            res.on('data', (chunk) => {
                data += chunk;
            });
            res.on('end', () => {
                try {
                    const jsonData = JSON.parse(data);
                    resolve({
                        statusCode: res.statusCode,
                        data: jsonData,
                        responseTime: Date.now() - startTime
                    });
                } catch (error) {
                    resolve({
                        statusCode: res.statusCode,
                        data: data,
                        responseTime: Date.now() - startTime
                    });
                }
            });
        });

        req.on('error', (error) => {
            reject(error);
        });

        const startTime = Date.now();
        req.end();
    });
}

// Function untuk mengecek server health
async function checkServerHealth() {
    try {
        const response = await makeRequest(`${config.serverUrl}/health`);
        return response;
    } catch (error) {
        return { error: error.message };
    }
}

// Function untuk mengecek performance metrics
async function checkPerformance() {
    try {
        const response = await makeRequest(`${config.serverUrl}/performance`);
        return response;
    } catch (error) {
        return { error: error.message };
    }
}

// Function untuk mengecek sessions
async function checkSessions() {
    try {
        const response = await makeRequest(`${config.serverUrl}/sessions`);
        return response;
    } catch (error) {
        return { error: error.message };
    }
}

// Function untuk menampilkan memory chart
function displayMemoryChart() {
    if (memoryHistory.length < 2) return;
    
    log('\nMemory Usage Trend:', 'cyan');
    
    const maxMemory = Math.max(...memoryHistory.map(m => m.heapUsed));
    const minMemory = Math.min(...memoryHistory.map(m => m.heapUsed));
    const avgMemory = Math.round(memoryHistory.reduce((sum, m) => sum + m.heapUsed, 0) / memoryHistory.length);
    
    log(`Max: ${maxMemory} MB | Min: ${minMemory} MB | Avg: ${avgMemory} MB`, 'yellow');
    
    // Simple ASCII chart
    const chartWidth = 50;
    const currentMemory = memoryHistory[memoryHistory.length - 1].heapUsed;
    const percentage = Math.round((currentMemory / maxMemory) * chartWidth);
    
    const chart = '█'.repeat(percentage) + '░'.repeat(chartWidth - percentage);
    log(`[${chart}] ${currentMemory} MB`, 'green');
}

// Function untuk menampilkan performance metrics
function displayPerformanceMetrics(performanceData) {
    if (!performanceData || performanceData.error) {
        log('❌ Cannot fetch performance data', 'red');
        return;
    }
    
    const data = performanceData.data;
    
    log('\nPerformance Metrics:', 'cyan');
    log(`Response Time: ${performanceData.responseTime}ms`, 'green');
    
    if (data.memoryUsage) {
        log(`Memory Usage:`, 'yellow');
        log(`  RSS: ${data.memoryUsage.rss}`, 'green');
        log(`  Heap Used: ${data.memoryUsage.heapUsed}`, 'green');
        log(`  Heap Total: ${data.memoryUsage.heapTotal}`, 'green');
        log(`  External: ${data.memoryUsage.external}`, 'green');
    }
    
    if (data.performance) {
        log(`Active Sessions: ${data.performance.activeSessions}`, 'green');
        log(`Cache Hit Rate: ${data.performance.cacheHitRate || 'N/A'}%`, 'green');
        log(`Average Response Time: ${data.performance.averageResponseTime || 'N/A'}ms`, 'green');
    }
}

// Function untuk menampilkan session info
function displaySessionInfo(sessionData) {
    if (!sessionData || sessionData.error) {
        log('❌ Cannot fetch session data', 'red');
        return;
    }
    
    const data = sessionData.data;
    
    log('\nSession Information:', 'cyan');
    log(`Total Sessions: ${data.length}`, 'green');
    
    if (data.length > 0) {
        const statusCounts = {};
        data.forEach(session => {
            statusCounts[session.status] = (statusCounts[session.status] || 0) + 1;
        });
        
        Object.entries(statusCounts).forEach(([status, count]) => {
            const color = status === 'connected' ? 'green' : 
                         status === 'connecting' ? 'yellow' : 'red';
            log(`  ${status}: ${count}`, color);
        });
    }
    
    if (sessionData.data.performance) {
        log(`Memory Usage: ${sessionData.data.performance.memoryUsage.heapUsed} MB`, 'green');
    }
}

// Function untuk menampilkan optimization status
function displayOptimizationStatus() {
    log('\nOptimization Status:', 'cyan');
    
    // Check if optimized server is running
    const isOptimized = process.argv.includes('--optimized') || 
                       process.argv.includes('-o');
    
    if (isOptimized) {
        log('✅ Running optimized version', 'green');
    } else {
        log('⚠️  Running standard version', 'yellow');
    }
    
    // Check for optimization packages
    const optimizationPackages = ['compression', 'express-rate-limit', 'node-cache'];
    optimizationPackages.forEach(pkg => {
        try {
            require(pkg);
            log(`✅ ${pkg} package loaded`, 'green');
        } catch (error) {
            log(`❌ ${pkg} package not found`, 'red');
        }
    });
}

// Function untuk menampilkan real-time monitoring
function displayRealTimeMonitoring() {
    const currentMemory = getMemoryUsage();
    memoryHistory.push(currentMemory);
    
    // Keep only last N measurements
    if (memoryHistory.length > config.maxHistory) {
        memoryHistory.shift();
    }
    
    // Clear console (works on most terminals)
    console.clear();
    
    logHeader('WhatsApp Engine Performance Monitor');
    log(`Monitoring: ${config.serverUrl}`, 'yellow');
    log(`Update Interval: ${config.interval / 1000} seconds`, 'yellow');
    log(`Timestamp: ${new Date().toLocaleString()}`, 'yellow');
    
    displayOptimizationStatus();
    displayMemoryChart();
}

// Function untuk menampilkan alerts
function displayAlerts(performanceData, sessionData) {
    const alerts = [];
    
    // Memory usage alerts
    const currentMemory = memoryHistory[memoryHistory.length - 1];
    if (currentMemory.heapUsed > 500) {
        alerts.push(`⚠️  High memory usage: ${currentMemory.heapUsed} MB`);
    }
    
    // Response time alerts
    if (performanceData && performanceData.responseTime > 1000) {
        alerts.push(`⚠️  Slow response time: ${performanceData.responseTime}ms`);
    }
    
    // Session alerts
    if (sessionData && sessionData.data && sessionData.data.length > 15) {
        alerts.push(`⚠️  High number of sessions: ${sessionData.data.length}`);
    }
    
    if (alerts.length > 0) {
        log('\nAlerts:', 'red');
        alerts.forEach(alert => log(`  ${alert}`, 'red'));
    }
}

// Main monitoring function
async function startMonitoring() {
    logHeader('Starting Performance Monitor');
    log('Press Ctrl+C to stop monitoring', 'yellow');
    
    let iteration = 0;
    
    const monitorInterval = setInterval(async () => {
        iteration++;
        
        try {
            // Get current memory usage
            displayRealTimeMonitoring();
            
            // Check server health
            const healthData = await checkServerHealth();
            if (healthData.error) {
                log(`❌ Server not responding: ${healthData.error}`, 'red');
                return;
            }
            
            // Check performance metrics
            const performanceData = await checkPerformance();
            displayPerformanceMetrics(performanceData);
            
            // Check sessions
            const sessionData = await checkSessions();
            displaySessionInfo(sessionData);
            
            // Display alerts
            displayAlerts(performanceData, sessionData);
            
            // Add to performance history
            if (performanceData && !performanceData.error) {
                performanceHistory.push({
                    timestamp: new Date(),
                    responseTime: performanceData.responseTime,
                    memoryUsage: performanceData.data.memoryUsage
                });
                
                if (performanceHistory.length > config.maxHistory) {
                    performanceHistory.shift();
                }
            }
            
        } catch (error) {
            log(`❌ Monitoring error: ${error.message}`, 'red');
        }
        
        // Display iteration count
        log(`\nIteration: ${iteration} | Next update in ${config.interval / 1000}s`, 'cyan');
        
    }, config.interval);
    
    // Handle graceful shutdown
    process.on('SIGINT', () => {
        clearInterval(monitorInterval);
        logHeader('Monitoring Stopped');
        log('Final Statistics:', 'yellow');
        
        if (performanceHistory.length > 0) {
            const avgResponseTime = Math.round(
                performanceHistory.reduce((sum, p) => sum + p.responseTime, 0) / performanceHistory.length
            );
            log(`Average Response Time: ${avgResponseTime}ms`, 'green');
        }
        
        if (memoryHistory.length > 0) {
            const avgMemory = Math.round(
                memoryHistory.reduce((sum, m) => sum + m.heapUsed, 0) / memoryHistory.length
            );
            log(`Average Memory Usage: ${avgMemory} MB`, 'green');
        }
        
        process.exit(0);
    });
}

// Command line interface
function showHelp() {
    logHeader('Performance Monitor Help');
    log('Usage: node monitor-performance.js [options]', 'yellow');
    log('\nOptions:', 'cyan');
    log('  --url <url>        Server URL (default: http://localhost:3000)', 'green');
    log('  --interval <ms>    Update interval in milliseconds (default: 5000)', 'green');
    log('  --api-key <key>    API key for authentication', 'green');
    log('  --optimized        Indicate running optimized version', 'green');
    log('  --help             Show this help message', 'green');
    log('\nExamples:', 'cyan');
    log('  node monitor-performance.js', 'green');
    log('  node monitor-performance.js --url http://localhost:3001', 'green');
    log('  node monitor-performance.js --interval 3000 --optimized', 'green');
}

// Parse command line arguments
function parseArguments() {
    const args = process.argv.slice(2);
    
    for (let i = 0; i < args.length; i++) {
        switch (args[i]) {
            case '--help':
            case '-h':
                showHelp();
                process.exit(0);
                break;
            case '--url':
                config.serverUrl = args[++i];
                break;
            case '--interval':
                config.interval = parseInt(args[++i]);
                break;
            case '--api-key':
                config.apiKey = args[++i];
                break;
            case '--optimized':
            case '-o':
                // Flag is handled in displayOptimizationStatus
                break;
        }
    }
}

// Start the monitor
if (require.main === module) {
    parseArguments();
    startMonitoring();
}

module.exports = {
    getMemoryUsage,
    checkServerHealth,
    checkPerformance,
    checkSessions,
    startMonitoring
}; 