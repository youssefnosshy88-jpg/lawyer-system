<?php

return [
    'fields' => ['amount' => 'Amount'],
    'fee_agreements' => [
        'singular' => 'Fee agreement', 'plural' => 'Fee agreements', 'type' => 'Fee type', 'total_amount' => 'Total fees',
        'advance_amount' => 'Advance', 'hourly_rate' => 'Hourly rate', 'success_percentage' => 'Success %',
        'installments_count' => 'Installments', 'agreed_at' => 'Agreed at', 'terms' => 'Terms', 'invoiced' => 'Invoiced', 'remaining' => 'Remaining',
    ],
    'invoices' => [
        'singular' => 'Invoice', 'plural' => 'Invoices', 'number' => 'Invoice no.', 'issued_at' => 'Issued at', 'due_at' => 'Due at',
        'subtotal' => 'Subtotal', 'discount' => 'Discount', 'tax_rate' => 'Tax %', 'tax_amount' => 'Tax', 'total' => 'Total',
        'paid_amount' => 'Paid', 'balance' => 'Balance due', 'add_item' => 'Add line', 'item_description' => 'Description',
        'quantity' => 'Qty', 'unit_price' => 'Unit price', 'regarding' => 'Regarding',
        'sections' => ['header' => 'Invoice details', 'items' => 'Line items'],
        'actions' => ['mark_sent' => 'Mark as sent'],
    ],
    'payments' => [
        'singular' => 'Payment', 'plural' => 'Payments', 'record' => 'Record payment', 'recorded' => 'Payment recorded',
        'receipt_no' => 'Receipt no.', 'method' => 'Method', 'paid_at' => 'Paid at', 'reference' => 'Reference',
        'received_by' => 'Received by', 'attachment' => 'Attachment', 'receipt' => 'Receipt',
    ],
    'expenses' => [
        'singular' => 'Expense', 'plural' => 'Expenses', 'category' => 'Category', 'description' => 'Description',
        'spent_at' => 'Date', 'billable' => 'Billable', 'invoiced' => 'Invoiced', 'receipt' => 'Receipt', 'paid_by' => 'Paid by',
    ],
    'print' => [
        'firm_signature' => 'Firm signature', 'client_signature' => 'Client signature', 'received_from' => 'Received from',
        'received_by_signature' => 'Receiver signature',
    ],
];
