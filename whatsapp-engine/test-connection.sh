#!/bin/bash

echo "🔍 Testing WhatsApp Engine Connection..."
echo "======================================="

SERVER_URL="http://localhost:3000"

echo "📡 Testing basic connectivity..."

# Test ping endpoint
echo "1️⃣ Testing ping endpoint..."
if curl -s --max-time 10 "$SERVER_URL/ping" > /dev/null; then
    echo "✅ Ping endpoint accessible"
else
    echo "❌ Ping endpoint not accessible"
    echo "   Server mungkin tidak berjalan atau ada masalah koneksi"
    exit 1
fi

# Test health endpoint
echo "2️⃣ Testing health endpoint..."
if curl -s --max-time 10 "$SERVER_URL/health" > /dev/null; then
    echo "✅ Health endpoint accessible"
else
    echo "❌ Health endpoint not accessible"
fi

# Test test endpoint
echo "3️⃣ Testing test endpoint..."
if curl -s --max-time 10 "$SERVER_URL/test" > /dev/null; then
    echo "✅ Test endpoint accessible"
else
    echo "❌ Test endpoint not accessible"
fi

# Test session creation endpoint (without auth)
echo "4️⃣ Testing session creation endpoint..."
RESPONSE=$(curl -s --max-time 30 -X POST "$SERVER_URL/sessions/create" \
    -H "Content-Type: application/json" \
    -d '{"sessionId": "test-connection", "phoneNumber": "1234567890"}')

if echo "$RESPONSE" | grep -q "Unauthorized"; then
    echo "✅ Session endpoint accessible (auth required as expected)"
elif echo "$RESPONSE" | grep -q "success"; then
    echo "✅ Session endpoint accessible and working"
else
    echo "❌ Session endpoint not accessible or error occurred"
    echo "   Response: $RESPONSE"
fi

echo ""
echo "🎯 Connection test completed!"
echo "============================"
echo ""
echo "📋 Jika semua test berhasil, server siap digunakan."
echo "📋 Jika ada error, pastikan:"
echo "   1. Server berjalan: ./start-server.sh"
echo "   2. Port 3000 tidak diblokir"
echo "   3. Firewall mengizinkan koneksi"
echo ""
echo "🚀 Untuk menjalankan server:"
echo "   chmod +x start-server.sh"
echo "   ./start-server.sh" 