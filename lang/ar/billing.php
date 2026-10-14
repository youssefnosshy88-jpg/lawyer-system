<?php

return [
    'fields' => ['amount' => 'المبلغ'],
    'fee_agreements' => [
        'singular' => 'اتفاقية أتعاب', 'plural' => 'اتفاقيات الأتعاب', 'type' => 'نوع الأتعاب', 'total_amount' => 'إجمالي الأتعاب',
        'advance_amount' => 'المقدم', 'hourly_rate' => 'سعر الساعة', 'success_percentage' => 'نسبة النجاح %',
        'installments_count' => 'عدد الأقساط', 'agreed_at' => 'تاريخ الاتفاق', 'terms' => 'الشروط', 'invoiced' => 'المفوتر', 'remaining' => 'المتبقي',
    ],
    'invoices' => [
        'singular' => 'فاتورة', 'plural' => 'الفواتير', 'number' => 'رقم الفاتورة', 'issued_at' => 'تاريخ الإصدار', 'due_at' => 'تاريخ الاستحقاق',
        'subtotal' => 'الإجمالي الفرعي', 'discount' => 'الخصم', 'tax_rate' => 'الضريبة %', 'tax_amount' => 'قيمة الضريبة', 'total' => 'الإجمالي',
        'paid_amount' => 'المدفوع', 'balance' => 'المتبقي', 'add_item' => 'إضافة بند', 'item_description' => 'البيان',
        'quantity' => 'الكمية', 'unit_price' => 'سعر الوحدة', 'regarding' => 'بخصوص',
        'sections' => ['header' => 'بيانات الفاتورة', 'items' => 'بنود الفاتورة'],
        'actions' => ['mark_sent' => 'اعتماد وإرسال'],
    ],
    'payments' => [
        'singular' => 'دفعة', 'plural' => 'المدفوعات', 'record' => 'تسجيل دفعة', 'recorded' => 'تم تسجيل الدفعة',
        'receipt_no' => 'رقم الإيصال', 'method' => 'طريقة الدفع', 'paid_at' => 'تاريخ الدفع', 'reference' => 'مرجع / رقم العملية',
        'received_by' => 'استلمها', 'attachment' => 'مرفق', 'receipt' => 'إيصال',
    ],
    'expenses' => [
        'singular' => 'مصروف', 'plural' => 'المصروفات', 'category' => 'التصنيف', 'description' => 'البيان',
        'spent_at' => 'التاريخ', 'billable' => 'يُحمَّل على الموكل', 'invoiced' => 'تمت فوترته', 'receipt' => 'الإيصال', 'paid_by' => 'دفعه',
    ],
    'print' => [
        'firm_signature' => 'توقيع المكتب', 'client_signature' => 'توقيع الموكل', 'received_from' => 'استلمنا من السيد/',
        'received_by_signature' => 'توقيع المستلم',
    ],
];
