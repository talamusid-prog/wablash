@echo off
echo ========================================
echo    UPLOAD WHATSAPP BLAST TO GITHUB
echo ========================================
echo.

echo [1/6] Checking Git status...
git status
echo.

echo [2/6] Creating .gitignore to exclude large files...
if not exist .gitignore (
    echo # Laravel
    echo /vendor/
    echo /node_modules/
    echo /storage/logs/
    echo /storage/framework/cache/
    echo /storage/framework/sessions/
    echo /storage/framework/views/
    echo /storage/app/public/
    echo .env
    echo .env.backup
    echo .env.production
    echo .phpunit.result.cache
    echo Homestead.json
    echo Homestead.yaml
    echo npm-debug.log
    echo yarn-error.log
    echo .idea/
    echo .vscode/
    echo *.log
    echo *.cache
    echo .DS_Store
    echo Thumbs.db
    echo.
    echo # WhatsApp Engine
    echo whatsapp-engine/node_modules/
    echo whatsapp-engine/uploads/
    echo whatsapp-engine/*.zip
    echo whatsapp-engine/package-lock.json
    echo whatsapp-engine/package-optimized.json
    echo whatsapp-engine/optimization-config.js
    echo whatsapp-engine/server-optimized.js
    echo whatsapp-engine/*.sh
    echo whatsapp-engine/*.bat
    echo.
    echo # Deployment
    echo deployment/
    echo .zeabur/
    echo.
    echo # Temporary files
    echo *.tmp
    echo *.temp
    echo .env_bc
    echo check_table.php
) > .gitignore
echo .gitignore created successfully!
echo.

echo [3/6] Removing large files from Git tracking...
git rm -r --cached whatsapp-engine/node_modules/ 2>nul
git rm -r --cached vendor/ 2>nul
git rm -r --cached storage/logs/ 2>nul
git rm -r --cached storage/framework/cache/ 2>nul
git rm -r --cached storage/framework/sessions/ 2>nul
git rm -r --cached storage/framework/views/ 2>nul
git rm -r --cached deployment/ 2>nul
git rm --cached .env_bc 2>nul
git rm --cached check_table.php 2>nul
echo Large files removed from tracking!
echo.

echo [4/6] Adding files to Git...
git add .
echo Files added successfully!
echo.

echo [5/6] Committing changes...
git commit -m "Upload WhatsApp Blast Application - Optimized for GitHub"
echo Commit completed!
echo.

echo [6/6] Pushing to GitHub...
echo Attempting to push to https://github.com/haerulhadi/whatsapp.git
git push -u origin master
echo.

if %ERRORLEVEL% EQU 0 (
    echo ========================================
    echo    UPLOAD BERHASIL!
    echo ========================================
    echo Repository: https://github.com/haerulhadi/whatsapp.git
    echo.
    echo File yang diupload:
    echo - Laravel Application (tanpa vendor/)
    echo - WhatsApp Engine (tanpa node_modules/)
    echo - Database Migrations
    echo - API Documentation
    echo - Configuration Files
    echo - Frontend Views
    echo.
    echo Catatan: node_modules dan file besar lainnya tidak diupload
    echo untuk menghemat ukuran repository.
) else (
    echo ========================================
    echo    UPLOAD GAGAL!
    echo ========================================
    echo Coba solusi alternatif:
    echo 1. Gunakan GitHub CLI: gh repo create
    echo 2. Upload manual via GitHub web interface
    echo 3. Gunakan Git LFS untuk file besar
)

echo.
pause 