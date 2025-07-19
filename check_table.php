<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Auto Warmers Table Structure ===\n";

try {
    $columns = Schema::getColumnListing('auto_warmers');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    // Get table info
    $tableInfo = DB::select("DESCRIBE auto_warmers");
    echo "\nTable Info:\n";
    foreach ($tableInfo as $column) {
        echo "- {$column->Field}: {$column->Type} {$column->Null} {$column->Key} {$column->Default}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 