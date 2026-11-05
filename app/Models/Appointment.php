<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use BelongsToClinic, HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Determine if the appointment date is before today (past appointment).
     */
    public function isPast(): bool
    {
        if (! $this->appointment_date) {
            return false;
        }

        return $this->appointment_date->toDateString() < now()->toDateString();
    }

    /**
     * Determine if the appointment date is today or in the future.
     */
    public function isUpcoming(): bool
    {
        return ! $this->isPast();
    }

    /**
     * Determine if the appointment is active and eligible for editing or cancellation.
     */
    public function canBeEdited(): bool
    {
        return $this->isUpcoming() && ! in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get the timezone for the appointment/clinic.
     */
    public function getTimezone(): string
    {
        return config('app.timezone') ?: 'Africa/Cairo';
    }

    /**
     * Get carbon start datetime of the appointment in local clinic timezone.
     */
    public function getStartDateTime(): Carbon
    {
        $dateStr = $this->appointment_date ? $this->appointment_date->format('Y-m-d') : now()->toDateString();
        $timeStr = $this->appointment_time ?? '12:00:00';
        $tz = $this->getTimezone();

        try {
            return Carbon::parse("{$dateStr} {$timeStr}", $tz);
        } catch (\Throwable) {
            return now($tz);
        }
    }

    /**
     * Get carbon end datetime (defaults to 45 mins after start) in local clinic timezone.
     */
    public function getEndDateTime(): Carbon
    {
        return $this->getStartDateTime()->copy()->addMinutes(45);
    }

    /**
     * Generate standard RFC 5545 iCalendar content with 1-day reminder alarm.
     */
    public function generateIcsContent(): string
    {
        $start = $this->getStartDateTime();
        $end = $this->getEndDateTime();
        $tz = $this->getTimezone();
        $patientName = $this->patient?->name ?? 'المريض';
        $patientEmail = $this->patient?->email ?? '';
        $rawDoctor = (string) ($this->doctor?->name ?? 'طبيب الأسنان');
        $doctorName = str_starts_with(trim($rawDoctor), 'د.') ? trim($rawDoctor) : 'د. '.trim($rawDoctor);
        $clinicName = $this->clinic?->name ?? 'مركز Dental Pro لطب الأسنان';
        $clinicAddress = $this->clinic?->address ?? 'الفرع الرئيسي';
        $service = $this->service_type ?? 'كشف واستشارة طبية';

        $summary = "موعد أسنان: {$service} - {$doctorName}";
        $description = "موعد طبي في عيادة الأسنان\nالمريض: {$patientName}\nالطبيب: {$doctorName}\nنوع الإجراء: {$service}\nيرجى الحضور قبل الموعد بـ 10 دقائق.\nالعيادة: {$clinicName}";
        $location = "{$clinicName} - {$clinicAddress}";

        $uid = 'apt-'.$this->id.'@dental.go-2web.com';
        $dtStamp = gmdate('Ymd\THis\Z');
        $sequence = $this->updated_at ? max(0, $this->updated_at->timestamp - ($this->created_at?->timestamp ?? $this->updated_at->timestamp)) : 0;

        $organizerEmail = config('mail.from.address') ?: 'app@go-2web.com';
        $organizerName = config('mail.from.name') ?: $clinicName;

        $escape = function ($str) {
            return addcslashes((string) $str, ",;\\\n\r");
        };

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Dental Pro ERP//Appointments//AR',
            'CALSCALE:GREGORIAN',
            'METHOD:REQUEST',
            'BEGIN:VTIMEZONE',
            'TZID:Africa/Cairo',
            'X-LIC-LOCATION:Africa/Cairo',
            'BEGIN:STANDARD',
            'DTSTART:20231027T000000',
            'TZOFFSETFROM:+0300',
            'TZOFFSETTO:+0200',
            'TZNAME:EET',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:20240426T000000',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0300',
            'TZNAME:EEST',
            'END:DAYLIGHT',
            'END:VTIMEZONE',
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'SEQUENCE:'.$sequence,
            'DTSTAMP:'.$dtStamp,
            'DTSTART;TZID=Africa/Cairo:'.$start->format('Ymd\THis'),
            'DTEND;TZID=Africa/Cairo:'.$end->format('Ymd\THis'),
            'ORGANIZER;CN='.$escape($organizerName).':mailto:'.$organizerEmail,
        ];

        if (! empty($patientEmail)) {
            $lines[] = 'ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN='.$escape($patientName).':mailto:'.$patientEmail;
        }

        $lines = array_merge($lines, [
            'SUMMARY:'.$escape($summary),
            'DESCRIPTION:'.$escape($description),
            'LOCATION:'.$escape($location),
            'STATUS:CONFIRMED',
            'BEGIN:VALARM',
            'TRIGGER:-P1D',
            'ACTION:DISPLAY',
            'DESCRIPTION:'.$escape("تذكير: موعدك الطبي غداً في عيادة الأسنان ({$service}) مع {$doctorName}"),
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return implode("\r\n", $lines);
    }

    /**
     * Get direct WhatsApp URL to send appointment confirmation to patient.
     */
    public function getWhatsAppUrl(): string
    {
        $rawPhone = $this->patient?->phone ?? '';
        $phone = preg_replace('/[^0-9]/', '', (string) $rawPhone);

        // Normalize Saudi numbers (05... -> 9665...)
        if (str_starts_with($phone, '05') && strlen($phone) === 10) {
            $phone = '966'.substr($phone, 1);
        }
        // Normalize Egypt numbers (01... -> 201...)
        elseif (str_starts_with($phone, '01') && strlen($phone) === 11) {
            $phone = '20'.substr($phone, 1);
        }
        // If local 9-digit without leading 0 for Saudi (e.g. 501234567)
        elseif (str_starts_with($phone, '5') && strlen($phone) === 9) {
            $phone = '966'.$phone;
        }

        $patientName = $this->patient?->name ?? 'عزيزنا المراجع';
        $rawDoctor = (string) ($this->doctor?->name ?? 'طبيب العيادة');
        $doctorName = str_starts_with(trim($rawDoctor), 'د.') ? trim($rawDoctor) : 'د. '.trim($rawDoctor);
        $doctorSpec = $this->doctor?->speciality ?? 'طب وجراحة الأسنان';
        $clinicName = $this->clinic?->name ?? 'مركز Dental Pro لطب الأسنان';
        $clinicAddress = $this->clinic?->address ?? 'الفرع الرئيسي';
        $service = $this->service_type ?? 'كشف واستشارة طبية';
        $dateStr = $this->appointment_date ? $this->appointment_date->format('Y-m-d') : date('Y-m-d');

        $rawTime = $this->appointment_time ?? '12:00';
        $timeParsed = null;
        try {
            $timeParsed = Carbon::parse($rawTime);
            $timePeriod = $timeParsed->format('A') === 'PM' ? 'مساءً' : 'صباحاً';
            $timeFormatted = $timeParsed->format('h:i').' '.$timePeriod;
        } catch (\Throwable) {
            $timeFormatted = $rawTime;
        }
        $fileNum = $this->patient?->file_number ?? '-';

        $msg = "🦷 *تأكيد حجز موعد طبي - {$clinicName}*\n\n"
             ."مرحباً بك عزيزنا *{$patientName}*،\n"
             ."تم تأكيد حجز موعد جلستك القادمة بنجاح وفق التفاصيل التالية:\n\n"
             ."👨‍⚕️ *الطبيب المعالج:* {$doctorName} ({$doctorSpec})\n"
             ."📅 *التاريخ:* {$dateStr}\n"
             ."⏰ *الوقت:* {$timeFormatted}\n"
             ."🩺 *نوع الإجراء:* {$service}\n"
             ."📁 *رقم الملف:* #{$fileNum}\n"
             ."📍 *الموقع:* {$clinicAddress}\n\n"
             ."💡 *ملاحظة:* نرجو التكرم بالحضور قبل الموعد بـ 10 دقائق لتسجيل الدخول.\n"
             ."📅 تم إرسال بطاقة التقويم التفاعلية إلى بريدك الإلكتروني مع تذكير تلقائي قبل الموعد بيوم واحد.\n\n"
             .'نتمنى لك دوام الصحة والعافية وابتسامة مشرقة! ✨';

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($msg);
    }

    /**
     * Get direct Google Calendar 1-click URL.
     */
    public function getGoogleCalendarUrl(): string
    {
        $start = $this->getStartDateTime();
        $end = $this->getEndDateTime();
        $tz = $this->getTimezone();

        $startStr = $start->format('Ymd\THis');
        $endStr = $end->format('Ymd\THis');

        $title = urlencode('موعد في عيادة الأسنان - '.($this->service_type ?? 'كشف'));
        $details = urlencode('موعد طبي مع '.($this->doctor?->name ?? 'طبيب الأسنان')."\nالخدمة: ".($this->service_type ?? 'كشف أسنان')."\nالمريض: ".($this->patient?->name ?? ''));
        $location = urlencode(($this->clinic?->name ?? 'عيادة الأسنان').' - '.($this->clinic?->address ?? ''));

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$startStr}/{$endStr}&ctz={$tz}&details={$details}&location={$location}";
    }
}
