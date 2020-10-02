<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaffCanNotAccessTest extends TestCase
{
    /** @test */
    public function testStaffCanNotAccessAccounts()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/accounts?page=1&limit=10')
            ->assertStatus(403);

        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/accounts?all=1')
            ->assertStatus(200);
    }

    /** @test */
    public function testStaffCanNotAccessCategories()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/categories?page=1&limit=10')
            ->assertStatus(403);

        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/categories?all=1')
            ->assertStatus(200);
    }

    /** @test */
    public function testStaffCanNotAccessSettings()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/settings?page=1&limit=10')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotAccessTaxes()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/taxes?page=1&limit=10')
            ->assertStatus(403);

        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/taxes?all=1')
            ->assertStatus(200);
    }

    /** @test */
    public function testStaffCanNotAccessTransfers()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/transfers?page=1&limit=10')
            ->assertStatus(403);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('staff');
    }
}
