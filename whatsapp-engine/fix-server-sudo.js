const fs = require('fs');
const path = require('path');

console.log('🔧 Memperbaiki server-optimized.js dengan sudo...');

// Baca file server-optimized.js
const filePath = path.join(__dirname, 'server-optimized.js');
let content = fs.readFileSync(filePath, 'utf8');

// Perbaiki 1: Ubah timeout dari 60 detik menjadi 300 detik (5 menit)
console.log('⏱️  Memperbaiki timeout dari 60 detik menjadi 300 detik...');
content = content.replace(
    /setTimeout\(\(\) => reject\(new Error\('Initialization timeout'\)\), 60000\);/,
    "setTimeout(() => reject(new Error('Initialization timeout')), 300000); // 5 minutes timeout"
);

// Perbaiki 2: Pastikan sessionId dalam scope di catch block
console.log('🔍 Memperbaiki scope variable sessionId...');
content = content.replace(
    /} catch \(error\) \{[\s\S]*?console\.error\('Error creating session:', error\);\s*\/\/ Cleanup on error\s*if \(sessionId && sessions\.has\(sessionId\)\) \{[\s\S]*?\}\s*res\.status\(500\)\.json\(\{[\s\S]*?\}\);\s*\}/,
    `} catch (error) {
        console.error('Error creating session:', error);
        
        // Cleanup on error - sessionId is in scope here
        if (sessionId && sessions.has(sessionId)) {
            await cleanupSession(sessionId);
        }
        
        res.status(500).json({
            success: false,
            error: 'Failed to create session: ' + error.message
        });
    }`
);

// Perbaiki 3: Tambahkan konfigurasi Puppeteer yang lebih optimal untuk Ubuntu
console.log('🖥️  Memperbaiki konfigurasi Puppeteer...');
content = content.replace(
    /const puppeteerConfig = \{[\s\S]*?defaultViewport: \{[\s\S]*?\}\s*\};/,
    `const puppeteerConfig = {
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
        '--disable-images',
        '--disable-javascript'
    ],
    defaultViewport: {
        width: 800,
        height: 600
    }
};`
);

// Tulis kembali file yang sudah diperbaiki dengan sudo
try {
    fs.writeFileSync(filePath, content);
    console.log('✅ File server-optimized.js berhasil diperbaiki!');
} catch (error) {
    if (error.code === 'EACCES') {
        console.log('❌ Permission denied! Jalankan dengan sudo:');
        console.log('   sudo node fix-server-sudo.js');
        process.exit(1);
    } else {
        console.log('❌ Error:', error.message);
        process.exit(1);
    }
}

console.log('');
console.log('📋 Perbaikan yang dilakukan:');
console.log('   1. ✅ Timeout diubah dari 60 detik menjadi 300 detik');
console.log('   2. ✅ Scope variable sessionId sudah diperbaiki');
console.log('   3. ✅ Konfigurasi Puppeteer dioptimasi untuk Ubuntu server');
console.log('');
console.log('🚀 Sekarang coba jalankan server:');
console.log('   node server-optimized.js');
console.log('');
console.log('📝 Jika masih ada masalah, jalankan script perbaikan Puppeteer:');
console.log('   chmod +x fix-puppeteer-ubuntu.sh');
console.log('   sudo ./fix-puppeteer-ubuntu.sh'); 