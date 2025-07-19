# 🔧 Panduan Troubleshooting WhatsApp Engine

## 🚨 Masalah: cURL error 28: Operation timeout

### Penyebab
- Server WhatsApp Engine tidak berjalan
- Nginx timeout terlalu pendek
- Firewall memblokir port 3000
- Server tidak responsive

### Solusi Langkah demi Langkah

#### 1. Jalankan Server WhatsApp Engine

```bash
# Beri permission execute
chmod +x start-server.sh

# Jalankan server
./start-server.sh
```

**Expected Output:**
```
🚀 Menjalankan WhatsApp Engine Server...
========================================
🔧 Environment Variables:
   NODE_ENV: production
   PUPPETEER_EXECUTABLE_PATH: /usr/bin/chromium-browser
   NODE_OPTIONS: --max-old-space-size=4096

✅ Browser ditemukan: /usr/bin/chromium-browser

🚀 Starting WhatsApp Engine Server...
   Server akan berjalan di: http://localhost:3000
```

#### 2. Test Koneksi Server

```bash
# Beri permission execute
chmod +x test-connection.sh

# Test koneksi
./test-connection.sh
```

**Expected Output:**
```
🔍 Testing WhatsApp Engine Connection...
=======================================
1️⃣ Testing ping endpoint...
✅ Ping endpoint accessible
2️⃣ Testing health endpoint...
✅ Health endpoint accessible
3️⃣ Testing test endpoint...
✅ Test endpoint accessible
4️⃣ Testing session creation endpoint...
✅ Session endpoint accessible (auth required as expected)
```

#### 3. Perbaiki Nginx Timeout

```bash
# Beri permission execute
chmod +x fix-nginx-timeout.sh

# Perbaiki timeout
./fix-nginx-timeout.sh
```

**Expected Output:**
```
🔧 Memperbaiki timeout Nginx untuk WhatsApp Engine...
==================================================
✅ File konfigurasi ditemukan: /etc/nginx/sites-available/wa-engine
⏱️  Menambahkan timeout settings...
✅ Konfigurasi nginx valid!
✅ Nginx berhasil di-reload!
```

#### 4. Jalankan Semua Perbaikan Sekaligus

```bash
# Beri permission execute
chmod +x fix-nginx-and-start.sh

# Jalankan semua perbaikan
./fix-nginx-and-start.sh
```

## 🔍 Manual Testing

### Test Server Langsung

```bash
# Test ping
curl http://localhost:3000/ping

# Test health
curl http://localhost:3000/health

# Test session creation (akan return Unauthorized)
curl -X POST http://localhost:3000/sessions/create \
  -H "Content-Type: application/json" \
  -d '{"sessionId": "test", "phoneNumber": "1234567890"}'
```

### Test Melalui Nginx

```bash
# Test melalui domain (ganti dengan domain Anda)
curl https://wa.juaraapps.my.id/ping

# Test session creation
curl -X POST https://wa.juaraapps.my.id/sessions/create \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your_api_key" \
  -d '{"sessionId": "test", "phoneNumber": "1234567890"}'
```

## 🛠️ Troubleshooting Lanjutan

### 1. Server Tidak Berjalan

**Gejala:** `curl: (7) Failed to connect to localhost port 3000`

**Solusi:**
```bash
# Cek apakah ada process yang berjalan
ps aux | grep node

# Kill process yang ada
pkill -f "node server-optimized.js"

# Jalankan ulang
./start-server.sh
```

### 2. Nginx Timeout

**Gejala:** `504 Gateway Time-out`

**Solusi Manual:**
```bash
# Edit nginx config
sudo nano /etc/nginx/sites-available/wa-engine

# Tambahkan timeout settings
proxy_connect_timeout 600s;
proxy_send_timeout 600s;
proxy_read_timeout 600s;
send_timeout 600s;

# Test config
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

### 3. Puppeteer Error

**Gejala:** `Failed to launch browser`

**Solusi:**
```bash
# Install dependencies
sudo apt update
sudo apt install -y \
  libatk-bridge2.0-0 \
  libatk1.0-0 \
  libatspi2.0-0 \
  libcups2 \
  libdbus-1-3 \
  libdrm2 \
  libgtk-3-0 \
  libnspr4 \
  libnss3 \
  libxcomposite1 \
  libxdamage1 \
  libxrandr2 \
  libxss1 \
  libxtst6 \
  xvfb

# Atau install Chromium
sudo apt install chromium-browser
```

### 4. Memory Issues

**Gejala:** `JavaScript heap out of memory`

**Solusi:**
```bash
# Set memory limit
export NODE_OPTIONS="--max-old-space-size=4096"

# Jalankan dengan garbage collection
node --expose-gc server-optimized.js
```

## 📊 Monitoring

### Cek Logs

```bash
# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Server logs (jika menggunakan systemd)
sudo journalctl -u wa-engine -f
```

### Cek Status Services

```bash
# Nginx status
sudo systemctl status nginx

# Cek port yang digunakan
netstat -tlnp | grep :3000

# Cek memory usage
free -h
```

## 🎯 Checklist Troubleshooting

- [ ] ✅ Server WhatsApp Engine berjalan di port 3000
- [ ] ✅ Ping endpoint accessible (`/ping`)
- [ ] ✅ Health endpoint accessible (`/health`)
- [ ] ✅ Nginx timeout dikonfigurasi (600s)
- [ ] ✅ Nginx reloaded dan berfungsi
- [ ] ✅ Puppeteer dependencies terinstall
- [ ] ✅ Memory cukup untuk Node.js
- [ ] ✅ Firewall tidak memblokir port 3000
- [ ] ✅ Domain mengarah ke server yang benar

## 🚀 Quick Fix Commands

```bash
# Fix semua masalah sekaligus
chmod +x fix-nginx-and-start.sh && ./fix-nginx-and-start.sh

# Test koneksi
chmod +x test-connection.sh && ./test-connection.sh

# Restart nginx jika perlu
sudo systemctl restart nginx

# Kill dan restart server
pkill -f "node server-optimized.js" && ./start-server.sh
```

## 📞 Support

Jika masih ada masalah:

1. **Cek logs:** `sudo tail -f /var/log/nginx/error.log`
2. **Test server:** `curl http://localhost:3000/ping`
3. **Restart services:** `sudo systemctl restart nginx`
4. **Cek memory:** `free -h && ps aux | grep node` 