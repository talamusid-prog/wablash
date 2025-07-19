# WhatsApp Contact Grabber

Fitur untuk mengambil (grab) kontak grup dan individual dari nomor WhatsApp yang terhubung di aplikasi WA Blast.

## Fitur Utama

✅ **Grab Kontak Grup** - Mengambil semua grup yang ada di WhatsApp  
✅ **Grab Kontak Individual** - Mengambil semua kontak individual  
✅ **Grab Semua Kontak** - Mengambil grup dan kontak individual sekaligus  
✅ **Penyimpanan Database** - Kontak otomatis disimpan ke database  
✅ **Deduplikasi** - Mencegah kontak duplikat dalam satu session  
✅ **Error Handling** - Penanganan error yang robust  
✅ **Logging** - Log lengkap untuk monitoring  

## Persyaratan

1. **WhatsApp Session Terhubung** - Session harus dalam status `connected`
2. **WhatsApp Engine** - Engine harus mendukung endpoint grabber kontak
3. **Database** - Tabel `contacts` sudah dibuat dan migrated

## Cara Penggunaan

### 1. Pastikan Session WhatsApp Terhubung

Pertama, pastikan session WhatsApp sudah terhubung dengan status `connected`:

```bash
# Cek status session
GET /api/sessions/{session_id}/status

# Response yang diharapkan
{
    "success": true,
    "data": {
        "session_id": "uuid-session",
        "status": "connected",
        "phone_number": "6281234567890"
    }
}
```

### 2. Grab Kontak Grup

```bash
# Ambil semua kontak grup
GET /api/sessions/{session_id}/grab-groups

# Response
{
    "success": true,
    "message": "Kontak grup berhasil diambil dan disimpan",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "total_groups": 5,
        "groups": [...],
        "saved_count": 3,
        "updated_count": 2,
        "grabbed_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 3. Grab Kontak Individual

```bash
# Ambil semua kontak individual
GET /api/sessions/{session_id}/grab-contacts

