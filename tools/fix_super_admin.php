<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

// 1. Delete doctor record for Fadi
Doctor::where('email', 'fady2200@gmail.com')
    ->orWhere('name', 'like', '%FADI%')
    ->delete();

// 2. Set FADI as system-only super admin (not attached to any clinic, not a doctor)
User::where('email', 'fady2200@gmail.com')->update([
    'clinic_id' => null,
    'role' => 'admin',
    'roles' => json_encode(['admin']),
    'user_type' => 'administrative',
    'speciality' => null,
    'is_super_admin' => true,
    'is_primary_owner' => false,
]);

// 3. Set admin@dentalcare.com as clinic manager ONLY (not super admin)
User::where('email', 'admin@dentalcare.com')->update([
    'clinic_id' => 1,
    'is_super_admin' => false,
    'is_primary_owner' => true,
]);

echo "Super Admin separation successfully configured!\n";
$fadi = User::where('email', 'fady2200@gmail.com')->first();
echo 'FADI: clinic_id='.var_export($fadi->clinic_id, true).', role='.$fadi->role->value.', is_super_admin='.var_export($fadi->is_super_admin, true)."\n";

$admin = User::where('email', 'admin@dentalcare.com')->first();
echo 'ADMIN: clinic_id='.$admin->clinic_id.', is_super_admin='.var_export($admin->is_super_admin, true).', is_primary_owner='.var_export($admin->is_primary_owner, true)."\n";
