<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaffCanAccessTest extends TestCase
{
    /** @test */
    public function testStaffCanAccessCustomers()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/customers?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testStaffCanAccessExpenses()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/expenses?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testStaffCanAccessIncomes()
    {
        $this->actingAs($this->user)->ajax()
        ->get('/app/incomes?page=1&limit=10')
        ->assertOk();
    }

    /** @test */
    public function testStaffCanAccessInvoices()
    {
        $this->actingAs($this->user)->ajax()
        ->get('/app/invoices?page=1&limit=10')
        ->assertOk();
    }

    /** @test */
    public function testStaffCanAccessPurchases()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/purchases?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testStaffCanAccessVendors()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/vendors?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testStaffCanProducts()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/products?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testStaffCanSearchProducts()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/products/search?query=001')
            ->assertOk();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('staff');
    }
}
