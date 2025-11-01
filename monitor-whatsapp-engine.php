<?php

// Monitor script to keep WhatsApp engine running
echo "WhatsApp Engine Monitor Started\n";
echo "==============================\n";

$enginePath = __DIR__ . '/whatsapp-engine';
$engineProcess = null;
$lastRestart = time();
$restartCount = 0;
$maxRestarts = 10; // Maximum restarts in 1 hour

while (true) {
    // Check if engine is running by pinging health endpoint
    $isRunning = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:3000/health");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $isRunning = true;
    }
    
    if (!$isRunning) {
        $currentTime = time();
        // Only restart if it's been more than 30 seconds since last restart
        if (($currentTime - $lastRestart) > 30) {
            // Check if we've exceeded max restarts in the last hour
            if ($restartCount >= $maxRestarts) {
                echo "[" . date('Y-m-d H:i:s') . "] Maximum restarts reached. Please check the engine logs.\n";
                sleep(60); // Wait longer before trying again
                continue;
            }
            
            echo "[" . date('Y-m-d H:i:s') . "] Engine not responding. Restarting...\n";
            
            // Kill any existing node processes
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                exec('taskkill /F /IM node.exe 2>nul');
            } else {
                // Unix/Linux/Mac
                exec('pkill -f "node server.js" 2>/dev/null');
            }
            
            // Start the engine
            $command = "cd " . escapeshellarg($enginePath) . " && node server.js > engine.log 2>&1 &";
            exec($command);
            
            $lastRestart = $currentTime;
            $restartCount++;
            echo "[" . date('Y-m-d H:i:s') . "] Engine restart command sent (Restart #" . $restartCount . ")\n";
            
            // Wait a bit for startup
            sleep(10);
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] Engine not responding, but waiting before restart (cooldown period)\n";
        }
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] Engine is running\n";
        // Reset restart count if engine has been running for more than 1 hour
        if (($currentTime - $lastRestart) > 3600) {
            $restartCount = 0;
        }
    }
    
    // Wait 30 seconds before next check
    sleep(30);
}