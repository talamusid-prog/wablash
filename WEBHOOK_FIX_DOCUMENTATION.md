# Fix Webhook Configuration - Problem & Solution

## **Masalah yang Ditemukan:**

Saat mengakses halaman `https://wa-blast.test/integration/webhook`, ada masalah dimana **URL webhook tidak tersimpan** setelah di-refresh halaman.

## **Root Cause Analysis:**

1. **Frontend tidak terhubung ke backend** - Function `saveWebhookConfig()` di JavaScript hanya melakukan `console.log()` dan tidak mengirim request ke API backend
2. **Backend tidak menyimpan data** - Method `setWebhookConfig()` di API controller hanya me-return response tanpa benar-benar menyimpan ke database
3. **Tidak ada model/tabel database** - Tidak ada struktur database untuk menyimpan konfigurasi webhook

## **Solusi yang Diterapkan:**

### 1. **Model & Migration** ✅
- **Dibuat:** `app/Models/WebhookConfig.php`
- **Dibuat:** Migration `2025_08_09_195411_create_webhook_configs_table.php`
- **Jalankan:** `php artisan migrate`

### 2. **Backend API Controller** ✅
- **Update:** `app/Http/Controllers/Api/IntegrationController.php`
  - Method `getWebhookConfig()` - sekarang baca dari database 
  - Method `setWebhookConfig()` - sekarang simpan ke database
  - Method `testWebhook()` - sekarang baca config dari database dan kirim HTTP request real

### 3. **Web Controller** ✅
- **Update:** `app/Http/Controllers/Web/IntegrationController.php`
  - Method `webhook()` - sekarang pass data webhook config ke view

### 4. **Frontend View** ✅
- **Update:** `resources/views/integration/webhook.blade.php`
  - Form fields sekarang load value dari database: `{{ $webhookConfig->url ?? 'default' }}`
  - JavaScript `saveWebhookConfig()` sekarang kirim POST request ke `/api/v1/integration/webhook-config`
  - JavaScript `testWebhook()` sekarang kirim POST request ke `/api/v1/integration/test-webhook`
  - Added loading states dan proper error handling

## **Struktur Database:**

```sql
CREATE TABLE webhook_configs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NULL,
    secret VARCHAR(255) NULL,
    events JSON NULL,
    enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## **API Endpoints:**

1. **GET** `/api/v1/integration/webhook-config` - Mendapatkan konfigurasi webhook
2. **POST** `/api/v1/integration/webhook-config` - Menyimpan konfigurasi webhook  
3. **POST** `/api/v1/integration/test-webhook` - Test webhook connection

## **Fungsi Halaman Webhook:**

### **Webhook Settings:**
- **Webhook URL** - URL endpoint untuk menerima webhook events
- **Secret Key** - Key untuk validasi webhook security (dengan generator)
- **Events** - Pilihan event yang akan dikirim:
  - Message Sent
  - Message Delivered  
  - Message Failed
  - Session Connected
  - Session Disconnected
  - Campaign Started
  - Campaign Completed
- **Enable/Disable** - Toggle untuk mengaktifkan/menonaktifkan webhook

### **Actions:**
- **Save Configuration** - Menyimpan konfigurasi ke database
- **Test Webhook** - Mengirim test event ke webhook URL

### **Webhook Status:**
- Status webhook (Active/Inactive)
- Last delivery timestamp
- Success rate
- Total events sent

### **Event Documentation:**
- Contoh payload untuk setiap jenis event
- Format JSON yang akan dikirim ke webhook endpoint

## **Flow Setelah Fix:**

1. **Load Page** → Controller ambil data dari database → View tampilkan form dengan data tersimpan
2. **User Input** → JavaScript kumpulkan data form
3. **Save** → AJAX POST ke API → API simpan ke database → Response sukses/error
4. **Refresh** → Data tetap ada karena disimpan di database ✅

## **Testing:**

Setelah fix, test dengan:
1. Akses `https://wa-blast.test/integration/webhook`
2. Isi form webhook URL dan secret
3. Pilih events yang diinginkan
4. Klik "Save Configuration"
5. Refresh halaman
6. **Hasil:** Data tetap tersimpan ✅

## **Files Changed:**

1. `app/Models/WebhookConfig.php` (NEW)
2. `database/migrations/2025_08_09_195411_create_webhook_configs_table.php` (NEW)
3. `app/Http/Controllers/Api/IntegrationController.php` (UPDATED)
4. `app/Http/Controllers/Web/IntegrationController.php` (UPDATED)  
5. `resources/views/integration/webhook.blade.php` (UPDATED)

## **Next Steps (Optional):**

1. **Webhook Logs** - Buat tabel untuk menyimpan log webhook events
2. **Real-time Webhook Status** - Update status webhook secara real-time
3. **Webhook Retry** - Implementasi retry mechanism untuk failed webhooks
4. **Security** - Add webhook signature validation
