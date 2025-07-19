# 🚀 Panduan Implementasi WhatsApp Engine

## 📋 Daftar Isi
1. [Perbaikan Otomatis](#perbaikan-otomatis)
2. [Perbaikan Manual](#perbaikan-manual)
3. [Troubleshooting](#troubleshooting)
4. [Testing](#testing)

## 🔧 Perbaikan Otomatis

### Langkah 1: Jalankan Script Perbaikan Lengkap

```bash
# Beri permission execute
chmod +x run-all-fixes.sh

# Jalankan semua perbaikan
./run-all-fixes.sh
```

Script ini akan:
- ✅ Memperbaiki timeout dari 60 detik menjadi 300 detik
- ✅ Memperbaiki scope variable sessionId
- ✅ Mengoptimasi konfigurasi Puppeteer untuk Ubuntu
- ✅ Install dependencies yang diperlukan
- ✅ Menyiapkan startup script

### Langkah 2: Jalankan Server

```bash
# Jalankan dengan script yang sudah diperbaiki
./start-server-fixed.sh
```

## 🔧 Perbaikan Manual

### 1. Perbaiki Timeout dan Scope Variable

```bash
# Jalankan script perbaikan server
node fix-server.js
```

### 2. Perbaiki Puppeteer Dependencies

```bash
# Jalankan script perbaikan Puppeteer
chmod +x fix-puppeteer-ubuntu.sh
sudo ./fix-puppeteer-ubuntu.sh
```

### 3. Install Dependencies

```bash
# Install dependencies
npm install

# Install dependencies tambahan untuk optimasi
npm install compression express-rate-limit node-cache
```

### 4. Jalankan Server

```bash
# Set environment variables
export NODE_ENV=production
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
export PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
export NODE_OPTIONS="--max-old-space-size=4096"

# Jalankan server
node server-optimized.js
```

## 🔍 Troubleshooting

### Error: Initialization timeout

**Penyebab**: Timeout terlalu pendek (60 detik)
**Solusi**: Sudah diperbaiki menjadi 300 detik (5 menit)

### Error: sessionId is not defined

**Penyebab**: Variable scope di catch block
**Solusi**: Sudah diperbaiki dengan pengecekan sessionId

### Error: Failed to launch browser

**Penyebab**: Dependencies Puppeteer tidak lengkap
**Solusi**: Jalankan `sudo ./fix-puppeteer-ubuntu.sh`

### Error: 504 Gateway Timeout

**Penyebab**: Nginx timeout terlalu pendek
**Solusi**: Update konfigurasi nginx:

```nginx
# Tambahkan di /etc/nginx/sites-available/wa-engine
proxy_connect_timeout 600s;
proxy_send_timeout 600s;
proxy_read_timeout 600s;
send_timeout 600s;
```

### Error: Memory issues

**Penyebab**: Memory usage terlalu tinggi
**Solusi**: 
```bash
# Set memory limit
export NODE_OPTIONS="--max-old-space-size=4096"

# Atau jalankan dengan garbage collection
node --expose-gc server-optimized.js
```

## 🧪 Testing

### 1. Test Health Endpoint

```bash
curl http://localhost:3000/health
```

**Expected Response:**
```json
{
  "success": true,
  "message": "WhatsApp Engine is running",
  "timestamp": "2024-01-01T00:00:00.000Z",
  "performance": {
    "memoryUsage": {
      "rss": "150 MB",
      "heapUsed": "80 MB",
      "heapTotal": "120 MB"
    },
    "uptime": "60 seconds",
    "activeSessions": 0
  }
}
```

### 2. Test Performance Endpoint

```bash
curl http://localhost:3000/performance
```

### 3. Test Session Creation

```bash
curl -X POST http://localhost:3000/sessions/create \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your_api_key" \
  -d '{"sessionId": "test-session", "phoneNumber": "1234567890"}' \
  --max-time 400
```

### 4. Test Message Sending

```bash
# Setelah session terhubung
curl -X POST http://localhost:3000/sessions/test-session/send \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your_api_key" \
  -d '{"to": "1234567890", "message": "Test message", "type": "text"}'
```

## 📊 Monitoring

### 1. Real-time Monitoring

```bash
# Monitor server logs
tail -f /var/log/wa-engine.log

# Monitor memory usage
watch -n 5 'ps aux | grep node'

# Monitor port usage
netstat -tlnp | grep :3000
```

### 2. Performance Monitoring

```bash
# Jalankan script monitoring
node monitor-performance.js
```

### 3. Memory Monitoring

```bash
# Cek memory usage
node -e "console.log(process.memoryUsage())"

# Force garbage collection
node -e "if (global.gc) { global.gc(); console.log('GC executed'); }"
```

## 🚀 Production Deployment

### 1. Systemd Service

Buat file `/etc/systemd/system/wa-engine.service`:

```ini
[Unit]
Description=WhatsApp Engine
After=network.target

[Service]
Type=simple
User=ubuntu
WorkingDirectory=/var/www/wa_juaraapps_usr/data/www/wa.juaraapps.my.id/whatsapp-engine
Environment=NODE_ENV=production
Environment=PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
Environment=PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
Environment=NODE_OPTIONS=--max-old-space-size=4096
ExecStart=/usr/bin/node server-optimized.js
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

### 2. Enable Service

```bash
# Reload systemd
sudo systemctl daemon-reload

# Enable service
sudo systemctl enable wa-engine

# Start service
sudo systemctl start wa-engine

# Check status
sudo systemctl status wa-engine
```

### 3. Log Management

```bash
# View logs
sudo journalctl -u wa-engine -f

# View recent logs
sudo journalctl -u wa-engine --since "1 hour ago"
```

## 📝 Checklist Implementasi

- [ ] ✅ Script perbaikan otomatis dijalankan
- [ ] ✅ Timeout diperbaiki (60s → 300s)
- [ ] ✅ Scope variable sessionId diperbaiki
- [ ] ✅ Puppeteer dependencies terinstall
- [ ] ✅ Server bisa dijalankan
- [ ] ✅ Health endpoint berfungsi
- [ ] ✅ Session creation berfungsi
- [ ] ✅ Message sending berfungsi
- [ ] ✅ Nginx timeout dikonfigurasi
- [ ] ✅ Systemd service dibuat (opsional)
- [ ] ✅ Monitoring diatur

## 🎯 Hasil yang Diharapkan

Setelah implementasi, server akan:
- ✅ Tidak ada error timeout
- ✅ Tidak ada error scope variable
- ✅ Bisa membuat session WhatsApp
- ✅ Bisa mengirim pesan
- ✅ Memory usage optimal
- ✅ Response time cepat
- ✅ Stable di production

## 📞 Support

Jika masih ada masalah:
1. Cek logs: `tail -f /var/log/wa-engine.log`
2. Restart service: `sudo systemctl restart wa-engine`
3. Jalankan troubleshooting script: `./run-all-fixes.sh` 