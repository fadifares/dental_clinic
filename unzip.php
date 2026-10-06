<?php
/**
 * Dental Clinic Auto-Extractor for Hostinger
 */
$zipFile = __DIR__ . '/dental_clinic.zip';
$extractTo = __DIR__;

header('Content-Type: text/html; charset=utf-8');

if (!file_exists($zipFile)) {
    die("<h3 style='color:red;'>خطأ: ملف dental_clinic.zip غير موجود في " . htmlspecialchars(__DIR__) . "</h3>");
}

$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "<div style='font-family: Arial; padding: 20px; direction: rtl; text-align: right;'>";
    echo "<h2 style='color: #10b981;'>✅ تم فك ضغط ونشر كافة ملفات نظام عيادة الأسنان بنجاح!</h2>";
    echo "<p>مسار المجلد: <code>" . htmlspecialchars(__DIR__) . "</code></p>";
    echo "<p>يمكنك الآن حذف ملف <code>unzip.php</code> وملف <code>dental_clinic.zip</code> بعد التأكد من عمل الموقع.</p>";
    echo "</div>";
} else {
    echo "<h3 style='color:red;'>تعذر فك ضغط الملف عبر PHP. يمكنك فك ضغطه بنقرة واحدة من إدارة ملفات Hostinger (File Manager).</h3>";
}
