#!/bin/bash

echo "🔧 Memperbaiki timeout Nginx untuk WhatsApp Engine..."
echo "=================================================="

# Backup konfigurasi nginx yang ada
echo "📋 Backup konfigurasi nginx..."
sudo cp /etc/nginx/sites-available/wa-engine /etc/nginx/sites-available/wa-engine.backup.$(date +%Y%m%d_%H%M%S)

# Cari file konfigurasi nginx yang digunakan
NGINX_CONFIG=""
if [ -f "/etc/nginx/sites-available/wa-engine" ]; then
    NGINX_CONFIG="/etc/nginx/sites-available/wa-engine"
elif [ -f "/etc/nginx/sites-available/default" ]; then
    NGINX_CONFIG="/etc/nginx/sites-available/default"
else
    echo "❌ File konfigurasi nginx tidak ditemukan!"
    echo "   Cari file konfigurasi nginx secara manual:"
    echo "   ls /etc/nginx/sites-available/"
    exit 1
fi

echo "✅ File konfigurasi ditemukan: $NGINX_CONFIG"

# Tambahkan timeout settings ke konfigurasi nginx
echo "⏱️  Menambahkan timeout settings..."

# Cek apakah sudah ada proxy timeout settings
if grep -q "proxy_connect_timeout" "$NGINX_CONFIG"; then
    echo "⚠️  Timeout settings sudah ada, akan diupdate..."
    # Update timeout yang ada
    sudo sed -i 's/proxy_connect_timeout [0-9]*s;/proxy_connect_timeout 600s;/g' "$NGINX_CONFIG"
    sudo sed -i 's/proxy_send_timeout [0-9]*s;/proxy_send_timeout 600s;/g' "$NGINX_CONFIG"
    sudo sed -i 's/proxy_read_timeout [0-9]*s;/proxy_read_timeout 600s;/g' "$NGINX_CONFIG"
    sudo sed -i 's/send_timeout [0-9]*s;/send_timeout 600s;/g' "$NGINX_CONFIG"
else
    echo "➕ Menambahkan timeout settings baru..."
    # Tambahkan timeout settings setelah location block
    sudo sed -i '/location \/ {/a\
    # Timeout settings untuk WhatsApp Engine\
    proxy_connect_timeout 600s;\
    proxy_send_timeout 600s;\
    proxy_read_timeout 600s;\
    send_timeout 600s;\
    ' "$NGINX_CONFIG"
fi

# Test konfigurasi nginx
echo "🧪 Testing konfigurasi nginx..."
if sudo nginx -t; then
    echo "✅ Konfigurasi nginx valid!"
    
    # Reload nginx
    echo "🔄 Reload nginx..."
    sudo systemctl reload nginx
    
    if [ $? -eq 0 ]; then
        echo "✅ Nginx berhasil di-reload!"
    else
        echo "❌ Gagal reload nginx!"
        echo "   Coba restart nginx: sudo systemctl restart nginx"
    fi
else
    echo "❌ Konfigurasi nginx tidak valid!"
    echo "   Restore backup: sudo cp /etc/nginx/sites-available/wa-engine.backup.* /etc/nginx/sites-available/wa-engine"
    exit 1
fi

echo ""
echo "🎉 Perbaikan timeout Nginx selesai!"
echo "=================================="
echo ""
echo "📋 Yang sudah diperbaiki:"
echo "   ✅ proxy_connect_timeout: 600s"
echo "   ✅ proxy_send_timeout: 600s"
echo "   ✅ proxy_read_timeout: 600s"
echo "   ✅ send_timeout: 600s"
echo ""
echo "🚀 Sekarang coba buat session WhatsApp lagi!"
echo "   QR code generation seharusnya tidak timeout lagi."
echo ""
echo "📝 Jika masih ada masalah:"
echo "   1. Cek logs nginx: sudo tail -f /var/log/nginx/error.log"
echo "   2. Restart nginx: sudo systemctl restart nginx"
echo "   3. Cek status nginx: sudo systemctl status nginx" 