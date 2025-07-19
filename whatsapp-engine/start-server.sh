#!/bin/bash

echo "🚀 Menjalankan WhatsApp Engine Server..."
echo "========================================"

# Set environment variables
export NODE_ENV=production
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
export PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
export NODE_OPTIONS="--max-old-space-size=4096"

echo "🔧 Environment Variables:"
echo "   NODE_ENV: $NODE_ENV"
echo "   PUPPETEER_EXECUTABLE_PATH: $PUPPETEER_EXECUTABLE_PATH"
echo "   NODE_OPTIONS: $NODE_OPTIONS"
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

# Check if Chromium is available
if ! command -v chromium-browser &> /dev/null; then
    echo "⚠️  Chromium tidak ditemukan, mencoba Google Chrome..."
    export PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome
    
    if ! command -v google-chrome &> /dev/null; then
        echo "❌ Chrome juga tidak ditemukan. Install Chromium:"
        echo "   sudo apt update && sudo apt install chromium-browser"
        exit 1
    fi
fi

echo "✅ Browser ditemukan: $PUPPETEER_EXECUTABLE_PATH"
echo ""

# Kill any existing node processes
echo "🔄 Stopping existing processes..."
pkill -f "node server-optimized.js" || true
sleep 2

echo "🚀 Starting WhatsApp Engine Server..."
echo "   Server akan berjalan di: http://localhost:3000"
echo "   Health check: http://localhost:3000/health"
echo "   Ping test: http://localhost:3000/ping"
echo "   Test endpoint: http://localhost:3000/test"
echo ""
echo "📋 Logs akan muncul di bawah ini:"
echo "========================================"

# Jalankan server
node server-optimized.js 