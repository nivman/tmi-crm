<?php

namespace Tests\Feature;

use Tests\TestCase;

class SuperCanAccessTest extends TestCase
{
    /** @test */
    public function testSuperCanAccessSettings()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/settings')
            ->assertOk();

        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/settings/system')
            ->assertOk();
    }

    /** @test */
    public function testSuperCanAccessUsers()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/user')
            ->assertOk();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('super');
    }
}
