<?php

namespace Tests\Feature;

use Tests\TestCase;

class SuperCanAddTest extends TestCase
{
    /** @test */
    public function testSuperCanAddAccount()
    {
        $account = factory('App\Account')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/accounts', $account)
            ->assertStatus(201);
    }

    /** @test */
    public function testSuperCanAddUser()
    {
        $user = factory('App\User')->make()->toArray();

        $user['password_confirmation'] = $user['password'] = '123456';
        $this->actingAs($this->user)->ajax()
            ->post('/app/users', $user)
            ->assertStatus(201);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('super');
    }
}
