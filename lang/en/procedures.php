<?php

return [
    'singular' => 'Procedure',
    'plural' => 'Company procedures',
    'sections' => ['basic' => 'Procedure details', 'checklist' => 'Document checklist', 'result' => 'Result'],
    'add_checklist_item' => 'Add item',
    'fields' => [
        'type' => 'Procedure type', 'authority' => 'Authority', 'authority_reference' => 'Authority reference no.', 'assigned_to' => 'Assigned to',
        'started_at' => 'Started at', 'submitted_at' => 'Submitted at', 'due_at' => 'Expected completion', 'completed_at' => 'Completed at',
        'government_fees' => 'Government fees', 'service_fees' => 'Service fees', 'checklist_item' => 'Item', 'done' => 'Done',
        'progress' => 'Progress', 'result' => 'Result / notes',
    ],
    'checklists' => [
        'incorporation' => ['Trade name approval certificate', 'Bank certificate of capital deposit', 'Partners IDs / passports', 'Lease contract or ownership deed of premises', 'Auditor acceptance letter', 'Lawyer power of attorney', 'Articles of association signed'],
        'amend_articles' => ['Extraordinary general assembly minutes', 'Amendment contract', 'Commercial register extract', 'Power of attorney'],
        'capital_increase' => ['Extraordinary general assembly minutes', 'Bank certificate of capital deposit', 'Amendment of articles', 'Commercial register annotation'],
        'capital_decrease' => ['Extraordinary general assembly minutes', 'Auditor report', 'Publication in newspaper', 'Commercial register annotation'],
        'change_manager' => ['General assembly minutes', 'New manager ID', 'Commercial register annotation'],
        'change_address' => ['General assembly minutes', 'New lease contract', 'Commercial register annotation'],
        'add_activity' => ['General assembly minutes', 'Activity approvals if required', 'Commercial register annotation'],
        'register_renewal' => ['Commercial register extract', 'Tax card', 'Renewal fees paid'],
        'gafi_license' => ['Application form', 'Commercial register', 'Tax card', 'Premises documents'],
        'general_assembly' => ['Invitation to partners', 'Attendance sheet', 'Meeting minutes', 'Auditor report'],
        'branch_opening' => ['Board decision', 'Branch lease contract', 'Branch manager appointment'],
        'transfer_shares' => ['Transfer contract', 'General assembly approval', 'Commercial register annotation'],
        'liquidation' => ['Dissolution decision', 'Liquidator appointment', 'Publication', 'Final liquidation report', 'Register cancellation'],
        'other' => [],
    ],
];
