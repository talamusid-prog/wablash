#!/bin/bash

echo "🔍 Testing WhatsApp Engine Connection..."
echo "======================================="

# Ambil URL dari .env file
if [ -f ".env" ]; then
    ENGINE_URL=$(grep "WHATSAPP_ENGINE_URL" .env | cut -d'=' -f2)
else
    ENGINE_URL="http://localhost:3000"
fi

echo "📡 Testing connection to: $ENGINE_URL"
echo ""

# Test 1: Ping endpoint
echo "1️⃣ Testing ping endpoint..."
PING_RESPONSE=$(curl -s --max-time 10 "$ENGINE_URL/ping")
if [ $? -eq 0 ] && echo "$PING_RESPONSE" | grep -q "success"; then
    echo "✅ Ping endpoint working"
    echo "   Response: $PING_RESPONSE"
else
    echo "❌ Ping endpoint failed"
    echo "   Response: $PING_RESPONSE"
    echo "   Error: Server tidak dapat diakses"
fi

echo ""

# Test 2: Health endpoint
echo "2️⃣ Testing health endpoint..."
HEALTH_RESPONSE=$(curl -s --max-time 10 "$ENGINE_URL/health")
if [ $? -eq 0 ] && echo "$HEALTH_RESPONSE" | grep -q "success"; then
    echo "✅ Health endpoint working"
    echo "   Server uptime: $(echo "$HEALTH_RESPONSE" | grep -o '"uptime":"[^"]*"' | cut -d'"' -f4)"
    echo "   Active sessions: $(echo "$HEALTH_RESPONSE" | grep -o '"activeSessions":[0-9]*' | cut -d':' -f2)"
else
    echo "❌ Health endpoint failed"
    echo "   Response: $HEALTH_RESPONSE"
fi

echo ""

# Test 3: Session creation endpoint (without auth - should return Unauthorized)
echo "3️⃣ Testing session creation endpoint..."
SESSION_RESPONSE=$(curl -s --max-time 30 -X POST "$ENGINE_URL/sessions/create" \
    -H "Content-Type: application/json" \
    -d '{"sessionId": "test-connection", "phoneNumber": "1234567890"}')

if [ $? -eq 0 ]; then
    if echo "$SESSION_RESPONSE" | grep -q "Unauthorized"; then
        echo "✅ Session endpoint working (auth required as expected)"
        echo "   Response: $SESSION_RESPONSE"
    elif echo "$SESSION_RESPONSE" | grep -q "success"; then
        echo "✅ Session endpoint working (no auth required)"
        echo "   Response: $SESSION_RESPONSE"
    else
        echo "⚠️  Session endpoint responded but unexpected response"
        echo "   Response: $SESSION_RESPONSE"
    fi
else
    echo "❌ Session endpoint failed (timeout or connection error)"
    echo "   Response: $SESSION_RESPONSE"
fi

echo ""
echo "🎯 Connection Test Results:"
echo "=========================="

# Summary
if curl -s --max-time 5 "$ENGINE_URL/ping" > /dev/null; then
    echo "✅ WhatsApp Engine is accessible"
    echo "✅ Configuration is correct"
    echo ""
    echo "🚀 Next steps:"
    echo "   1. Try creating session from frontend"
    echo "   2. Check logs if there are any issues"
else
    echo "❌ WhatsApp Engine is not accessible"
    echo ""
    echo "🔧 Troubleshooting:"
    echo "   1. Check if server is running: ps aux | grep node"
    echo "   2. Check URL in .env: grep WHATSAPP_ENGINE_URL .env"
    echo "   3. Test local connection: curl http://localhost:3000/ping"
    echo "   4. Check firewall settings"
fi

echo ""
echo "📊 Server Status:"
echo "   - Engine URL: $ENGINE_URL"
echo "   - Local port 3000: $(netstat -tlnp | grep :3000 | wc -l) processes"
echo "   - Node processes: $(ps aux | grep 'node server-optimized.js' | grep -v grep | wc -l)" 