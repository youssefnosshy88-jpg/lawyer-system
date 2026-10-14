<?php

return [
    'singular' => 'شركة',
    'plural' => 'الشركات',
    'sections' => ['basic' => 'بيانات الشركة', 'capital' => 'رأس المال', 'registration' => 'السجلات والتراخيص', 'address' => 'العنوان'],
    'fields' => [
        'name' => 'اسم الشركة', 'name_en' => 'الاسم بالإنجليزية', 'legal_form' => 'الشكل القانوني', 'responsible_lawyer' => 'المحامي المسئول',
        'law' => 'القانون المنظم', 'activity' => 'النشاط', 'authorized_capital' => 'رأس المال المرخص به', 'issued_capital' => 'رأس المال المصدر',
        'paid_capital' => 'رأس المال المدفوع', 'currency' => 'العملة', 'commercial_register_no' => 'رقم السجل التجاري',
        'commercial_register_office' => 'مكتب السجل', 'commercial_register_expires_at' => 'انتهاء السجل التجاري',
        'tax_card_no' => 'رقم البطاقة الضريبية', 'tax_office' => 'المأمورية', 'fiscal_year_end_month' => 'نهاية السنة المالية',
        'gafi_file_no' => 'رقم الملف بالهيئة', 'gafi_license_no' => 'رقم ترخيص الهيئة', 'incorporated_at' => 'تاريخ التأسيس',
        'governorate' => 'المحافظة', 'open_procedures' => 'إجراءات جارية',
    ],
    'partners' => [
        'title' => 'الشركاء والمساهمون', 'name' => 'الاسم', 'role' => 'الصفة', 'id_type' => 'نوع الهوية', 'id_number' => 'رقم الهوية',
        'share_percentage' => 'نسبة الحصة %', 'shares_count' => 'عدد الأسهم / الحصص', 'share_value' => 'قيمة الحصة', 'is_signatory' => 'له حق التوقيع',
        'id_types' => ['national_id' => 'رقم قومي', 'passport' => 'جواز سفر', 'commercial_register' => 'سجل تجاري'],
    ],
];
