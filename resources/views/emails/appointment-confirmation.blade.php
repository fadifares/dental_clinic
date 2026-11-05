<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد حجز موعدك الطبي - {{ $appointment->clinic->name ?? 'Dental Pro' }}</title>
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
            background: linear-gradient(135deg, #0b1329 0%, #0284c7 60%, #0d9488 100%);
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
        .status-badge {
            display: inline-block;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50px;
            margin-bottom: 16px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
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
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 24px;
        }
        .card-header-badge {
            display: inline-block;
            background: #0284c7;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
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
        .calendar-notice {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 13.5px;
            color: #1e40af;
            line-height: 1.6;
        }
        .calendar-btn-wrap {
            text-align: center;
            margin: 20px 0 24px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        .calendar-btn {
            display: inline-block;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            padding: 12px 28px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }
        .whatsapp-btn {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 24px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            margin-top: 8px;
        }
        .tips-box {
            background: #fefce8;
            border: 1px solid #fef08a;
            border-radius: 12px;
            padding: 16px 20px;
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
        <h1 class="clinic-brand">🦷 {{ $appointment->clinic->name ?? 'Dental Pro Clinic' }}</h1>
        <div class="clinic-tagline">المنظومة الذكية لطب وجراحة وتجميل الأسنان</div>
    </div>

    <!-- Body -->
    <div class="email-body">
        <div class="status-badge">
            ✓ تم تأكيد الحجز بنجاح
        </div>

        <div class="greeting">
            مرحباً بك عزيزنا المراجع {{ $appointment->patient->name ?? '' }}،
        </div>
        <p class="intro-text">
            يسعدنا إبلاغك بأنه تم تأكيد موعد جلستك الطبية القادمة بنجاح في عيادتنا. لقد قمنا بتجهيز العيادة والفريق الطبي لتقديم أرقى مستويات الرعاية الصحية.
        </p>

        <!-- Appointment Card -->
        <div class="appointment-card">
            <span class="card-header-badge">تفاصيل الجلسة الطبية</span>

            <div class="detail-row">
                <span class="detail-label">📅 تاريخ الموعد:</span>
                <span class="detail-value">{{ $appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : 'اليوم المحدد' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">⏰ التوقيت المحدد:</span>
                <span class="detail-value rtl-text">
                    @php
                        $parsedTime = null;
                        try {
                            $parsedTime = \Carbon\Carbon::parse($appointment->appointment_time);
                        } catch(\Throwable $e) {}
                    @endphp
                    @if($parsedTime)
                        {{ $parsedTime->format('h:i') }} {{ $parsedTime->format('A') === 'PM' ? 'مساءً' : 'صباحاً' }}
                        <span style="font-size: 12px; color: #64748b;">({{ $appointment->appointment_time }})</span>
                    @else
                        {{ $appointment->appointment_time ?? '12:00' }}
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">👨‍⚕️ الطبيب المعالج:</span>
                <span class="detail-value rtl-text">
                    @php
                        $docName = $appointment->doctor ? $appointment->doctor->name : 'طبيب العيادة المختص';
                        $docDisplay = str_starts_with(trim($docName), 'د.') ? trim($docName) : 'د. ' . trim($docName);
                    @endphp
                    {{ $docDisplay }} ({{ $appointment->doctor->speciality ?? 'طب أسنان' }})
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">🩺 نوع الإجراء الطبي:</span>
                <span class="detail-value rtl-text">{{ $appointment->service_type ?? 'كشف واستشارة طبية' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">📁 رقم الملف الطبي:</span>
                <span class="detail-value">#{{ $appointment->patient->file_number ?? '-' }}</span>
            </div>

            @if($appointment->notes)
            <div class="detail-row">
                <span class="detail-label">📝 ملاحظات خاصة:</span>
                <span class="detail-value rtl-text">{{ $appointment->notes }}</span>
            </div>
            @endif
        </div>

        <!-- Calendar Notice & Google Calendar Button -->
        <div class="calendar-notice">
            <strong>📅 ميزة الحفظ التلقائي في التقويم والتنبيه:</strong><br>
            تم إرفاق ملف التقويم التفاعلي <strong>(appointment-invite.ics)</strong> بهذا البريد الإلكتروني.
            عند فتحه على هاتفك الذكي (iPhone / Android) أو بريدك، سيتم حفظ الموعد في تقويمك تلقائياً مع <strong>تنبيه تذكيري مسبق قبل الموعد بيوم واحد (24 ساعة)</strong>.
        </div>

        <div class="calendar-btn-wrap">
            <a href="{{ $appointment->getGoogleCalendarUrl() }}" target="_blank" class="calendar-btn">
                📅 إضافة الموعد إلى تقويم Google بنقرة واحدة
            </a>
            
            <a href="{{ $appointment->getWhatsAppUrl() }}" target="_blank" class="whatsapp-btn">
                💬 التواصل المباشر مع العيادة عبر واتساب
            </a>
        </div>

        <!-- Clinical Guidelines -->
        <div class="tips-box">
            <strong>💡 إرشادات هامة قبل الزيارة:</strong>
            <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                <li>يرجى الحضور قبل الموعد بـ 10 دقائق لتسجيل الدخول وإنهاء إجراءات الاستقبال.</li>
                <li>إذا كنت تتناول أي أدوية منتظمة أو تعاني من أمراض مزمنة أو حساسية، يرجى إبلاغ الطبيب المعالج.</li>
                <li>في حال رغبتكم في تعديل الموعد، يُرجى التواصل معنا عبر الهاتف أو الواتساب قبل الموعد بـ 6 ساعات على الأقل.</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="email-footer">
        <div>نسعد دائماً بخدمتكم ونتمنى لكم دوام الصحة والعافية وابتسامة مشرقة.</div>
        <div style="margin-top: 6px;">لأي استفسار أو تعديل، يمكنكم التواصل مع قسم الاستقبال:</div>
        <div class="footer-phone">📞 {{ $appointment->clinic->phone ?? '0500000000' }}</div>
        <div style="margin-top: 12px; color: #94a3b8; font-size: 11px;">
            تم إصدار هذه الرسالة وبطاقة التقويم بواسطة نظام إدارة العيادات الذكي {{ $appointment->clinic->name ?? 'Dental Pro' }} &copy; {{ date('Y') }}
        </div>
    </div>
</div>

</body>
</html>

