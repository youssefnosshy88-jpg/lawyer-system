<?php

return [
    'singular' => 'Contract',
    'plural' => 'Contracts',
    'fields' => [
        'title' => 'Title', 'type' => 'Contract type', 'second_party' => 'Second party', 'signed_at' => 'Signed at',
        'starts_at' => 'Starts at', 'ends_at' => 'Ends at', 'value' => 'Value', 'drafted_by' => 'Drafted by', 'summary' => 'Summary',
    ],
    'type_suggestions' => ['Lease', 'Sale', 'Employment', 'Services', 'Partnership', 'NDA', 'Settlement', 'Agency'],
];
