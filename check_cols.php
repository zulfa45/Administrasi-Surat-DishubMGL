<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$type1 = Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnType('incoming_letters', 'status');
$type2 = Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnType('assignments', 'status');

echo "Incoming_letters status: " . $type1 . "\n";
echo "Assignments status: " . $type2 . "\n";
