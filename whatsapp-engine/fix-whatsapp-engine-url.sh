#!/bin/bash

echo "🔧 Memperbaiki URL WhatsApp Engine di Laravel .env..."
echo "=================================================="

# Masuk ke direktori Laravel
cd /var/www/wa_juaraapps_usr/data/www/wa.juaraapps.my.id

# Cek apakah file .env ada
if [ ! -f ".env" ]; then
    echo "❌ File .env tidak ditemukan!"
    echo "Mencari file .env..."
    find . -name ".env" -type f
    exit 1
fi

echo "✅ File .env ditemukan"

# Backup file .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "✅ Backup .env dibuat"

# Update URL WhatsApp Engine
echo "🔄 Mengupdate URL WhatsApp Engine..."

# Cek apakah ada konfigurasi WhatsApp Engine
if grep -q "WHATSAPP_ENGINE_URL" .env; then
    # Update URL yang sudah ada
    sed -i 's|WHATSAPP_ENGINE_URL=.*|WHATSAPP_ENGINE_URL=http://127.0.0.1:3000|' .env
    echo "✅ URL WhatsApp Engine diupdate"
else
    # Tambah konfigurasi baru
    echo "" >> .env
    echo "# WhatsApp Engine Configuration" >> .env
    echo "WHATSAPP_ENGINE_URL=http://127.0.0.1:3000" >> .env
    echo "WHATSAPP_ENGINE_API_KEY=wa_blast_api_key_2024" >> .env
    echo "✅ Konfigurasi WhatsApp Engine ditambahkan"
fi

# Update API Key jika belum ada
if ! grep -q "WHATSAPP_ENGINE_API_KEY" .env; then
    echo "WHATSAPP_ENGINE_API_KEY=wa_blast_api_key_2024" >> .env
    echo "✅ API Key WhatsApp Engine ditambahkan"
fi

# Clear Laravel cache
echo "🔄 Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Cache cleared"

# Verifikasi perubahan
echo ""
echo "📋 Verifikasi konfigurasi:"
echo "=========================="
grep "WHATSAPP_ENGINE" .env

echo ""
echo "🎯 Test koneksi ke WhatsApp Engine:"
echo "==================================="

# Test koneksi ke WhatsApp Engine
if curl -s http://127.0.0.1:3000/health > /dev/null; then
    echo "✅ WhatsApp Engine berjalan di http://127.0.0.1:3000"
else
    echo "❌ WhatsApp Engine tidak berjalan di http://127.0.0.1:3000"
    echo "   Pastikan server WhatsApp Engine sudah berjalan"
fi

echo ""
echo "🚀 Selesai! Coba buat session baru dari frontend." 