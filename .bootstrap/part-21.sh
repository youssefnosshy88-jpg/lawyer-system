#!/usr/bin/env bash
set -euo pipefail

mkdir -p "tests/Feature"
cat > "tests/Feature/PermissionsTest.php" <<'__LAWYER_EOF__'
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
__LAWYER_EOF__
mkdir -p "tests/Feature"
cat > "tests/Feature/PortalScopingTest.php" <<'__LAWYER_EOF__'
<?php

use App\Models\Client;
use App\Models\LegalCase;
use App\Models\User;

function portalUserFor(Client $client): User
{
    $user = User::factory()->create(['client_id' => $client->id]);
    $user->syncRoles('client');

    return $user;
}

it('only shows the client their own visible cases', function () {
    $mine = Client::factory()->create();
    $other = Client::factory()->create();

    $visible = LegalCase::factory()->create(['client_id' => $mine->id, 'title' => 'My visible case']);
    LegalCase::factory()->create(['client_id' => $mine->id, 'title' => 'Hidden case', 'visible_to_client' => false]);
    $foreign = LegalCase::factory()->create(['client_id' => $other->id, 'title' => 'Someone else case']);

    $this->actingAs(portalUserFor($mine))
        ->get('/portal/cases')
        ->assertOk()
        ->assertSee('My visible case')
        ->assertDontSee('Hidden case')
        ->assertDontSee('Someone else case');

    $this->actingAs(portalUserFor($mine))
        ->get("/portal/cases/{$foreign->id}")
        ->assertNotFound();

    $this->actingAs(portalUserFor($mine))
        ->get("/portal/cases/{$visible->id}")
        ->assertOk();
});
__LAWYER_EOF__
mkdir -p "tests"
cat > "tests/Pest.php" <<'__LAWYER_EOF__'
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
__LAWYER_EOF__
mkdir -p "tests"
cat > "tests/TestCase.php" <<'__LAWYER_EOF__'
<?php

namespace Tests;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }
}
__LAWYER_EOF__
mkdir -p "tests/Unit"
cat > "tests/Unit/ReferenceGeneratorTest.php" <<'__LAWYER_EOF__'
<?php

use App\Models\Client;
use App\Models\LegalCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('generates prefixed yearly references', function () {
    $year = now()->format('Y');

    expect(Client::factory()->create()->code)->toBe("CL-{$year}-0001")
        ->and(LegalCase::factory()->create()->reference)->toBe("CS-{$year}-0001")
        ->and(LegalCase::factory()->create()->reference)->toBe("CS-{$year}-0002");
});
__LAWYER_EOF__
