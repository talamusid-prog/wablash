#!/bin/bash

echo "🔧 Quick Fix: Mengubah timeout di server-optimized.js..."

# Backup file original
cp server-optimized.js server-optimized.js.backup

# Perbaiki timeout menggunakan sed
sed -i 's/60000/300000/g' server-optimized.js
sed -i 's/60 seconds timeout/5 minutes timeout/g' server-optimized.js

echo "✅ Timeout berhasil diubah dari 60 detik menjadi 300 detik!"
echo ""
echo "🚀 Jalankan server:"
echo "   node server-optimized.js"
echo ""
echo "📝 Jika ada masalah, restore backup:"
echo "   cp server-optimized.js.backup server-optimized.js" 