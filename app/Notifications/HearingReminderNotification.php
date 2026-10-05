<?php

namespace App\Notifications;

use App\Models\Hearing;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HearingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Hearing $hearing) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $case = $this->hearing->legalCase;

        return (new MailMessage)
            ->subject($this->title())
            ->greeting(__('app.portal.welcome', ['name' => $notifiable->name]))
            ->line($this->body())
            ->line(__('cases.fields.client').': '.$case->client?->name);
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->title())
            ->body($this->body())
            ->warning()
            ->icon('heroicon-o-calendar-days')
            ->getDatabaseMessage();
    }

    protected function title(): string
    {
        return __('notifications.hearing_reminder.title', ['case' => $this->hearing->legalCase->display_title]);
    }

    protected function body(): string
    {
        return __('notifications.hearing_reminder.body', [
            'court' => $this->hearing->legalCase->court?->name ?? '-',
            'time' => $this->hearing->scheduled_at->format('Y-m-d H:i'),
            'requirements' => $this->hearing->requirements ?? '',
        ]);
    }
}
