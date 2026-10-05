<?php

namespace App\Console\Commands;

use App\Models\Hearing;
use App\Models\User;
use App\Notifications\HearingReminderNotification;
use Illuminate\Console\Command;

class RemindHearingsCommand extends Command
{
    protected $signature = 'firm:remind-hearings {--days= : Days ahead (defaults to config firm.hearing_reminder_days)}';

    protected $description = 'Send reminders for upcoming hearings to the attending lawyer, the lead lawyer and the client portal user.';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('firm.hearing_reminder_days'));
        $target = now()->addDays($days)->toDateString();

        $hearings = Hearing::query()
            ->upcoming()
            ->where('reminder_sent', false)
            ->whereDate('scheduled_at', '<=', $target)
            ->with(['legalCase.client.portalAccount', 'legalCase.leadLawyer', 'legalCase.court', 'lawyer'])
            ->get();

        foreach ($hearings as $hearing) {
            $recipients = collect([$hearing->lawyer, $hearing->legalCase->leadLawyer])
                ->filter()
                ->unique('id');

            if ($hearing->visible_to_client && $hearing->legalCase->visible_to_client) {
                $portalUser = $hearing->legalCase->client?->portalAccount;

                if ($portalUser instanceof User) {
                    $recipients->push($portalUser);
                }
            }

            $recipients->each(fn (User $user) => $user->notify(new HearingReminderNotification($hearing)));

            $hearing->update(['reminder_sent' => true]);
        }

        $this->info("Reminders sent for {$hearings->count()} hearing(s).");

        return self::SUCCESS;
    }
}
