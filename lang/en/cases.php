<?php

return [
    'singular' => 'Case',
    'plural' => 'Cases',
    'sections' => ['basic' => 'Case information', 'team' => 'Team & dates', 'details' => 'Details', 'judgment' => 'Judgment & closing'],
    'tabs' => ['active' => 'Active', 'mine' => 'My cases', 'closed' => 'Closed', 'all' => 'All'],
    'fields' => [
        'reference' => 'Reference', 'case_number' => 'Case no.', 'case_year' => 'Year', 'title' => 'Title', 'client' => 'Client',
        'client_role' => 'Client role', 'case_type' => 'Type', 'court' => 'Court', 'circuit' => 'Circuit', 'degree' => 'Degree',
        'lead_lawyer' => 'Lead lawyer', 'lawyers' => 'Team', 'subject' => 'Subject', 'description' => 'Description',
        'claim_amount' => 'Claim amount', 'filed_at' => 'Filed at', 'closed_at' => 'Closed at', 'judgment_summary' => 'Judgment summary',
        'next_hearing' => 'Next hearing', 'opponents' => 'Opponents',
    ],
    'activities' => [
        'title' => 'Activity log', 'title_field' => 'Title', 'type' => 'Type', 'occurred_at' => 'Date', 'hours_spent' => 'Hours',
        'body' => 'Details', 'user' => 'By',
    ],
];
