# WhatsApp Contact Grabber API

Dokumentasi API untuk fungsi grabber kontak dari WhatsApp yang terhubung.

## Endpoint Overview

### 1. Grab Kontak Grup
**GET** `/api/sessions/{session_id}/grab-groups`

Mengambil semua kontak grup dari WhatsApp session yang terhubung.

**Parameters:**
- `session_id` (string, required) - UUID session WhatsApp

**Response Success:**
```json
{
    "success": true,
    "message": "Kontak grup berhasil diambil dan disimpan",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "total_groups": 5,
        "groups": [
            {
                "id": "group-id",
                "name": "Nama Grup",
                "desc": "Deskripsi grup",
                "participants_count": 25,
                "is_admin": true,
                "profile_picture": "url-foto"
            }
        ],
        "saved_count": 3,
        "updated_count": 2,
        "grabbed_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

**Response Error:**
```json
{
    "success": false,
    "message": "Session tidak terhubung. Status: connecting"
}
```

### 2. Grab Kontak Individual
**GET** `/api/sessions/{session_id}/grab-contacts`

Mengambil semua kontak individual dari WhatsApp session yang terhubung.

**Parameters:**
- `session_id` (string, required) - UUID session WhatsApp

**Response Success:**
```json
{
    "success": true,
    "message": "Kontak individual berhasil diambil dan disimpan",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "total_contacts": 150,
        "contacts": [
            {
                "id": "contact-id",
                "name": "Nama Kontak",
                "phone": "6281234567890",
                "pushname": "Display Name",
                "is_admin": false,
                "profile_picture": "url-foto"
            }
        ],
        "saved_count": 120,
        "updated_count": 30,
        "grabbed_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 3. Grab Semua Kontak
**GET** `/api/sessions/{session_id}/grab-all`

Mengambil semua kontak (grup dan individual) dari WhatsApp session yang terhubung.

**Parameters:**
- `session_id` (string, required) - UUID session WhatsApp

**Response Success:**
```json
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

### 4. Ambil Kontak dari Database
**GET** `/api/sessions/{session_id}/contacts`

Mengambil kontak yang sudah disimpan di database.

**Parameters:**
- `session_id` (string, required) - UUID session WhatsApp
- `type` (string, optional) - Filter tipe kontak: `individual`, `group`, atau kosong untuk semua

**Query Parameters:**
- `type=individual` - Hanya ambil kontak individual
- `type=group` - Hanya ambil kontak grup
- Tanpa parameter - Ambil semua kontak

**Response Success:**
```json
{
    "success": true,
    "message": "Kontak berhasil diambil dari database",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "type": "individual",
        "total_contacts": 150,
        "contacts": [
            {
                "id": 1,
                "session_id": "uuid-session",
                "contact_id": "contact-whatsapp-id",
                "name": "Nama Kontak",
                "phone_number": "6281234567890",
                "type": "individual",
                "group_id": null,
                "is_admin": false,
                "profile_picture": "url-foto",
                "status": "active",
                "grabbed_at": "2024-01-15T10:30:00.000000Z",
                "created_at": "2024-01-15T10:30:00.000000Z",
                "updated_at": "2024-01-15T10:30:00.000000Z"
            }
        ]
    }
}
```

### 5. Hapus Kontak dari Database
**DELETE** `/api/sessions/{session_id}/contacts`

Menghapus semua kontak yang disimpan untuk session tertentu.

**Parameters:**
- `session_id` (string, required) - UUID session WhatsApp

**Response Success:**
```json
{
    "success": true,
    "message": "Kontak berhasil dihapus dari database",
    "data": {
        "session_id": "uuid-session",
        "session_name": "Nama Session",
        "deleted_count": 155
    }
}
```

## Struktur Database

### Tabel `contacts`

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `session_id` | string | UUID session WhatsApp |
| `contact_id` | string | ID kontak dari WhatsApp |
| `name` | string | Nama kontak |
| `phone_number` | string | Nomor telepon (untuk kontak individual) |
| `type` | enum | Tipe kontak: `individual` atau `group` |
| `group_id` | string | ID grup (untuk kontak individual yang ada di grup) |
| `group_name` | string | Nama grup (untuk kontak grup) |
| `group_description` | text | Deskripsi grup |
| `group_participants_count` | integer | Jumlah peserta grup |
| `is_admin` | boolean | Apakah admin grup |
| `profile_picture` | string | URL foto profil |
| `status` | enum | Status kontak: `active`, `inactive`, `blocked` |
| `grabbed_at` | timestamp | Waktu kontak di-grab |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

## Contoh Penggunaan

### 1. Grab Kontak Grup
```bash
curl -X GET "http://localhost:8000/api/sessions/123e4567-e89b-12d3-a456-426614174000/grab-groups" \
  -H "Accept: application/json"
```

### 2. Grab Kontak Individual
```bash
curl -X GET "http://localhost:8000/api/sessions/123e4567-e89b-12d3-a456-426614174000/grab-contacts" \
  -H "Accept: application/json"
```

### 3. Grab Semua Kontak
```bash
curl -X GET "http://localhost:8000/api/sessions/123e4567-e89b-12d3-a456-426614174000/grab-all" \
  -H "Accept: application/json"
```

### 4. Ambil Kontak Individual dari Database
```bash
curl -X GET "http://localhost:8000/api/sessions/123e4567-e89b-12d3-a456-426614174000/contacts?type=individual" \
  -H "Accept: application/json"
```

### 5. Hapus Kontak dari Database
```bash
curl -X DELETE "http://localhost:8000/api/sessions/123e4567-e89b-12d3-a456-426614174000/contacts" \
  -H "Accept: application/json"
```

## Catatan Penting

1. **Session Harus Terhubung**: Semua endpoint grabber memerlukan session WhatsApp yang sudah terhubung (status: `connected`).

2. **Timeout**: Proses grabber kontak memerlukan waktu yang cukup lama (60-120 detik) tergantung jumlah kontak.

3. **Duplikasi**: Sistem akan otomatis mendeteksi dan update kontak yang sudah ada, atau membuat kontak baru jika belum ada.

4. **Error Handling**: Jika terjadi error pada beberapa kontak, proses akan tetap berlanjut dan mengembalikan daftar error yang terjadi.

5. **Database Storage**: Semua kontak yang di-grab akan otomatis disimpan ke database untuk penggunaan selanjutnya.

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Session tidak terhubung |
| 404 | Session tidak ditemukan |
| 500 | Error internal server |

## Integrasi dengan WhatsApp Engine

Fungsi grabber ini memerlukan WhatsApp Engine yang mendukung endpoint berikut:
- `GET /sessions/{sessionId}/groups` - Untuk mengambil kontak grup
- `GET /sessions/{sessionId}/contacts` - Untuk mengambil kontak individual  
- `GET /sessions/{sessionId}/all-contacts` - Untuk mengambil semua kontak 