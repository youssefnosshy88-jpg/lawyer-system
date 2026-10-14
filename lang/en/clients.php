<?php

return [
    'singular' => 'Client',
    'plural' => 'Clients',
    'sections' => ['basic' => 'Basic information', 'contact' => 'Contact details'],
    'fields' => [
        'code' => 'Code', 'type' => 'Type', 'name' => 'Name', 'name_en' => 'Name (English)', 'national_id' => 'National ID',
        'commercial_register_no' => 'Commercial register no.', 'tax_number' => 'Tax number', 'nationality' => 'Nationality',
        'phone' => 'Phone', 'phone_alt' => 'Alternative phone', 'email' => 'Email', 'address' => 'Address', 'city' => 'City',
        'occupation' => 'Occupation', 'cases_count' => 'Cases', 'companies_count' => 'Companies',
    ],
    'contacts' => ['title' => 'Contact persons', 'name' => 'Name', 'position' => 'Position', 'is_primary' => 'Primary'],
    'poa' => [
        'title' => 'Powers of attorney', 'number' => 'POA number', 'notary_office' => 'Notary office', 'issued_at' => 'Issued at',
        'expires_at' => 'Expires at', 'lawyers' => 'Authorized lawyers', 'scope' => 'Scope',
    ],
];
