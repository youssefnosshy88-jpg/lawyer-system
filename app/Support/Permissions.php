<?php

namespace App\Support;

class Permissions
{
    public const ACTIONS = ['view_any', 'view', 'create', 'update', 'delete'];

    public const RESOURCES = [
        'clients', 'opponents', 'powers_of_attorney', 'courts', 'case_types', 'cases', 'hearings',
        'case_activities', 'documents', 'contracts', 'fee_agreements', 'invoices', 'payments', 'expenses',
        'companies', 'company_partners', 'company_procedures', 'company_deadlines', 'tasks', 'users', 'roles',
    ];

    public static function all(): array
    {
        $permissions = [];

        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS as $action) {
                $permissions[] = "{$action}_{$resource}";
            }
        }

        return $permissions;
    }

    public static function for(array $resources, array $actions = self::ACTIONS): array
    {
        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissions[] = "{$action}_{$resource}";
            }
        }

        return $permissions;
    }

    public static function rolePermissions(): array
    {
        $readOnly = ['view_any', 'view'];
        $noDelete = ['view_any', 'view', 'create', 'update'];

        return [
            'admin' => self::all(),

            'lawyer' => array_merge(
                self::for(['clients', 'opponents', 'powers_of_attorney', 'cases', 'hearings', 'case_activities',
                    'documents', 'contracts', 'companies', 'company_partners', 'company_procedures',
                    'company_deadlines', 'tasks', 'expenses']),
                self::for(['courts', 'case_types', 'fee_agreements', 'invoices', 'payments', 'users'], $readOnly),
            ),

            'secretary' => array_merge(
                self::for(['clients', 'opponents', 'powers_of_attorney', 'hearings', 'documents', 'tasks',
                    'company_deadlines'], $noDelete),
                self::for(['cases', 'case_activities', 'contracts', 'companies', 'company_partners',
                    'company_procedures', 'expenses'], $noDelete),
                self::for(['courts', 'case_types', 'invoices', 'payments', 'fee_agreements', 'users'], $readOnly),
            ),

            'accountant' => array_merge(
                self::for(['fee_agreements', 'invoices', 'payments', 'expenses']),
                self::for(['clients', 'cases', 'companies', 'company_procedures', 'contracts', 'users'], $readOnly),
            ),
        ];
    }
}
