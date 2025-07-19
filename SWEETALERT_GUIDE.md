# SweetAlert2 Implementation Guide

## Overview
Aplikasi WA Blast telah diupgrade dengan SweetAlert2 untuk mengganti semua popup native browser dengan alert yang lebih modern dan user-friendly.

## Fitur yang Tersedia

### 1. Success Alert (Toast)
```javascript
showSuccess('Pesan berhasil disimpan!');
showSuccess('Data berhasil dihapus', 'Berhasil!');
```

### 2. Error Alert
```javascript
showError('Terjadi kesalahan saat menyimpan data');
showError('Gagal menghapus data', 'Error!');
```

### 3. Warning Alert
```javascript
showWarning('Semua field harus diisi');
showWarning('Data akan dihapus permanen', 'Peringatan!');
```

### 4. Info Alert
```javascript
showInfo('Fitur akan segera hadir');
showInfo('Data sedang diproses', 'Informasi');
```

### 5. Confirmation Dialog
```javascript
showConfirm('Apakah Anda yakin ingin menghapus data ini?').then((result) => {
    if (result.isConfirmed) {
        // User clicked "Ya"
        deleteData();
    }
});

// Dengan custom text
showConfirm(
    'Apakah Anda yakin ingin menghapus data ini?', 
    'Konfirmasi Hapus', 
    'Ya, Hapus', 
    'Batal'
).then((result) => {
    if (result.isConfirmed) {
        deleteData();
    }
});
```

### 6. Loading Alert
```javascript
showLoading('Memproses data...');

// Tutup loading
Swal.close();
```

## Implementasi di File-file

### File yang Sudah Diupdate:
1. **resources/views/phonebook/index.blade.php**
   - Delete contact confirmation
   - Success/error messages

2. **resources/views/phonebook/show.blade.php**
   - Delete contact confirmation
   - Success/error messages

3. **resources/views/campaigns/index.blade.php**
   - Create campaign validation
   - Start/pause campaign actions
   - Delete campaign confirmation
   - Success/error messages

4. **resources/views/messages/index.blade.php**
   - Retry message confirmation
   - Delete message confirmation
   - Success/error messages

5. **resources/views/sessions/index.blade.php**
   - Delete session confirmation
   - Form validation warnings

6. **resources/views/integration/keys.blade.php**
   - API key generation validation
   - Revoke key confirmation

7. **resources/views/integration/webhook.blade.php**
   - Webhook configuration success
   - Webhook test success
   - Validation warnings

## Custom Styling

SweetAlert2 telah dikustomisasi dengan:
- Border radius yang lebih modern (12px)
- Shadow effects yang elegan
- Color scheme yang konsisten dengan aplikasi
- Hover effects pada tombol
- Toast notifications untuk success messages
- Loading spinner yang smooth

## Override Native Functions

Native `alert()` dan `confirm()` telah di-override untuk menggunakan SweetAlert2 secara otomatis:

```javascript
// Ini akan menggunakan SweetAlert2
alert('Pesan informasi');
confirm('Apakah Anda yakin?');
```

## Best Practices

1. **Gunakan showLoading() untuk operasi async:**
```javascript
showLoading('Memproses...');
fetch('/api/data')
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            showSuccess('Berhasil!');
        } else {
            showError('Gagal!');
        }
    });
```

2. **Gunakan showConfirm() untuk konfirmasi:**
```javascript
showConfirm('Apakah Anda yakin?').then((result) => {
    if (result.isConfirmed) {
        // Lakukan aksi
    }
});
```

3. **Gunakan showSuccess() untuk feedback positif:**
```javascript
showSuccess('Data berhasil disimpan');
```

4. **Gunakan showError() untuk error handling:**
```javascript
.catch(error => {
    showError('Terjadi kesalahan: ' + error.message);
});
```

## File Structure

```
public/js/sweetalert.js          # Helper functions
resources/views/layouts/app.blade.php  # Layout dengan SweetAlert2 CDN
```

## Dependencies

- SweetAlert2 v11 (CDN)
- Custom helper functions
- Custom CSS styling

## Migration dari Native Alert

Semua `alert()` dan `confirm()` native telah diganti dengan:
- `alert()` → `showInfo()` atau `showWarning()`
- `confirm()` → `showConfirm()`

## Performance

- SweetAlert2 dimuat dari CDN untuk performa optimal
- Helper functions di-cache browser
- Minimal overhead pada aplikasi 