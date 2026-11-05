<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Appointment $appointment)
    {
        $this->appointment->loadMissing(['patient', 'doctor', 'clinic']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $dateStr = $this->appointment->appointment_date ? $this->appointment->appointment_date->format('Y-m-d') : '';
        $timeStr = $this->appointment->appointment_time ?? '';
        $clinicName = $this->appointment->clinic?->name ?? 'Dental Pro';

        return new Envelope(
            subject: "تأكيد حجز موعدك الطبي ({$dateStr} {$timeStr}) - {$clinicName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-confirmation',
        );
    }

    /**
     * Get the attachments for the message (iCalendar .ics invite with 1-day reminder).
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->appointment->generateIcsContent(), 'appointment-invite.ics')
                ->withMime('text/calendar; charset=UTF-8; method=REQUEST'),
        ];
    }
}
