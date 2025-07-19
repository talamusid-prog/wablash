#!/bin/bash

echo "🚀 Menjalankan WhatsApp Engine dengan konfigurasi yang diperbaiki..."
echo "================================================================"

# Set environment variables
export NODE_ENV=production
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
export PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Set Node.js memory limit
export NODE_OPTIONS="--max-old-space-size=4096"

# Set timeout untuk session creation
export SESSION_TIMEOUT=300000

echo "🔧 Konfigurasi Environment:"
echo "   - NODE_ENV: $NODE_ENV"
echo "   - PUPPETEER_EXECUTABLE_PATH: $PUPPETEER_EXECUTABLE_PATH"
echo "   - NODE_OPTIONS: $NODE_OPTIONS"
echo "   - SESSION_TIMEOUT: $SESSION_TIMEOUT ms"
echo ""

# Check if Chromium is available
if ! command -v chromium-browser &> /dev/null; then
    echo "⚠️  Chromium tidak ditemukan, mencoba Google Chrome..."
    export PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome
    
    if ! command -v google-chrome &> /dev/null; then
        echo "❌ Chrome juga tidak ditemukan. Jalankan script perbaikan Puppeteer terlebih dahulu:"
        echo "   chmod +x fix-puppeteer-ubuntu.sh"
        echo "   sudo ./fix-puppeteer-ubuntu.sh"
        exit 1
    fi
fi

echo "✅ Browser ditemukan: $PUPPETEER_EXECUTABLE_PATH"
echo ""

# Check if server file exists
if [ ! -f "server-optimized.js" ]; then
    echo "❌ File server-optimized.js tidak ditemukan!"
    exit 1
fi

# Check if dependencies are installed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

echo "🚀 Menjalankan server..."
echo "   Server akan berjalan di: http://localhost:3000"
echo "   Health check: http://localhost:3000/health"
echo "   Performance stats: http://localhost:3000/performance"
echo ""
echo "📋 Logs akan muncul di bawah ini:"
echo "================================================================"

# Jalankan server dengan error handling
node server-optimized.js

# Jika server crash, tampilkan error
if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Server crash! Coba periksa:"
    echo "   1. Apakah Puppeteer dependencies sudah terinstall?"
    echo "   2. Apakah browser (Chrome/Chromium) tersedia?"
    echo "   3. Apakah port 3000 sudah digunakan?"
    echo ""
    echo "🔧 Troubleshooting:"
    echo "   - Jalankan: chmod +x fix-puppeteer-ubuntu.sh && sudo ./fix-puppeteer-ubuntu.sh"
    echo "   - Cek port: netstat -tlnp | grep :3000"
    echo "   - Kill process: sudo pkill -f 'node server-optimized.js'"
fi 