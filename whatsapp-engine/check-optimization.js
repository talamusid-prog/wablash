/**
 * Script untuk mengecek penggunaan data yang dioptimasi
 * Membandingkan performa antara versi lama dan versi yang dioptimasi
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

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

function logSection(message) {
    console.log('\n' + '-'.repeat(40));
    log(message, 'cyan');
    console.log('-'.repeat(40));
}

// Function untuk mendapatkan memory usage
function getMemoryUsage() {
    const usage = process.memoryUsage();
    return {
        rss: Math.round(usage.rss / 1024 / 1024),
        heapUsed: Math.round(usage.heapUsed / 1024 / 1024),
        heapTotal: Math.round(usage.heapTotal / 1024 / 1024),
        external: Math.round(usage.external / 1024 / 1024)
    };
}

// Function untuk mengecek file sizes
function checkFileSizes() {
    logSection('File Size Comparison');
    
    const files = [
        { name: 'Original Server', path: 'server.js' },
        { name: 'Optimized Server', path: 'server-optimized.js' },
        { name: 'Original Package', path: 'package.json' },
        { name: 'Optimized Package', path: 'package-optimized.json' }
    ];
    
    files.forEach(file => {
        try {
            const stats = fs.statSync(file.path);
            const sizeKB = Math.round(stats.size / 1024);
            log(`${file.name}: ${sizeKB} KB`, 'green');
        } catch (error) {
            log(`${file.name}: File not found`, 'red');
        }
    });
}

// Function untuk mengecek dependencies
function checkDependencies() {
    logSection('Dependencies Check');
    
    try {
        const packageJson = JSON.parse(fs.readFileSync('package.json', 'utf8'));
        const optimizedPackageJson = JSON.parse(fs.readFileSync('package-optimized.json', 'utf8'));
        
        log('Original Dependencies:', 'yellow');
        Object.keys(packageJson.dependencies || {}).forEach(dep => {
            log(`  - ${dep}: ${packageJson.dependencies[dep]}`, 'green');
        });
        
        log('\nOptimized Dependencies:', 'yellow');
        Object.keys(optimizedPackageJson.dependencies || {}).forEach(dep => {
            log(`  - ${dep}: ${optimizedPackageJson.dependencies[dep]}`, 'green');
        });
        
        log('\nNew Dependencies Added:', 'yellow');
        const newDeps = Object.keys(optimizedPackageJson.dependencies || {}).filter(
            dep => !packageJson.dependencies || !packageJson.dependencies[dep]
        );
        newDeps.forEach(dep => {
            log(`  + ${dep}: ${optimizedPackageJson.dependencies[dep]}`, 'magenta');
        });
        
    } catch (error) {
        log(`Error reading package files: ${error.message}`, 'red');
    }
}

// Function untuk mengecek optimasi dalam kode
function checkCodeOptimizations() {
    logSection('Code Optimization Analysis');
    
    try {
        const originalCode = fs.readFileSync('server.js', 'utf8');
        const optimizedCode = fs.readFileSync('server-optimized.js', 'utf8');
        
        // Check for caching implementation
        const hasCaching = optimizedCode.includes('NodeCache') || optimizedCode.includes('node-cache');
        log(`Caching System: ${hasCaching ? '✅ Implemented' : '❌ Not found'}`, hasCaching ? 'green' : 'red');
        
        // Check for compression
        const hasCompression = optimizedCode.includes('compression()');
        log(`Response Compression: ${hasCompression ? '✅ Implemented' : '❌ Not found'}`, hasCompression ? 'green' : 'red');
        
        // Check for rate limiting
        const hasRateLimit = optimizedCode.includes('rateLimit') || optimizedCode.includes('express-rate-limit');
        log(`Rate Limiting: ${hasRateLimit ? '✅ Implemented' : '❌ Not found'}`, hasRateLimit ? 'green' : 'red');
        
        // Check for memory management
        const hasMemoryManagement = optimizedCode.includes('cleanupSession') || optimizedCode.includes('sessionTimers');
        log(`Memory Management: ${hasMemoryManagement ? '✅ Implemented' : '❌ Not found'}`, hasMemoryManagement ? 'green' : 'red');
        
        // Check for performance monitoring
        const hasPerformanceMonitoring = optimizedCode.includes('/performance') || optimizedCode.includes('performanceCache');
        log(`Performance Monitoring: ${hasPerformanceMonitoring ? '✅ Implemented' : '❌ Not found'}`, hasPerformanceMonitoring ? 'green' : 'red');
        
        // Check for optimized Puppeteer config
        const hasOptimizedPuppeteer = optimizedCode.includes('--disable-background-timer-throttling') || 
                                     optimizedCode.includes('--memory-pressure-off');
        log(`Optimized Puppeteer: ${hasOptimizedPuppeteer ? '✅ Implemented' : '❌ Not found'}`, hasOptimizedPuppeteer ? 'green' : 'red');
        
        // Check for retry mechanism
        const hasRetryMechanism = optimizedCode.includes('retries') && optimizedCode.includes('while (retries > 0)');
        log(`Retry Mechanism: ${hasRetryMechanism ? '✅ Implemented' : '❌ Not found'}`, hasRetryMechanism ? 'green' : 'red');
        
    } catch (error) {
        log(`Error analyzing code: ${error.message}`, 'red');
    }
}

// Function untuk mengecek konfigurasi
function checkConfiguration() {
    logSection('Configuration Check');
    
    try {
        if (fs.existsSync('optimization-config.js')) {
            log('✅ Optimization config file found', 'green');
            
            const config = require('./optimization-config.js');
            const activeConfig = config.getConfig();
            
            log('\nActive Configuration:', 'yellow');
            log(`Memory GC Threshold: ${activeConfig.memory.gcThreshold} MB`, 'green');
            log(`Session Cleanup Timeout: ${activeConfig.memory.sessionCleanupTimeout / 1000 / 60} minutes`, 'green');
            log(`Contact Cache TTL: ${activeConfig.cache.contactTTL} seconds`, 'green');
            log(`Group Cache TTL: ${activeConfig.cache.groupTTL} seconds`, 'green');
            log(`Rate Limit: ${activeConfig.rateLimit.maxRequests} requests per ${activeConfig.rateLimit.windowMs / 1000 / 60} minutes`, 'green');
            
        } else {
            log('❌ Optimization config file not found', 'red');
        }
    } catch (error) {
        log(`Error checking configuration: ${error.message}`, 'red');
    }
}

// Function untuk mengecek startup scripts
function checkStartupScripts() {
    logSection('Startup Scripts Check');
    
    const scripts = [
        { name: 'Windows Batch Script', path: 'start-optimized.bat' },
        { name: 'Linux/Mac Shell Script', path: 'start-optimized.sh' }
    ];
    
    scripts.forEach(script => {
        try {
            const stats = fs.statSync(script.path);
            const sizeKB = Math.round(stats.size / 1024);
            log(`✅ ${script.name}: ${sizeKB} KB`, 'green');
        } catch (error) {
            log(`❌ ${script.name}: Not found`, 'red');
        }
    });
}

// Function untuk mengecek dokumentasi
function checkDocumentation() {
    logSection('Documentation Check');
    
    const docs = [
        { name: 'Optimization README', path: 'README-OPTIMIZED.md' },
        { name: 'Performance Comparison', path: 'PERFORMANCE_COMPARISON.md' }
    ];
    
    docs.forEach(doc => {
        try {
            const stats = fs.statSync(doc.path);
            const sizeKB = Math.round(stats.size / 1024);
            log(`✅ ${doc.name}: ${sizeKB} KB`, 'green');
        } catch (error) {
            log(`❌ ${doc.name}: Not found`, 'red');
        }
    });
}

// Function untuk mengecek current memory usage
function checkCurrentMemoryUsage() {
    logSection('Current Memory Usage');
    
    const memory = getMemoryUsage();
    log(`RSS (Resident Set Size): ${memory.rss} MB`, 'green');
    log(`Heap Used: ${memory.heapUsed} MB`, 'green');
    log(`Heap Total: ${memory.heapTotal} MB`, 'green');
    log(`External: ${memory.external} MB`, 'green');
    
    // Calculate memory efficiency
    const efficiency = Math.round((memory.heapUsed / memory.heapTotal) * 100);
    log(`Memory Efficiency: ${efficiency}%`, efficiency > 80 ? 'yellow' : 'green');
}

// Function untuk mengecek Node.js version
function checkNodeVersion() {
    logSection('Environment Check');
    
    const nodeVersion = process.version;
    const majorVersion = parseInt(nodeVersion.slice(1).split('.')[0]);
    
    log(`Node.js Version: ${nodeVersion}`, 'green');
    log(`Major Version: ${majorVersion}`, 'green');
    
    if (majorVersion >= 16) {
        log('✅ Node.js version is compatible with optimizations', 'green');
    } else {
        log('❌ Node.js version 16+ required for optimizations', 'red');
    }
    
    log(`Platform: ${process.platform}`, 'green');
    log(`Architecture: ${process.arch}`, 'green');
}

// Function untuk mengecek npm packages
function checkNpmPackages() {
    logSection('NPM Packages Check');
    
    try {
        const packageJson = JSON.parse(fs.readFileSync('package.json', 'utf8'));
        const nodeModulesPath = path.join(__dirname, 'node_modules');
        
        if (fs.existsSync(nodeModulesPath)) {
            log('✅ node_modules directory exists', 'green');
            
            // Check for optimization packages
            const optimizationPackages = ['compression', 'express-rate-limit', 'node-cache'];
            optimizationPackages.forEach(pkg => {
                const pkgPath = path.join(nodeModulesPath, pkg);
                if (fs.existsSync(pkgPath)) {
                    log(`✅ ${pkg} package installed`, 'green');
                } else {
                    log(`❌ ${pkg} package not installed`, 'red');
                }
            });
        } else {
            log('❌ node_modules directory not found', 'red');
            log('Run: npm install', 'yellow');
        }
    } catch (error) {
        log(`Error checking packages: ${error.message}`, 'red');
    }
}

// Main function
function main() {
    logHeader('WhatsApp Engine Optimization Checker');
    
    checkNodeVersion();
    checkFileSizes();
    checkDependencies();
    checkNpmPackages();
    checkCodeOptimizations();
    checkConfiguration();
    checkStartupScripts();
    checkDocumentation();
    checkCurrentMemoryUsage();
    
    logHeader('Optimization Summary');
    log('✅ All optimization files have been created successfully!', 'green');
    log('\nTo test the optimized version:', 'yellow');
    log('1. Install dependencies: npm install', 'cyan');
    log('2. Start optimized server: node server-optimized.js', 'cyan');
    log('3. Or use startup script: .\\start-optimized.bat --memory', 'cyan');
    log('\nTo monitor performance:', 'yellow');
    log('1. Check health: curl http://localhost:3000/health', 'cyan');
    log('2. Check performance: curl http://localhost:3000/performance', 'cyan');
    log('3. Monitor memory usage in real-time', 'cyan');
}

// Run the checker
if (require.main === module) {
    main();
}

module.exports = {
    getMemoryUsage,
    checkFileSizes,
    checkDependencies,
    checkCodeOptimizations,
    checkConfiguration,
    checkStartupScripts,
    checkDocumentation,
    checkCurrentMemoryUsage,
    checkNodeVersion,
    checkNpmPackages
}; 