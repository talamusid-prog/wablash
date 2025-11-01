<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Register the debug command
Artisan::command('whatsapp:debug-session {session_id?}', function ($session_id = null) {
    $this->call('whatsapp:debug-session', ['session_id' => $session_id]);
});
