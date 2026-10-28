<?php

use Illuminate\Contracts\Console\Kernel;

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
    $status = $kernel->call('migrate', ['--force' => true]);
    echo htmlspecialchars($kernel->output());
    echo '</pre>';
    echo '<p class="success">✅ تمت عملية ترقية قاعدة البيانات وترحيل العيادة الأولى والمستخدمين بنجاح!</p>';
} catch (Throwable $e) {
    echo '</pre>';
    echo '<p style="color:#ef4444;">❌ حدث خطأ أثناء التحديث: '.htmlspecialchars($e->getMessage()).'</p>';
}

echo '</div></body></html>';
