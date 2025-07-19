# WhatsApp Engine

Node.js engine untuk WhatsApp Blast Application yang menggunakan whatsapp-web.js.

## Fitur

- ✅ Multi-session management
- ✅ QR code generation
- ✅ Message sending (text, image, document)
- ✅ Session status monitoring
- ✅ RESTful API
- ✅ Authentication dengan API key
- ✅ Graceful shutdown

## Instalasi

### 1. Install Dependencies
```bash
npm install
```

### 2. Setup Environment
```bash
cp env.example .env
```

Edit file `.env`:
```env
PORT=3000
API_KEY=your_secure_api_key_here
```

### 3. Jalankan Engine
```bash
# Development mode
npm run dev

# Production mode
npm start
```

## API Endpoints

### Health Check
```
GET /health
```

### Sessions
```
POST /sessions/create
GET /sessions/:sessionId/qr
GET /sessions/:sessionId/status
DELETE /sessions/:sessionId
GET /sessions
```

### Messages
```
POST /sessions/:sessionId/send
```

## Contoh Penggunaan

### 1. Membuat Session
```bash
curl -X POST http://localhost:3000/sessions/create \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your_api_key" \
  -d '{
    "sessionId": "session-123",
    "phoneNumber": "6281234567890"
  }'
```

### 2. Get QR Code
```bash
curl -X GET http://localhost:3000/sessions/session-123/qr \
  -H "X-API-Key: your_api_key"
```

### 3. Send Message
```bash
curl -X POST http://localhost:3000/sessions/session-123/send \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your_api_key" \
  -d '{
    "to": "6281234567890",
    "message": "Hello from WhatsApp Engine!",
    "type": "text"
  }'
```

## Session States

- `connecting` - Session sedang dibuat
- `qr_ready` - QR code siap untuk scan
- `authenticated` - Berhasil login
- `connected` - Siap untuk kirim pesan
- `auth_failed` - Gagal login
- `disconnected` - Terputus

## Troubleshooting

### 1. QR Code Tidak Muncul
- Pastikan Chrome/Chromium terinstall
- Periksa log untuk error Puppeteer
- Restart engine jika perlu

### 2. Session Terputus
- Periksa koneksi internet
- Pastikan WhatsApp Web tidak logout
- Restart session jika perlu

### 3. Pesan Tidak Terkirim
- Periksa format nomor telepon
- Pastikan session status 'connected'
- Periksa log untuk error detail

## Security

- Gunakan API key yang kuat
- Batasi akses ke engine
- Monitor log untuk aktivitas mencurigakan
- Update dependencies secara berkala

## Logs

Engine akan menampilkan log untuk:
- Session creation/deletion
- QR code generation
- Message sending
- Errors dan warnings

## Performance

- Engine dapat menangani multiple sessions
- Rate limiting untuk mencegah spam
- Memory management untuk session cleanup
- Graceful shutdown untuk data integrity 