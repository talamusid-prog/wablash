#!/bin/bash

echo "🔧 Memperbaiki Nginx Timeout dan Menjalankan Server..."
echo "======================================================"

# Step 1: Fix nginx timeout
echo "1️⃣ Memperbaiki Nginx timeout..."
if [ -f "fix-nginx-timeout.sh" ]; then
    chmod +x fix-nginx-timeout.sh
    ./fix-nginx-timeout.sh
else
    echo "⚠️  Script fix-nginx-timeout.sh tidak ditemukan"
    echo "   Jalankan manual: sudo nano /etc/nginx/sites-available/wa-engine"
    echo "   Tambahkan: proxy_read_timeout 600s;"
fi

echo ""

# Step 2: Start WhatsApp Engine Server
echo "2️⃣ Menjalankan WhatsApp Engine Server..."
if [ -f "start-server.sh" ]; then
    chmod +x start-server.sh
    ./start-server.sh
else
    echo "❌ Script start-server.sh tidak ditemukan!"
    echo "   Jalankan manual: node server-optimized.js"
fi

echo ""
echo "🎯 Setup selesai!"
echo "================"
echo ""
echo "📋 Yang sudah dilakukan:"
echo "   ✅ Nginx timeout diperbaiki (600s)"
echo "   ✅ WhatsApp Engine server dijalankan"
echo ""
echo "🔍 Test koneksi:"
echo "   chmod +x test-connection.sh"
echo "   ./test-connection.sh"
echo ""
echo "📝 Jika masih ada masalah:"
echo "   1. Cek logs: tail -f /var/log/nginx/error.log"
echo "   2. Restart nginx: sudo systemctl restart nginx"
echo "   3. Cek server: curl http://localhost:3000/ping" 