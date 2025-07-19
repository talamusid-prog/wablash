#!/bin/bash

echo "🔧 Membuka file server-optimized.js untuk edit manual..."
echo ""
echo "📝 Instruksi edit:"
echo "1. Cari baris: setTimeout(() => reject(new Error('Initialization timeout')), 60000);"
echo "2. Ganti 60000 menjadi 300000"
echo "3. Ganti '60 seconds timeout' menjadi '5 minutes timeout'"
echo "4. Simpan dengan Ctrl+X, Y, Enter"
echo ""

# Backup file
cp server-optimized.js server-optimized.js.backup

# Buka file dengan nano
nano server-optimized.js

echo ""
echo "✅ Edit selesai!"
echo "🚀 Jalankan server: node server-optimized.js" 