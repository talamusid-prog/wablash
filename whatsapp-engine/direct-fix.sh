#!/bin/bash

echo "🔧 Memperbaiki server-optimized.js secara langsung..."

# Perbaiki timeout dari 60 detik menjadi 300 detik
echo "⏱️  Mengubah timeout dari 60 detik menjadi 300 detik..."
sed -i 's/setTimeout(() => reject(new Error('\''Initialization timeout'\'')), 60000);/setTimeout(() => reject(new Error('\''Initialization timeout'\'')), 300000); \/\/ 5 minutes timeout/' server-optimized.js

# Perbaiki konfigurasi Puppeteer untuk menambahkan args yang lebih optimal
echo "🖥️  Memperbaiki konfigurasi Puppeteer..."
sed -i 's/--disable-features=VizDisplayCompositor/--disable-features=VizDisplayCompositor,\n        '\''--disable-extensions'\'',\n        '\''--disable-plugins'\'',\n        '\''--disable-images'\'',\n        '\''--disable-javascript'\''/' server-optimized.js

echo "✅ File server-optimized.js berhasil diperbaiki!"
echo ""
echo "📋 Perbaikan yang dilakukan:"
echo "   1. ✅ Timeout diubah dari 60 detik menjadi 300 detik (5 menit)"
echo "   2. ✅ Konfigurasi Puppeteer dioptimasi untuk Ubuntu server"
echo ""
echo "🚀 Sekarang coba jalankan server:"
echo "   node server-optimized.js" 