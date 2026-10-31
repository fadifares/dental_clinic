<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Direct Migration Runner for Shared Hosting (Hostinger)
 * Allows executing Artisan migrate safely from browser.
 */

// Simple protection key
$secretKey = 'dental_saas_update_2026';

if (! isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    exit('<div style="font-family:sans-serif;padding:30px;color:red;text-align:center;"><h2>403 Forbidden</h2><p>Access denied. Valid security key required.</p></div>');
}

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

echo '<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تحديث قاعدة البيانات - Dental SaaS</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }
        .card { max-width: 800px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; font-size: 20px; border-bottom: 1px solid #334155; padding-bottom: 12px; }
        pre { background: #090d16; color: #4ade80; padding: 16px; border-radius: 8px; overflow-x: auto; font-family: Consolas, monospace; }
        .success { color: #34d399; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
<div class="card">
    <h1>🚀 ترقية قاعدة بيانات Dental SaaS على الاستضافة</h1>
    <p>جاري تشغيل أمر: <code>php artisan migrate --force</code> ...</p>
    <pre>';

try {
    $kernel->call('migrate:status');
    echo "=== MIGRATE STATUS ===\n";
    echo htmlspecialchars($kernel->output());
    echo "\n=== RUNNING MIGRATE ===\n";
    $status = $kernel->call('migrate', ['--force' => true]);
    echo htmlspecialchars($kernel->output());

    // Check if last_login_at exists on users table
    $columns = Schema::getColumnListing('users');
    echo "\n=== USERS TABLE COLUMNS ===\n".implode(', ', $columns)."\n";

    if (! in_array('last_login_at', $columns)) {
        echo "\nAdding missing columns to users table...\n";
        Schema::table('users', function ($table) use ($columns) {
            if (! in_array('clinic_id', $columns)) {
                $table->unsignedBigInteger('clinic_id')->nullable()->after('id');
            }
            if (! in_array('is_super_admin', $columns)) {
                $table->boolean('is_super_admin')->default(false)->after('role');
            }
            if (! in_array('is_primary_owner', $columns)) {
                $table->boolean('is_primary_owner')->default(false)->after('is_super_admin');
            }
            if (! in_array('last_login_at', $columns)) {
                $table->dateTime('last_login_at')->nullable()->after('is_active');
            }
            if (! in_array('last_login_ip', $columns)) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });
        echo "Successfully added missing columns to users table!\n";
    }

    // Check operational tables for clinic_id
    $tables = ['patients', 'doctors', 'appointments', 'dental_charts', 'lab_orders', 'invoices', 'treatment_plans', 'expenses', 'settings'];
    foreach ($tables as $tbl) {
        if (Schema::hasTable($tbl)) {
            $cols = Schema::getColumnListing($tbl);
            if (! in_array('clinic_id', $cols)) {
                echo "Adding clinic_id to $tbl...\n";
                Schema::table($tbl, function ($table) {
                    $table->unsignedBigInteger('clinic_id')->nullable();
                });
            }
        }
    }

    // Ensure clinics table exists
    if (! Schema::hasTable('clinics')) {
        echo "\nCreating clinics table...\n";
        Schema::create('clinics', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('status', 50)->default('active');
            $table->unsignedInteger('max_users')->default(10);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    // Ensure default clinic exists
    $defaultClinic = DB::table('clinics')->find(1);
    if (! $defaultClinic) {
        echo "Creating default Clinic #1...\n";
        DB::table('clinics')->insert([
            'id' => 1,
            'name' => 'العيادة الأولى',
            'slug' => 'clinic-1',
            'status' => 'active',
            'max_users' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Ensure clinic_page_visits table exists
    if (! Schema::hasTable('clinic_page_visits')) {
        echo "Creating clinic_page_visits table...\n";
        Schema::create('clinic_page_visits', function ($table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('route_name')->nullable();
            $table->string('url_path');
            $table->string('page_title')->nullable();
            $table->string('method', 10)->default('GET');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    // Ensure clinic_user_activities table exists
    if (! Schema::hasTable('clinic_user_activities')) {
        echo "Creating clinic_user_activities table...\n";
        Schema::create('clinic_user_activities', function ($table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action_type', 50);
            $table->text('description');
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    // Associate unassigned users and data to Clinic #1
    DB::table('users')->whereNull('clinic_id')->where('is_super_admin', false)->update(['clinic_id' => 1]);
    foreach ($tables as $tbl) {
        if (Schema::hasTable($tbl)) {
            DB::table($tbl)->whereNull('clinic_id')->update(['clinic_id' => 1]);
        }
    }

    // Ensure primary owner flag on admin@dentalcare.com
    DB::table('users')->where('email', 'admin@dentalcare.com')->update([
        'clinic_id' => 1,
        'is_primary_owner' => true,
        'is_super_admin' => false,
    ]);

    // Ensure Super Admin FADI D ABUHAMDA is independent
    DB::table('users')->where('email', 'fady2200@gmail.com')->update([
        'clinic_id' => null,
        'is_super_admin' => true,
        'is_primary_owner' => false,
    ]);

    echo "\n=== ALL CHECKS & POPULATIONS COMPLETE ===\n";

    echo '</pre>';
    echo '<p class="success">✅ تمت عملية ترقية قاعدة البيانات وترحيل العيادة الأولى والمستخدمين بنجاح!</p>';
} catch (Throwable $e) {
    echo '</pre>';
    echo '<p style="color:#ef4444;">❌ حدث خطأ أثناء التحديث: '.htmlspecialchars($e->getMessage()).'</p>';
}

echo '</div></body></html>';
