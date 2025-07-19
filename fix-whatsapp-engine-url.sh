#!/bin/bash

echo "🔧 Memperbaiki Konfigurasi URL WhatsApp Engine..."
echo "================================================"

# Cek apakah file .env ada
if [ ! -f ".env" ]; then
    echo "❌ File .env tidak ditemukan!"
    echo "   Membuat file .env dari .env_bc..."
    cp .env_bc .env
fi

# Cek apakah WhatsApp Engine URL sudah benar
CURRENT_URL=$(grep "WHATSAPP_ENGINE_URL" .env | cut -d'=' -f2)

echo "📋 Konfigurasi saat ini:"
echo "   WHATSAPP_ENGINE_URL: $CURRENT_URL"

# Tanya user untuk URL yang benar
echo ""
echo "🔍 Masukkan URL WhatsApp Engine yang benar:"
echo "   Contoh: http://localhost:3000 (jika di server yang sama)"
echo "   Contoh: http://IP_SERVER:3000 (jika di server berbeda)"
echo "   Contoh: https://wa.juaraapps.my.id (jika melalui domain)"
echo ""

read -p "Masukkan URL WhatsApp Engine: " NEW_URL

if [ -z "$NEW_URL" ]; then
    echo "❌ URL tidak boleh kosong!"
    exit 1
fi

# Update .env file
echo "⏳ Mengupdate file .env..."
sed -i "s|WHATSAPP_ENGINE_URL=.*|WHATSAPP_ENGINE_URL=$NEW_URL|" .env

# Verifikasi perubahan
UPDATED_URL=$(grep "WHATSAPP_ENGINE_URL" .env | cut -d'=' -f2)
echo "✅ URL berhasil diupdate:"
echo "   WHATSAPP_ENGINE_URL: $UPDATED_URL"

# Clear config cache
echo "🔄 Clearing config cache..."
php artisan config:clear
php artisan cache:clear

echo ""
echo "🎯 Konfigurasi selesai!"
echo "======================"
echo ""
echo "📋 Langkah selanjutnya:"
echo "   1. Pastikan WhatsApp Engine server berjalan"
echo "   2. Test koneksi: curl $NEW_URL/ping"
echo "   3. Coba buat session dari frontend"
echo ""
echo "🔍 Test koneksi:"
echo "   curl $NEW_URL/ping"
echo "   curl $NEW_URL/health" 