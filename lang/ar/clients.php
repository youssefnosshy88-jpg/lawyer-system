<?php

return [
    'singular' => 'موكل',
    'plural' => 'الموكلون',
    'sections' => ['basic' => 'البيانات الأساسية', 'contact' => 'بيانات الاتصال'],
    'fields' => [
        'code' => 'الكود', 'type' => 'النوع', 'name' => 'الاسم', 'name_en' => 'الاسم بالإنجليزية', 'national_id' => 'الرقم القومي',
        'commercial_register_no' => 'رقم السجل التجاري', 'tax_number' => 'الرقم الضريبي', 'nationality' => 'الجنسية',
        'phone' => 'الهاتف', 'phone_alt' => 'هاتف بديل', 'email' => 'البريد الإلكتروني', 'address' => 'العنوان', 'city' => 'المدينة',
        'occupation' => 'المهنة', 'cases_count' => 'القضايا', 'companies_count' => 'الشركات',
    ],
    'contacts' => ['title' => 'جهات الاتصال', 'name' => 'الاسم', 'position' => 'الصفة', 'is_primary' => 'رئيسي'],
    'poa' => [
        'title' => 'التوكيلات', 'number' => 'رقم التوكيل', 'notary_office' => 'مكتب الشهر العقاري', 'issued_at' => 'تاريخ الإصدار',
        'expires_at' => 'تاريخ الانتهاء', 'lawyers' => 'المحامون الموكَّلون', 'scope' => 'نطاق التوكيل',
    ],
];
