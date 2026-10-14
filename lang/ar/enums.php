<?php

return [
    'client_type' => ['individual' => 'فرد', 'company' => 'شركة'],
    'client_role' => [
        'plaintiff' => 'مدعي', 'defendant' => 'مدعى عليه', 'appellant' => 'مستأنف',
        'respondent' => 'مستأنف ضده', 'petitioner' => 'طالب', 'other' => 'أخرى',
    ],
    'case_status' => [
        'open' => 'مفتوحة', 'in_progress' => 'متداولة', 'postponed' => 'مؤجلة', 'judged' => 'صدر فيها حكم',
        'appealed' => 'مستأنفة', 'closed' => 'مغلقة', 'archived' => 'مؤرشفة',
    ],
    'case_degree' => [
        'first_instance' => 'أول درجة', 'appeal' => 'استئناف', 'cassation' => 'نقض',
        'supreme_constitutional' => 'دستورية عليا', 'administrative' => 'قضاء إداري', 'execution' => 'تنفيذ',
    ],
    'hearing_status' => ['scheduled' => 'محددة', 'held' => 'انعقدت', 'postponed' => 'تأجلت', 'cancelled' => 'ألغيت'],
    'hearing_type' => [
        'pleading' => 'مرافعة', 'evidence' => 'إثبات / مستندات', 'expert_report' => 'تقرير خبير', 'judgment' => 'نطق بالحكم',
        'procedural' => 'إجرائية', 'execution' => 'تنفيذ', 'other' => 'أخرى',
    ],
    'activity_type' => [
        'memo' => 'مذكرة', 'filing' => 'إيداع / قيد', 'service' => 'إعلان', 'meeting' => 'اجتماع',
        'call' => 'مكالمة', 'research' => 'بحث قانوني', 'expert' => 'خبرة', 'other' => 'أخرى',
    ],
    'document_category' => [
        'pleading' => 'مذكرة / صحيفة', 'judgment' => 'حكم', 'contract' => 'عقد', 'power_of_attorney' => 'توكيل',
        'evidence' => 'مستند إثبات', 'correspondence' => 'مراسلات', 'id_document' => 'إثبات هوية',
        'commercial_register' => 'سجل تجاري', 'tax_card' => 'بطاقة ضريبية', 'gafi' => 'مستند هيئة الاستثمار',
        'invoice' => 'فاتورة', 'other' => 'أخرى',
    ],
    'contract_status' => ['draft' => 'مسودة', 'active' => 'ساري', 'expired' => 'منتهي', 'terminated' => 'مفسوخ'],
    'poa_status' => ['active' => 'ساري', 'expired' => 'منتهي', 'revoked' => 'ملغي'],
    'fee_type' => ['fixed' => 'أتعاب ثابتة', 'hourly' => 'بالساعة', 'percentage' => 'نسبة من الدعوى', 'installments' => 'أقساط', 'retainer' => 'اشتراك شهري'],
    'invoice_status' => ['draft' => 'مسودة', 'sent' => 'مرسلة', 'partially_paid' => 'مدفوعة جزئياً', 'paid' => 'مدفوعة', 'overdue' => 'متأخرة', 'cancelled' => 'ملغاة'],
    'payment_method' => ['cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'cheque' => 'شيك', 'instapay' => 'إنستاباي', 'wallet' => 'محفظة إلكترونية', 'card' => 'بطاقة'],
    'expense_category' => [
        'court_fees' => 'رسوم قضائية', 'expert_fees' => 'أتعاب خبير', 'transport' => 'انتقالات', 'translation' => 'ترجمة',
        'notary' => 'شهر عقاري', 'publication' => 'نشر', 'stamps' => 'دمغات', 'other' => 'أخرى',
    ],
    'legal_form' => [
        'llc' => 'ذات مسئولية محدودة', 'jsc' => 'مساهمة', 'one_person' => 'شركة الشخص الواحد', 'general_partnership' => 'تضامن',
        'limited_partnership' => 'توصية بسيطة', 'partnership_limited_by_shares' => 'توصية بالأسهم',
        'branch' => 'فرع شركة أجنبية', 'representative_office' => 'مكتب تمثيل', 'sole_proprietorship' => 'منشأة فردية',
    ],
    'company_status' => ['under_incorporation' => 'تحت التأسيس', 'active' => 'نشطة', 'suspended' => 'موقوفة', 'under_liquidation' => 'تحت التصفية', 'dissolved' => 'منحلة'],
    'partner_role' => [
        'partner' => 'شريك', 'shareholder' => 'مساهم', 'manager' => 'مدير', 'chairman' => 'رئيس مجلس الإدارة',
        'board_member' => 'عضو مجلس إدارة', 'managing_partner' => 'شريك متضامن مدير', 'legal_representative' => 'ممثل قانوني',
    ],
    'procedure_type' => [
        'incorporation' => 'تأسيس', 'amend_articles' => 'تعديل عقد الشركة', 'capital_increase' => 'زيادة رأس المال',
        'capital_decrease' => 'تخفيض رأس المال', 'change_manager' => 'تغيير المدير', 'change_address' => 'تغيير المقر',
        'add_activity' => 'إضافة نشاط', 'register_renewal' => 'تجديد السجل التجاري', 'gafi_license' => 'ترخيص هيئة الاستثمار',
        'general_assembly' => 'جمعية عمومية', 'branch_opening' => 'فتح فرع', 'transfer_shares' => 'تنازل عن حصص / أسهم',
        'liquidation' => 'تصفية', 'other' => 'أخرى',
    ],
    'procedure_status' => [
        'draft' => 'مسودة', 'documents_pending' => 'في انتظار المستندات', 'submitted' => 'مقدم', 'under_review' => 'قيد المراجعة',
        'approved' => 'معتمد', 'completed' => 'مكتمل', 'rejected' => 'مرفوض',
    ],
    'deadline_type' => [
        'commercial_register' => 'السجل التجاري', 'tax_card' => 'البطاقة الضريبية', 'gafi_license' => 'ترخيص الهيئة',
        'general_assembly' => 'الجمعية العمومية', 'financial_statements' => 'القوائم المالية',
        'social_insurance' => 'التأمينات الاجتماعية', 'license' => 'ترخيص', 'other' => 'أخرى',
    ],
    'deadline_status' => ['pending' => 'قائم', 'done' => 'تم', 'overdue' => 'متأخر'],
    'task_status' => ['todo' => 'مطلوب', 'in_progress' => 'جاري', 'done' => 'منتهي', 'cancelled' => 'ملغي'],
    'task_priority' => ['low' => 'منخفضة', 'normal' => 'عادية', 'high' => 'عالية', 'urgent' => 'عاجلة'],
];
