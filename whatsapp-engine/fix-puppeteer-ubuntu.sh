#!/bin/bash

echo "🔧 Memperbaiki masalah Puppeteer di Ubuntu..."
echo "=============================================="

# Update package list
echo "📦 Mengupdate package list..."
sudo apt update

# Install required dependencies for Puppeteer
echo "📥 Menginstall dependencies Puppeteer..."
sudo apt install -y \
    gconf-service \
    libasound2 \
    libatk1.0-0 \
    libc6 \
    libcairo2 \
    libcups2 \
    libdbus-1-3 \
    libexpat1 \
    libfontconfig1 \
    libgcc1 \
    libgconf-2-4 \
    libgdk-pixbuf2.0-0 \
    libglib2.0-0 \
    libgtk-3-0 \
    libnspr4 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libstdc++6 \
    libx11-6 \
    libx11-xcb1 \
    libxcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxext6 \
    libxfixes3 \
    libxi6 \
    libxrandr2 \
    libxrender1 \
    libxss1 \
    libxtst6 \
    ca-certificates \
    fonts-liberation \
    libappindicator1 \
    libnss3 \
    lsb-release \
    xdg-utils \
    wget

# Install additional fonts
echo "🔤 Menginstall fonts tambahan..."
sudo apt install -y \
    fonts-noto-color-emoji \
    fonts-noto-cjk \
    fonts-noto-cjk-extra

# Set environment variables for Puppeteer
echo "🔧 Mengatur environment variables..."
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
export PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome

# Install Google Chrome if not present
if ! command -v google-chrome &> /dev/null; then
    echo "🌐 Menginstall Google Chrome..."
    wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
    echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" | sudo tee /etc/apt/sources.list.d/google-chrome.list
    sudo apt update
    sudo apt install -y google-chrome-stable
fi

# Alternative: Use Chromium
if ! command -v google-chrome &> /dev/null; then
    echo "🌐 Menginstall Chromium sebagai alternatif..."
    sudo apt install -y chromium-browser
    export PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
fi

# Create .env file with Puppeteer settings
echo "📝 Membuat file .env untuk konfigurasi Puppeteer..."
cat > .env << EOF
# Puppeteer Configuration
PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome

# Alternative if Google Chrome not available
# PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Additional Puppeteer args for server environment
PUPPETEER_ARGS=--no-sandbox,--disable-setuid-sandbox,--disable-dev-shm-usage,--disable-accelerated-2d-canvas,--no-first-run,--no-zygote,--disable-gpu
EOF

echo "✅ Selesai! Masalah Puppeteer sudah diperbaiki."
echo ""
echo "🚀 Sekarang coba jalankan server lagi:"
echo "   node server-optimized.js"
echo ""
echo "📋 Jika masih ada masalah, coba:"
echo "   1. Restart server: sudo systemctl restart your-service"
echo "   2. Atau jalankan dengan: PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser node server-optimized.js" 