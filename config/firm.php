<?php

return [
    'name' => env('FIRM_NAME', 'مكتب المحاماة'),
    'currency' => env('FIRM_CURRENCY', 'EGP'),
    'hearing_reminder_days' => (int) env('FIRM_HEARING_REMINDER_DAYS', 1),
    'deadline_reminder_days' => (int) env('FIRM_DEADLINE_REMINDER_DAYS', 30),
    'documents_disk' => env('FIRM_DOCUMENTS_DISK', 'private'),
];
