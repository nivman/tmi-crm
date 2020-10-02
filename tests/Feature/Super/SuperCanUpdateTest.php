<?php

namespace Tests\Feature;

use Tests\TestCase;

class SuperCanUpdateTest extends TestCase
{
    /** @test */
    public function testSuperCanUpdateAccount()
    {
        $account       = \App\Account::latest()->first();
        $account->name = $account->name . ' Updated';
        $this->actingAs($this->user)->ajax()
            ->put('/app/accounts/' . $account->id, $account->toArray())
            ->assertOk()->assertJson([
                'name' => $account->name,
            ]);
    }

    /** @test */
    public function testSuperCanUpdateUser()
    {
        $user        = \App\User::latest()->first();
        $user->name  = $user->name . ' Updated';
        $user->roles = [\App\Role::whereSlug('staff')->first()->toArray()];
        $this->actingAs($this->user)->ajax()
            ->put('/app/users/' . $user->username, $user->toArray())
            ->assertOk()->assertJson([
                'name' => $user->name,
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('super');
    }
}
