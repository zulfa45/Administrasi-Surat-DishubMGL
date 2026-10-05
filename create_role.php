<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Role;

if (!Role::where('name', 'kepala_bidang')->exists()) {
    Role::create(['name' => 'kepala_bidang']);
    echo "Role kepala_bidang created.\n";
} else {
    echo "Role kepala_bidang already exists.\n";
}
