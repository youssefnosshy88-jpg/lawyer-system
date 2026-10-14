<?php

use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\User;

it('allows accountants to manage invoices but not delete cases', function () {
    $accountant = User::factory()->accountant()->create();

    expect($accountant->can('create', Invoice::class))->toBeTrue()
        ->and($accountant->can('delete', LegalCase::factory()->create()))->toBeFalse()
        ->and($accountant->can('view', LegalCase::factory()->create()))->toBeTrue();
});

it('allows secretaries to create cases but not delete them', function () {
    $secretary = User::factory()->secretary()->create();

    expect($secretary->can('create', LegalCase::class))->toBeTrue()
        ->and($secretary->can('delete', LegalCase::factory()->create()))->toBeFalse()
        ->and($secretary->can('create', Invoice::class))->toBeFalse();
});

it('gives admins everything', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('delete', LegalCase::factory()->create()))->toBeTrue();
});
