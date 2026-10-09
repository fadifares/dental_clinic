<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكير بموعدك الطبي - Dental Pro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #1e293b;
            direction: rtl;
            text-align: right;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #0b1329 0%, #0284c7 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .clinic-brand {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0;
            display: inline-block;
        }
        .clinic-tagline {
            font-size: 13px;
            color: #bae6fd;
            margin-top: 6px;
        }
        .email-body {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0f172a;
        }
        .intro-text {
            font-size: 15px;
            line-height: 1.7;
            color: #475569;
            margin-bottom: 24px;
        }
        .appointment-card {
            background: #f8fafc;
            border: 2px solid #e0f2fe;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .card-header-badge {
            display: inline-block;
            background: #0284c7;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 50px;
            margin-bottom: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #cbd5e1;
            font-size: 14px;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #64748b;
            font-weight: 600;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 700;
            direction: ltr;
            text-align: left;
        }
        .detail-value.rtl-text {
            direction: rtl;
            text-align: right;
        }
        .calendar-btn-wrap {
            text-align: center;
            margin: 28px 0;
        }
        .calendar-btn {
            display: inline-block;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            padding: 14px 28px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        .tips-box {
            background: #fefce8;
            border: 1px solid #fef08a;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13px;
            color: #854d0e;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .email-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }
        .footer-phone {
            font-size: 14px;
            font-weight: bold;
            color: #0284c7;
            margin-top: 4px;
            display: inline-block;
            direction: ltr;
        }
    </style>
</head>
<body>

<div class="email-container">
    <!-- Header -->
    <div class="email-header">
        <h1 class="clinic-brand">🦷 Dental Pro Clinic</h1>
        <div class="clinic-tagline">المركز المتخصص لطب وجراحة وتجميل الأسنان</div>
    </div>

    <!-- Body -->
    <div class="email-body">
        <div class="greeting">
            مرحباً بك عزيزنا المراجع {{ $appointment->patient->name }}،
        </div>
        <p class="intro-text">
            نود تذكيرك بموعد جلستك الطبية القادمة في عيادتنا. لقد قمنا بتجهيز العيادة والفريق الطبي لتقديم أفضل رعاية لابتسامتك.
        </p>

        <!-- Appointment Card -->
        <div class="appointment-card">
            <span class="card-header-badge">تفاصيل الموعد الطبي</span>

            <div class="detail-row">
                <span class="detail-label">📅 تاريخ الموعد:</span>
                <span class="detail-value">{{ $appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : 'اليوم المحدد' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">⏰ التوقيت المحدد:</span>
                <span class="detail-value">{{ $appointment->appointment_time ?? '00:00' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">👨‍⚕️ الطبيب المعالج:</span>
                <span class="detail-value rtl-text">{{ $appointment->doctor ? $appointment->doctor->name : 'طبيب العيادة المختص' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">🩺 نوع الخدمة / الإجراء:</span>
                <span class="detail-value rtl-text">{{ $appointment->service_type ?? 'كشف واستشارة طبية' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">📁 رقم الملف الطبي:</span>
                <span class="detail-value">{{ $appointment->patient->file_number }}</span>
            </div>
        </div>

        <!-- Google Calendar Sync Link -->
        @php
            $date = $appointment->appointment_date ? $appointment->appointment_date->format('Ymd') : date('Ymd');
            $timeRaw = str_replace(':', '', $appointment->appointment_time ?? '1000');
            $timeFormatted = str_pad(substr($timeRaw, 0, 4), 4, '0') . '00';
            $calStart = $date . 'T' . $timeFormatted;
            $calTitle = urlencode('موعد في عيادة الأسنان Dental Pro');
            $calDetails = urlencode('موعد مع د. ' . ($appointment->doctor->name ?? 'طبيب الأسنان') . ' - خدمة: ' . ($appointment->service_type ?? 'كشف أسنان'));
            $calLocation = urlencode('مركز دنتال برو لطب الأسنان');
            $googleCalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$calTitle}&dates={$calStart}/{$calStart}&details={$calDetails}&location={$calLocation}";
        @endphp

        <div class="calendar-btn-wrap">
            <a href="{{ $googleCalUrl }}" target="_blank" class="calendar-btn">
                📅 إضافة الموعد إلى تقويم Google
            </a>
        </div>

        <!-- Clinical Guidelines -->
        <div class="tips-box">
            <strong>💡 إرشادات الزيارة:</strong>
            <ul style="margin: 6px 0 0 0; padding-right: 20px;">
                <li>يرجى التفضل بالحضور قبل الموعد بـ 10 دقائق لتسجيل الوصول في قسم الاستقبال.</li>
                <li>إذا كنت تتناول أي أدوية بانتظام أو تعاني من حساسيات جديدة، يرجى إبلاغ الطبيب المعالج.</li>
                <li>في حال رغبتكم في تعديل الموعد، يُرجى التواصل معنا قبل الموعد بـ 6 ساعات على الأقل.</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="email-footer">
        <div>نسعد دائماً بخدمتكم ونتمنى لكم دوام الصحة والعافية وابتسامة مشرقة.</div>
        <div style="margin-top: 6px;">لأي استفسار أو تعديل، يمكنكم التواصل المباشر مع الاستقبال:</div>
        <div class="footer-phone">📞 920000000 / 0500000000</div>
        <div style="margin-top: 12px; color: #94a3b8; font-size: 11px;">
            تم إرسال هذا التذكير المؤتمت بواسطة نظام إدارة العيادات الذكي Dental Pro ERP &copy; {{ date('Y') }}
        </div>
    </div>
</div>

</body>
</html>
