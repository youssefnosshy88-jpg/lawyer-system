<?php

return [
    'client_type' => ['individual' => 'Individual', 'company' => 'Company'],
    'client_role' => [
        'plaintiff' => 'Plaintiff', 'defendant' => 'Defendant', 'appellant' => 'Appellant',
        'respondent' => 'Respondent', 'petitioner' => 'Petitioner', 'other' => 'Other',
    ],
    'case_status' => [
        'open' => 'Open', 'in_progress' => 'In progress', 'postponed' => 'Postponed', 'judged' => 'Judged',
        'appealed' => 'Appealed', 'closed' => 'Closed', 'archived' => 'Archived',
    ],
    'case_degree' => [
        'first_instance' => 'First instance', 'appeal' => 'Appeal', 'cassation' => 'Cassation',
        'supreme_constitutional' => 'Supreme Constitutional', 'administrative' => 'Administrative', 'execution' => 'Execution',
    ],
    'hearing_status' => ['scheduled' => 'Scheduled', 'held' => 'Held', 'postponed' => 'Postponed', 'cancelled' => 'Cancelled'],
    'hearing_type' => [
        'pleading' => 'Pleading', 'evidence' => 'Evidence', 'expert_report' => 'Expert report', 'judgment' => 'Judgment',
        'procedural' => 'Procedural', 'execution' => 'Execution', 'other' => 'Other',
    ],
    'activity_type' => [
        'memo' => 'Memo', 'filing' => 'Filing', 'service' => 'Service of process', 'meeting' => 'Meeting',
        'call' => 'Phone call', 'research' => 'Legal research', 'expert' => 'Expert', 'other' => 'Other',
    ],
    'document_category' => [
        'pleading' => 'Pleading / Memo', 'judgment' => 'Judgment', 'contract' => 'Contract', 'power_of_attorney' => 'Power of attorney',
        'evidence' => 'Evidence', 'correspondence' => 'Correspondence', 'id_document' => 'ID document',
        'commercial_register' => 'Commercial register', 'tax_card' => 'Tax card', 'gafi' => 'GAFI document',
        'invoice' => 'Invoice', 'other' => 'Other',
    ],
    'contract_status' => ['draft' => 'Draft', 'active' => 'Active', 'expired' => 'Expired', 'terminated' => 'Terminated'],
    'poa_status' => ['active' => 'Active', 'expired' => 'Expired', 'revoked' => 'Revoked'],
    'fee_type' => ['fixed' => 'Fixed fee', 'hourly' => 'Hourly', 'percentage' => 'Success percentage', 'installments' => 'Installments', 'retainer' => 'Retainer'],
    'invoice_status' => ['draft' => 'Draft', 'sent' => 'Sent', 'partially_paid' => 'Partially paid', 'paid' => 'Paid', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled'],
    'payment_method' => ['cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'cheque' => 'Cheque', 'instapay' => 'InstaPay', 'wallet' => 'Mobile wallet', 'card' => 'Card'],
    'expense_category' => [
        'court_fees' => 'Court fees', 'expert_fees' => 'Expert fees', 'transport' => 'Transport', 'translation' => 'Translation',
        'notary' => 'Notary', 'publication' => 'Publication', 'stamps' => 'Stamps', 'other' => 'Other',
    ],
    'legal_form' => [
        'llc' => 'LLC', 'jsc' => 'Joint stock company', 'one_person' => 'One person company', 'general_partnership' => 'General partnership',
        'limited_partnership' => 'Limited partnership', 'partnership_limited_by_shares' => 'Partnership limited by shares',
        'branch' => 'Foreign branch', 'representative_office' => 'Representative office', 'sole_proprietorship' => 'Sole proprietorship',
    ],
    'company_status' => ['under_incorporation' => 'Under incorporation', 'active' => 'Active', 'suspended' => 'Suspended', 'under_liquidation' => 'Under liquidation', 'dissolved' => 'Dissolved'],
    'partner_role' => [
        'partner' => 'Partner', 'shareholder' => 'Shareholder', 'manager' => 'Manager', 'chairman' => 'Chairman',
        'board_member' => 'Board member', 'managing_partner' => 'Managing partner', 'legal_representative' => 'Legal representative',
    ],
    'procedure_type' => [
        'incorporation' => 'Incorporation', 'amend_articles' => 'Amend articles', 'capital_increase' => 'Capital increase',
        'capital_decrease' => 'Capital decrease', 'change_manager' => 'Change manager', 'change_address' => 'Change address',
        'add_activity' => 'Add activity', 'register_renewal' => 'Commercial register renewal', 'gafi_license' => 'GAFI license',
        'general_assembly' => 'General assembly', 'branch_opening' => 'Branch opening', 'transfer_shares' => 'Transfer of shares',
        'liquidation' => 'Liquidation', 'other' => 'Other',
    ],
    'procedure_status' => [
        'draft' => 'Draft', 'documents_pending' => 'Documents pending', 'submitted' => 'Submitted', 'under_review' => 'Under review',
        'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected',
    ],
    'deadline_type' => [
        'commercial_register' => 'Commercial register', 'tax_card' => 'Tax card', 'gafi_license' => 'GAFI license',
        'general_assembly' => 'General assembly', 'financial_statements' => 'Financial statements',
        'social_insurance' => 'Social insurance', 'license' => 'License', 'other' => 'Other',
    ],
    'deadline_status' => ['pending' => 'Pending', 'done' => 'Done', 'overdue' => 'Overdue'],
    'task_status' => ['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done', 'cancelled' => 'Cancelled'],
    'task_priority' => ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'],
];
