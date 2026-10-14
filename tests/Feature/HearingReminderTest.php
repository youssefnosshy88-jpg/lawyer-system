<?php

use App\Models\Hearing;
use App\Models\LegalCase;
use App\Models\User;
use App\Notifications\HearingReminderNotification;
use Illuminate\Support\Facades\Notification;

it('notifies the attending lawyer about tomorrow hearings once', function () {
    Notification::fake();

    $lawyer = User::factory()->lawyer()->create();
    $case = LegalCase::factory()->create(['lead_lawyer_id' => $lawyer->id]);
    $hearing = Hearing::factory()->create(['legal_case_id' => $case->id, 'lawyer_id' => $lawyer->id, 'scheduled_at' => now()->addDay()->setTime(10, 0)]);
    Hearing::factory()->create(['legal_case_id' => $case->id, 'lawyer_id' => $lawyer->id, 'scheduled_at' => now()->addDays(10)]);

    $this->artisan('firm:remind-hearings')->assertSuccessful();

    Notification::assertSentTo($lawyer, HearingReminderNotification::class, fn ($n) => $n->hearing->is($hearing));
    Notification::assertCount(1);

    expect($hearing->refresh()->reminder_sent)->toBeTrue();

    $this->artisan('firm:remind-hearings')->assertSuccessful();
    Notification::assertCount(1);
});
