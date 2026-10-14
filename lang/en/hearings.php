<?php

return [
    'singular' => 'Hearing',
    'plural' => 'Hearings',
    'tabs' => ['today' => 'Today', 'week' => 'Next 7 days', 'upcoming' => 'Upcoming', 'mine' => 'My hearings', 'all' => 'All'],
    'fields' => [
        'case' => 'Case', 'scheduled_at' => 'Date & time', 'type' => 'Type', 'courtroom' => 'Courtroom', 'lawyer' => 'Attending lawyer',
        'requirements' => 'Preparation needed', 'outcome' => 'Outcome', 'decision' => 'Court decision', 'next_hearing_at' => 'Postponed to',
        'schedule_next' => 'Next hearing date',
    ],
    'actions' => ['record_outcome' => 'Record outcome', 'create_next' => 'Create the next hearing automatically'],
];
