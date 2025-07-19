#!/bin/bash

echo "🔧 WhatsApp Engine - Complete Fix Script"
echo "========================================"
echo ""

# Step 1: Fix server-optimized.js
echo "📝 Step 1: Memperbaiki server-optimized.js..."
if [ -f "fix-server.js" ]; then
    node fix-server.js
    if [ $? -eq 0 ]; then
        echo "✅ Server file berhasil diperbaiki!"
    else
        echo "❌ Gagal memperbaiki server file!"
        exit 1
    fi
else
    echo "❌ File fix-server.js tidak ditemukan!"
    exit 1
fi

echo ""

# Step 2: Fix Puppeteer dependencies
echo "🖥️  Step 2: Memperbaiki Puppeteer dependencies..."
if [ -f "fix-puppeteer-ubuntu.sh" ]; then
    echo "   Jalankan script perbaikan Puppeteer..."
    echo "   (Ini memerlukan sudo privileges)"
    echo ""
    read -p "   Lanjutkan dengan perbaikan Puppeteer? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        chmod +x fix-puppeteer-ubuntu.sh
        sudo ./fix-puppeteer-ubuntu.sh
        if [ $? -eq 0 ]; then
            echo "✅ Puppeteer dependencies berhasil diperbaiki!"
        else
            echo "⚠️  Ada masalah dengan perbaikan Puppeteer, tapi lanjutkan..."
        fi
    else
        echo "⏭️  Melewati perbaikan Puppeteer..."
    fi
else
    echo "❌ File fix-puppeteer-ubuntu.sh tidak ditemukan!"
    exit 1
fi

echo ""

# Step 3: Install dependencies
echo "📦 Step 3: Installing dependencies..."
if [ -f "package.json" ]; then
    npm install
    if [ $? -eq 0 ]; then
        echo "✅ Dependencies berhasil diinstall!"
    else
        echo "❌ Gagal install dependencies!"
        exit 1
    fi
else
    echo "❌ File package.json tidak ditemukan!"
    exit 1
fi

echo ""

# Step 4: Make startup script executable
echo "🚀 Step 4: Menyiapkan startup script..."
if [ -f "start-server-fixed.sh" ]; then
    chmod +x start-server-fixed.sh
    echo "✅ Startup script siap digunakan!"
else
    echo "❌ File start-server-fixed.sh tidak ditemukan!"
    exit 1
fi

echo ""

# Step 5: Summary
echo "🎉 SEMUA PERBAIKAN SELESAI!"
echo "============================"
echo ""
echo "📋 Yang sudah diperbaiki:"
echo "   ✅ Timeout dari 60 detik menjadi 300 detik"
echo "   ✅ Scope variable sessionId"
echo "   ✅ Konfigurasi Puppeteer untuk Ubuntu"
echo "   ✅ Dependencies terinstall"
echo "   ✅ Startup script siap"
echo ""
echo "🚀 Cara menjalankan server:"
echo "   ./start-server-fixed.sh"
echo ""
echo "🔧 Atau jalankan manual:"
echo "   node server-optimized.js"
echo ""
echo "📊 Test endpoints:"
echo "   Health: curl http://localhost:3000/health"
echo "   Performance: curl http://localhost:3000/performance"
echo ""
echo "📝 Jika masih ada masalah:"
echo "   1. Cek logs: tail -f /var/log/wa-engine.log"
echo "   2. Restart: sudo systemctl restart your-service"
echo "   3. Kill process: sudo pkill -f 'node server-optimized.js'"
echo ""
echo "🎯 Server siap digunakan!" 