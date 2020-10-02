<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminCanNotAccessTest extends TestCase
{
    /** @test */
    public function testAdminCanNotAccessSettings()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/settings')
            ->assertStatus(403);

        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/settings/system')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotAccessUsers()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/users')
            ->assertStatus(403);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('admin');
    }
}
