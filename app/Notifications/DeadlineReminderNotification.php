<?php

namespace App\Notifications;

use App\Models\CompanyDeadline;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CompanyDeadline $deadline) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title())
            ->line($this->body());
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->title())
            ->body($this->body())
            ->danger()
            ->icon('heroicon-o-bell-alert')
            ->getDatabaseMessage();
    }

    protected function title(): string
    {
        return __('notifications.deadline_reminder.title', ['company' => $this->deadline->company->name]);
    }

    protected function body(): string
    {
        return __('notifications.deadline_reminder.body', [
            'title' => $this->deadline->title,
            'date' => $this->deadline->due_at->format('Y-m-d'),
        ]);
    }
}
