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
