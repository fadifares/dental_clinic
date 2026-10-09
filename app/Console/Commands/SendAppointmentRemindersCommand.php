<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[Signature('clinic:send-reminders {--date= : The date to check for appointments (Y-m-d, defaults to tomorrow)} {--force : Re-send even if already marked as sent}')]
#[Description('Send automated email reminders to patients for upcoming clinic appointments')]
class SendAppointmentRemindersCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateParam = $this->option('date');
        $targetDate = $dateParam ? Carbon::parse($dateParam)->format('Y-m-d') : Carbon::tomorrow()->format('Y-m-d');
        $force = (bool) $this->option('force');

        $this->info("Scanning appointments for target date: {$targetDate}...");

        $query = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $targetDate)
            ->whereIn('status', ['scheduled', 'confirmed']);

        if (! $force) {
            $query->whereNull('reminder_sent_at');
        }

        $appointments = $query->get();

        if ($appointments->isEmpty()) {
            $this->info("No pending appointments found for date: {$targetDate}.");

            return self::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($appointments as $appointment) {
            $patient = $appointment->patient;

            if (! $patient || empty($patient->email)) {
                $this->warn("Skipped Appointment #{$appointment->id} - Patient '{$patient?->name}' has no registered email.");
                $skippedCount++;

                continue;
            }

            try {
                Mail::to($patient->email)->send(new AppointmentReminderMail($appointment));

                $appointment->update([
                    'reminder_sent_at' => now(),
                ]);

                $this->info("✓ Sent email reminder to {$patient->name} ({$patient->email}) for Appointment #{$appointment->id}.");
                Log::info("Automated appointment reminder email sent to {$patient->email} for appointment #{$appointment->id}");
                $sentCount++;
            } catch (\Throwable $e) {
                $this->error("✗ Failed sending email to {$patient->email} for Appointment #{$appointment->id}: ".$e->getMessage());
                Log::error('Failed to send appointment reminder email: '.$e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'patient_email' => $patient->email,
                ]);
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("Summary: {$sentCount} reminders sent, {$skippedCount} skipped (no email), {$failedCount} failed.");

        return self::SUCCESS;
    }
}
