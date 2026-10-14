<?php

use App\Models\Client;
use App\Models\User;
use Filament\Facades\Filament;

it('lets staff into the admin panel but not the portal', function () {
    $lawyer = User::factory()->lawyer()->create();

    expect($lawyer->canAccessPanel(Filament::getPanel('admin')))->toBeTrue()
        ->and($lawyer->canAccessPanel(Filament::getPanel('portal')))->toBeFalse();
});

it('lets client users into the portal but not the admin panel', function () {
    $client = Client::factory()->create();
    $user = User::factory()->create(['client_id' => $client->id]);
    $user->syncRoles('client');

    expect($user->canAccessPanel(Filament::getPanel('portal')))->toBeTrue()
        ->and($user->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});

it('blocks inactive users everywhere', function () {
    $admin = User::factory()->admin()->create(['is_active' => false]);

    expect($admin->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});

it('redirects guests from the admin panel to login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('renders the admin dashboard for an admin', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertOk();
});
