<?php

return [
    'singular' => 'Company',
    'plural' => 'Companies',
    'sections' => ['basic' => 'Company information', 'capital' => 'Capital', 'registration' => 'Registration & licenses', 'address' => 'Address'],
    'fields' => [
        'name' => 'Company name', 'name_en' => 'Name (English)', 'legal_form' => 'Legal form', 'responsible_lawyer' => 'Responsible lawyer',
        'law' => 'Governing law', 'activity' => 'Activity', 'authorized_capital' => 'Authorized capital', 'issued_capital' => 'Issued capital',
        'paid_capital' => 'Paid capital', 'currency' => 'Currency', 'commercial_register_no' => 'Commercial register no.',
        'commercial_register_office' => 'Register office', 'commercial_register_expires_at' => 'Register expiry',
        'tax_card_no' => 'Tax card no.', 'tax_office' => 'Tax office', 'fiscal_year_end_month' => 'Fiscal year end',
        'gafi_file_no' => 'GAFI file no.', 'gafi_license_no' => 'GAFI license no.', 'incorporated_at' => 'Incorporated at',
        'governorate' => 'Governorate', 'open_procedures' => 'Open procedures',
    ],
    'partners' => [
        'title' => 'Partners & shareholders', 'name' => 'Name', 'role' => 'Role', 'id_type' => 'ID type', 'id_number' => 'ID number',
        'share_percentage' => 'Share %', 'shares_count' => 'Shares', 'share_value' => 'Share value', 'is_signatory' => 'Authorized signatory',
        'id_types' => ['national_id' => 'National ID', 'passport' => 'Passport', 'commercial_register' => 'Commercial register'],
    ],
];