# Response
{
    "success": true,
    "message": "Kontak individual berhasil diambil dan disimpan",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "total_contacts": 150,
        "contacts": [...],
        "saved_count": 120,
        "updated_count": 30,
        "grabbed_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 4. Grab Semua Kontak

```bash
# Ambil semua kontak (grup + individual)
GET /api/sessions/{session_id}/grab-all

# Response
{
    "success": true,
    "message": "Semua kontak berhasil diambil dan disimpan",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "total_groups": 5,
        "total_contacts": 150,
        "groups": [...],
        "contacts": [...],
        "saved_count": 123,
        "updated_count": 32,
        "grabbed_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 5. Ambil Kontak dari Database

```bash
# Ambil semua kontak yang disimpan
GET /api/sessions/{session_id}/contacts

# Ambil hanya kontak individual
GET /api/sessions/{session_id}/contacts?type=individual

# Ambil hanya kontak grup
GET /api/sessions/{session_id}/contacts?type=group
```

### 6. Hapus Kontak dari Database

```bash
# Hapus semua kontak untuk session tertentu
DELETE /api/sessions/{session_id}/contacts
```

## Struktur Data Kontak

### Kontak Individual
```json
{
    "id": 1,
    "session_id": "uuid-session",
    "contact_id": "6281234567890@s.whatsapp.net",
    "name": "Nama Kontak",
    "phone_number": "6281234567890",
    "type": "individual",
    "group_id": null,
    "is_admin": false,
    "profile_picture": "https://example.com/photo.jpg",
    "status": "active",
    "grabbed_at": "2024-01-15T10:30:00.000000Z"
}
```

### Kontak Grup
```json
{
    "id": 2,
    "session_id": "uuid-session",
    "contact_id": "123456789@g.us",
    "name": "Nama Grup",
    "phone_number": null,
    "type": "group",
    "group_id": null,
    "group_name": "Nama Grup",
    "group_description": "Deskripsi grup",
    "group_participants_count": 25,
    "is_admin": true,
    "profile_picture": "https://example.com/group-photo.jpg",
    "status": "active",
    "grabbed_at": "2024-01-15T10:30:00.000000Z"
}
```

## Integrasi dengan WhatsApp Engine

WhatsApp Engine harus mengimplementasikan endpoint berikut:

### 1. Grab Kontak Grup
```
GET /sessions/{sessionId}/groups
```

### 2. Grab Kontak Individual
```
GET /sessions/{sessionId}/contacts
```

### 3. Grab Semua Kontak
```
GET /sessions/{sessionId}/all-contacts
```

Lihat file `whatsapp-engine/contact-grabber-example.js` untuk contoh implementasi lengkap.

## Error Handling

### Session Tidak Terhubung
```json
{
    "success": false,
    "message": "Session tidak terhubung. Status: connecting"
}
```

### Session Tidak Ditemukan
```json
{
    "success": false,
    "error": "Session tidak ditemukan"
}
```

### Error Internal
```json
{
    "success": false,
    "message": "Gagal mengambil kontak grup: Connection timeout"
}
```

## Monitoring dan Logging

Semua aktivitas grabber kontak akan di-log dengan detail:

```php
// Log saat mulai grab
Log::info('Grabbing group contacts', [
    'session_id' => $sessionId,
    'session_name' => $sessionName
]);

// Log saat berhasil
Log::info('Group contacts grabbed and saved successfully', [
    'session_id' => $sessionId,
    'total_groups' => $totalGroups,
    'saved_count' => $savedCount,
    'updated_count' => $updatedCount
]);

// Log error
Log::error('Error grabbing group contacts', [
    'session_id' => $sessionId,
    'error' => $errorMessage
]);
```

## Performa dan Optimasi

1. **Timeout**: Set timeout 60-120 detik untuk proses grabber
2. **Batch Processing**: Kontak diproses satu per satu untuk menghindari memory overflow
3. **Database Transaction**: Menggunakan transaction untuk konsistensi data
4. **Error Recovery**: Jika satu kontak error, proses tetap lanjut

## Keamanan

1. **API Key Authentication**: Semua request harus menyertakan API key yang valid
2. **Session Validation**: Hanya session yang terhubung yang bisa diakses
3. **Data Sanitization**: Semua data input dibersihkan sebelum disimpan
4. **Access Control**: Kontak hanya bisa diakses oleh session yang sesuai

## Troubleshooting

### Masalah Umum

1. **Session tidak terhubung**
   - Pastikan QR code sudah di-scan
   - Cek status session dengan `/api/sessions/{session_id}/status`

2. **Timeout error**
   - Tingkatkan timeout di WhatsApp Engine
   - Cek koneksi internet

3. **Kontak tidak tersimpan**
   - Cek log error di storage/logs/laravel.log
   - Pastikan tabel contacts sudah dibuat

4. **WhatsApp Engine error**
   - Cek apakah engine mendukung endpoint grabber
   - Restart WhatsApp Engine

### Debug Mode

Aktifkan debug mode untuk melihat detail error:

```php
// Di .env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Contoh Implementasi Lengkap

Lihat file-file berikut untuk implementasi lengkap:

- `app/Services/WhatsAppService.php` - Service untuk komunikasi dengan WhatsApp Engine
- `app/Services/ContactService.php` - Service untuk mengelola kontak
- `app/Http/Controllers/Api/WhatsAppController.php` - Controller API
- `app/Models/Contact.php` - Model kontak
- `whatsapp-engine/contact-grabber-example.js` - Contoh implementasi WhatsApp Engine

## Support

Jika mengalami masalah, silakan:

1. Cek log error di `storage/logs/laravel.log`
2. Pastikan semua persyaratan terpenuhi
3. Test dengan session WhatsApp yang berbeda
4. Hubungi tim support dengan detail error yang lengkap 