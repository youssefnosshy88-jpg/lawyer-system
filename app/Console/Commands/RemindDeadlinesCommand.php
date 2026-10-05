<?php

namespace App\Console\Commands;

use App\Enums\DeadlineStatus;
use App\Models\CompanyDeadline;
use App\Models\User;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Console\Command;

class RemindDeadlinesCommand extends Command
{
    protected $signature = 'firm:remind-deadlines {--days= : Days ahead (defaults to config firm.deadline_reminder_days)}';

    protected $description = 'Notify responsible lawyers about company deadlines approaching, and mark passed ones as overdue.';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('firm.deadline_reminder_days'));

        CompanyDeadline::query()
            ->where('status', DeadlineStatus::PENDING)
            ->whereDate('due_at', '<', today())
            ->update(['status' => DeadlineStatus::OVERDUE]);

        $deadlines = CompanyDeadline::query()
            ->pending()
            ->where('reminder_sent', false)
            ->whereDate('due_at', '<=', now()->addDays($days)->toDateString())
            ->with('company.responsibleLawyer')
            ->get();

        $admins = User::role('admin')->get();

        foreach ($deadlines as $deadline) {
            $recipients = $admins->concat([$deadline->company->responsibleLawyer])->filter()->unique('id');
            $recipients->each(fn (User $user) => $user->notify(new DeadlineReminderNotification($deadline)));
            $deadline->update(['reminder_sent' => true]);
        }

        $this->info("Reminders sent for {$deadlines->count()} deadline(s).");

        return self::SUCCESS;
    }
}
