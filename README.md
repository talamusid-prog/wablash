# WhatsApp Blast Application

Aplikasi WhatsApp Blast yang dibangun dengan Laravel 12 untuk mengirim pesan massal melalui WhatsApp.

## Stack Teknologi

- **Backend**: Laravel 12
- **Frontend**: Blade + Tailwind CSS
- **Database**: MySQL
- **WA Engine**: Node.js + whatsapp-web.js
- **Fitur API**: RESTful API menggunakan Laravel API Resources

## Fitur Utama

### 1. Manajemen Sesi WhatsApp
- Membuat sesi WhatsApp baru
- Menampilkan QR code untuk login
- Monitoring status koneksi
- Mengelola multiple sesi

### 2. Kampanye Blast
- Membuat kampanye blast dengan template pesan
- Menambahkan daftar nomor tujuan
- Scheduling kampanye
- Monitoring progress dan statistik

### 3. API RESTful
- Endpoint untuk manajemen sesi WhatsApp
- Endpoint untuk kampanye blast
- Response format yang konsisten
- Error handling yang baik

### 4. Dashboard Web
- Statistik real-time
- Monitoring sesi dan kampanye
- Interface yang user-friendly

## Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd wa-blast
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wa_blast_db
DB_USERNAME=root
DB_PASSWORD=your_password

# WhatsApp Engine Configuration
WHATSAPP_BASE_URL=http://localhost:3000
WHATSAPP_API_KEY=your_api_key
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Build Assets
```bash
npm run build
```

## WhatsApp Engine Setup

### 1. Install Node.js Dependencies
```bash
cd whatsapp-engine
npm install
```

### 2. Konfigurasi Engine
Edit file `whatsapp-engine/config.js`:
```javascript
module.exports = {
    port: 3000,
    apiKey: 'your_api_key',
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    }
};
```

### 3. Jalankan Engine
```bash
cd whatsapp-engine
node server.js
```

## Penggunaan

### 1. Menjalankan Aplikasi
```bash
php artisan serve
```

### 2. Menjalankan Queue Worker
```bash
php artisan queue:work
```

### 3. Menjalankan WhatsApp Engine
```bash
php artisan whatsapp:start-engine
```

### 4. API Endpoints

#### WhatsApp Sessions
- `GET /api/whatsapp/sessions` - Daftar semua sesi
- `POST /api/whatsapp/sessions` - Buat sesi baru
- `GET /api/whatsapp/sessions/{id}` - Detail sesi
- `PUT /api/whatsapp/sessions/{id}` - Update sesi
- `DELETE /api/whatsapp/sessions/{id}` - Hapus sesi
- `GET /api/whatsapp/sessions/{id}/qr` - QR code sesi
- `POST /api/whatsapp/sessions/{id}/send` - Kirim pesan

#### Blast Campaigns
- `GET /api/blast/campaigns` - Daftar kampanye
- `POST /api/blast/campaigns` - Buat kampanye baru
- `GET /api/blast/campaigns/{id}` - Detail kampanye
- `PUT /api/blast/campaigns/{id}` - Update kampanye
- `DELETE /api/blast/campaigns/{id}` - Hapus kampanye
- `POST /api/blast/campaigns/{id}/start` - Mulai kampanye
- `GET /api/blast/campaigns/{id}/statistics` - Statistik kampanye
- `GET /api/blast/campaigns/{id}/messages` - Pesan kampanye

### 5. Contoh Request API

#### Membuat Sesi WhatsApp
```bash
curl -X POST http://localhost:8000/api/whatsapp/sessions \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "6281234567890"
  }'
```

#### Membuat Kampanye Blast
```bash
curl -X POST http://localhost:8000/api/blast/campaigns \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Promo Hari Ini",
    "message_template": "Halo! Promo spesial untuk Anda. Klik link berikut: https://example.com",
    "target_numbers": ["6281234567890", "6281234567891"],
    "session_id": "session-uuid-here"
  }'
```

## Struktur Database

### Tabel `whatsapp_sessions`
- `id` - Primary key
- `session_id` - UUID sesi WhatsApp
- `phone_number` - Nomor telepon
- `status` - Status koneksi (connecting, connected, disconnected, error)
- `qr_code` - QR code untuk login
- `is_active` - Status aktif sesi
- `last_activity` - Timestamp aktivitas terakhir
- `device_info` - Informasi device (JSON)

### Tabel `whatsapp_messages`
- `id` - Primary key
- `session_id` - Foreign key ke whatsapp_sessions
- `message_id` - ID pesan WhatsApp
- `from_number` - Nomor pengirim
- `to_number` - Nomor penerima
- `message_type` - Tipe pesan (text, image, video, dll)
- `content` - Isi pesan
- `media_url` - URL media (jika ada)
- `status` - Status pesan (pending, sent, delivered, read, failed)
- `direction` - Arah pesan (in, out)
- `timestamp` - Timestamp pesan
- `metadata` - Metadata tambahan (JSON)

### Tabel `blast_campaigns`
- `id` - Primary key
- `name` - Nama kampanye
- `message_template` - Template pesan
- `target_numbers` - Daftar nomor tujuan (JSON)
- `status` - Status kampanye (draft, scheduled, running, completed, failed)
- `scheduled_at` - Waktu terjadwal
- `sent_count` - Jumlah terkirim
- `failed_count` - Jumlah gagal
- `total_count` - Total pesan
- `session_id` - Foreign key ke whatsapp_sessions
- `created_by` - User yang membuat

### Tabel `blast_messages`
- `id` - Primary key
- `campaign_id` - Foreign key ke blast_campaigns
- `phone_number` - Nomor telepon
- `message_content` - Isi pesan
- `status` - Status (pending, sent, failed)
- `sent_at` - Waktu terkirim
- `error_message` - Pesan error (jika gagal)
- `whatsapp_message_id` - Foreign key ke whatsapp_messages

## Monitoring dan Logging

### Log Files
- `storage/logs/laravel.log` - Log aplikasi Laravel
- `storage/logs/whatsapp.log` - Log WhatsApp engine

### Monitoring
- Dashboard web untuk monitoring real-time
- API endpoint untuk statistik
- Queue monitoring untuk job processing

## Troubleshooting

### 1. Database Connection Error
- Pastikan MySQL server berjalan
- Periksa konfigurasi database di `.env`
- Jalankan `php artisan migrate:fresh` jika perlu

### 2. WhatsApp Engine Tidak Terhubung
- Pastikan Node.js engine berjalan di port 3000
- Periksa konfigurasi `WHATSAPP_BASE_URL` di `.env`
- Periksa log engine untuk error

### 3. QR Code Tidak Muncul
- Pastikan sesi WhatsApp dibuat dengan benar
- Periksa koneksi ke WhatsApp Web
- Restart WhatsApp engine jika perlu

### 4. Pesan Tidak Terkirim
- Periksa status sesi WhatsApp (harus connected)
- Periksa format nomor telepon (dengan kode negara)
- Periksa log untuk error detail

## Keamanan

### 1. API Security
- Implementasi rate limiting
- Validasi input yang ketat
- Sanitasi data sebelum disimpan

### 2. WhatsApp Security
- Session management yang aman
- QR code expiration
- Logout otomatis untuk session yang tidak aktif

### 3. Data Protection
- Enkripsi data sensitif
- Backup database secara berkala
- Log rotation untuk mencegah disk penuh

## Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

## Support

Untuk dukungan teknis, silakan buat issue di repository ini atau hubungi tim development.
