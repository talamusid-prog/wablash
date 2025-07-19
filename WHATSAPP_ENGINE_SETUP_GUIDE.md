# 🚀 Panduan Setup WhatsApp Engine

## 🚨 Masalah: cURL error 28: Operation timeout

### Penyebab
Frontend Laravel mencoba mengakses WhatsApp Engine di `http://localhost:3000` yang tidak bisa diakses dari browser frontend.

### Solusi Langkah demi Langkah

## 1. Perbaiki Konfigurasi URL WhatsApp Engine

### Jalankan script perbaikan konfigurasi:

```bash
# Beri permission execute
chmod +x fix-whatsapp-engine-url.sh

# Jalankan script
./fix-whatsapp-engine-url.sh
```

**Input yang diminta:**
- Jika WhatsApp Engine berjalan di server yang sama: `http://localhost:3000`
- Jika WhatsApp Engine berjalan di server berbeda: `http://IP_SERVER:3000`
- Jika melalui domain: `https://wa.juaraapps.my.id`

## 2. Test Koneksi WhatsApp Engine

### Jalankan script test koneksi:

```bash
# Beri permission execute
chmod +x test-whatsapp-engine.sh

# Test koneksi
./test-whatsapp-engine.sh
```

**Expected Output:**
```
🔍 Testing WhatsApp Engine Connection...
=======================================
📡 Testing connection to: http://localhost:3000

1️⃣ Testing ping endpoint...
✅ Ping endpoint working
   Response: {"success":true,"message":"pong","timestamp":"..."}

2️⃣ Testing health endpoint...
✅ Health endpoint working
   Server uptime: 120 seconds
   Active sessions: 0

3️⃣ Testing session creation endpoint...
✅ Session endpoint working (auth required as expected)
   Response: {"success":false,"error":"Unauthorized"}

🎯 Connection Test Results:
==========================
✅ WhatsApp Engine is accessible
✅ Configuration is correct
```

## 3. Perbaiki Nginx Timeout (Jika Menggunakan Domain)

### Jalankan script perbaikan nginx:

```bash
# Beri permission execute
chmod +x whatsapp-engine/fix-nginx-timeout.sh

# Perbaiki timeout
./whatsapp-engine/fix-nginx-timeout.sh
```

## 4. Manual Configuration

### Jika script tidak berfungsi, lakukan manual:

#### A. Edit file .env

```bash
# Buat file .env jika belum ada
cp .env_bc .env

# Edit file .env
nano .env
```

**Tambahkan atau update:**
```env
WHATSAPP_ENGINE_URL=http://localhost:3000
WHATSAPP_ENGINE_API_KEY=wa_blast_api_key_2024
```

#### B. Clear Laravel Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

#### C. Test Koneksi Manual

```bash
# Test ping
curl http://localhost:3000/ping

# Test health
curl http://localhost:3000/health

# Test session creation (akan return Unauthorized)
curl -X POST http://localhost:3000/sessions/create \
  -H "Content-Type: application/json" \
  -H "X-API-Key: wa_blast_api_key_2024" \
  -d '{"sessionId": "test", "phoneNumber": "1234567890"}'
```

## 5. Troubleshooting

### A. Server Tidak Berjalan

**Gejala:** `curl: (7) Failed to connect to localhost port 3000`

**Solusi:**
```bash
# Cek apakah server berjalan
ps aux | grep "node server-optimized.js"

# Jalankan server
cd whatsapp-engine
./start-server.sh
```

### B. URL Konfigurasi Salah

**Gejala:** `cURL error 28: Operation timeout`

**Solusi:**
```bash
# Cek URL di .env
grep WHATSAPP_ENGINE_URL .env

# Update URL yang benar
sed -i 's|WHATSAPP_ENGINE_URL=.*|WHATSAPP_ENGINE_URL=http://localhost:3000|' .env

# Clear cache
php artisan config:clear
```

### C. Nginx Timeout

**Gejala:** `504 Gateway Time-out`

**Solusi:**
```bash
# Edit nginx config
sudo nano /etc/nginx/sites-available/wa-engine

# Tambahkan timeout settings
proxy_connect_timeout 600s;
proxy_send_timeout 600s;
proxy_read_timeout 600s;
send_timeout 600s;

# Test dan reload nginx
sudo nginx -t
sudo systemctl reload nginx
```

### D. API Key Tidak Cocok

**Gejala:** `{"success":false,"error":"Unauthorized"}`

**Solusi:**
```bash
# Cek API key di .env
grep WHATSAPP_ENGINE_API_KEY .env

# Update API key jika perlu
sed -i 's|WHATSAPP_ENGINE_API_KEY=.*|WHATSAPP_ENGINE_API_KEY=wa_blast_api_key_2024|' .env

# Clear cache
php artisan config:clear
```

## 6. Verifikasi Setup

### A. Test Frontend ke Backend

```bash
# Test API Laravel
curl -X POST http://your-domain.com/api/sessions \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{"name": "test", "phone_number": "1234567890"}'
```

### B. Test Backend ke WhatsApp Engine

```bash
# Test dari Laravel ke WhatsApp Engine
php artisan tinker
```

```php
$service = new App\Services\WhatsAppService();
$result = $service->createSession('test-session', 'Test Session', '1234567890');
dd($result);
```

## 7. Monitoring

### A. Cek Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# WhatsApp Engine logs (jika berjalan di background)
tail -f /var/log/wa-engine.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
```

### B. Cek Status Services

```bash
# Cek Node.js processes
ps aux | grep node

# Cek port usage
netstat -tlnp | grep :3000

# Cek memory usage
free -h
```

## 8. Quick Fix Commands

```bash
# Fix semua masalah sekaligus
chmod +x fix-whatsapp-engine-url.sh && ./fix-whatsapp-engine-url.sh
chmod +x test-whatsapp-engine.sh && ./test-whatsapp-engine.sh
chmod +x whatsapp-engine/fix-nginx-timeout.sh && ./whatsapp-engine/fix-nginx-timeout.sh

# Restart services
sudo systemctl restart nginx
cd whatsapp-engine && ./start-server.sh

# Clear Laravel cache
php artisan config:clear && php artisan cache:clear
```

## 🎯 Checklist Setup

- [ ] ✅ WhatsApp Engine server berjalan di port 3000
- [ ] ✅ URL konfigurasi benar di .env
- [ ] ✅ API key sesuai antara Laravel dan WhatsApp Engine
- [ ] ✅ Nginx timeout dikonfigurasi (jika menggunakan domain)
- [ ] ✅ Laravel cache di-clear
- [ ] ✅ Test koneksi berhasil
- [ ] ✅ Frontend bisa mengakses backend
- [ ] ✅ Backend bisa mengakses WhatsApp Engine

## 📞 Support

Jika masih ada masalah:

1. **Cek logs:** `tail -f storage/logs/laravel.log`
2. **Test koneksi:** `./test-whatsapp-engine.sh`
3. **Restart services:** `sudo systemctl restart nginx`
4. **Cek memory:** `free -h && ps aux | grep node` 