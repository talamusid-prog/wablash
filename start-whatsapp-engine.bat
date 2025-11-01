@echo off
cd /d "c:\Users\melis\Herd\wa-blast\whatsapp-engine"
echo Starting WhatsApp Engine...
echo Log file: engine.log
node server.js > engine.log 2>&1
echo WhatsApp Engine has stopped.
echo Check engine.log for details.
pause