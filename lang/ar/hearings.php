<?php

return [
    'singular' => 'جلسة',
    'plural' => 'الجلسات',
    'tabs' => ['today' => 'اليوم', 'week' => 'خلال 7 أيام', 'upcoming' => 'القادمة', 'mine' => 'جلساتي', 'all' => 'الكل'],
    'fields' => [
        'case' => 'القضية', 'scheduled_at' => 'موعد الجلسة', 'type' => 'نوع الجلسة', 'courtroom' => 'القاعة', 'lawyer' => 'المحامي الحاضر',
        'requirements' => 'المطلوب تجهيزه', 'outcome' => 'ما تم بالجلسة', 'decision' => 'قرار المحكمة', 'next_hearing_at' => 'تأجلت إلى',
        'schedule_next' => 'موعد الجلسة القادمة',
    ],
    'actions' => ['record_outcome' => 'تسجيل ما تم بالجلسة', 'create_next' => 'إنشاء الجلسة القادمة تلقائياً'],
];
