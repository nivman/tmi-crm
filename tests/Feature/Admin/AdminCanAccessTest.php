<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminCanAccessTest extends TestCase
{
    /** @test */
    public function testAdminCanAccessAccounts()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/accounts?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessCategories()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/categories?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessCustomers()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/customers?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessExpenses()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/expenses?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessIncomes()
    {
        $this->actingAs($this->user)->ajax()
        ->get('/app/incomes?page=1&limit=10')
        ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessInvoices()
    {
        $this->actingAs($this->user)->ajax()
        ->get('/app/invoices?page=1&limit=10')
        ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessPurchases()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/purchases?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessTaxes()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/taxes?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessTransfers()
    {
        $this->actingAs($this->user)->ajax()
            ->json('get', '/app/transfers?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanAccessVendors()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/vendors?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanProducts()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/products?page=1&limit=10')
            ->assertOk();
    }

    /** @test */
    public function testAdminCanSearchProducts()
    {
        $this->actingAs($this->user)->ajax()
            ->get('/app/products/search?query=001')
            ->assertOk();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('admin');
    }
}
