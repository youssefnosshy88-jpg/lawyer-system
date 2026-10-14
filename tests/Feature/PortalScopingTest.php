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
