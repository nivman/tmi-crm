<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaffCanNotUpdateTest extends TestCase
{
    /** @test */
    public function testStaffCanNotUpdateAccount()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/accounts/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateCategory()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/categories/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateCompany()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/companies/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateCustomer()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/customers/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateCustomFields()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/custom_fields/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateExpense()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/expenses/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateIncome()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/incomes/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateInvoice()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/invoices/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdatePayment()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/payments/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateProduct()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/products/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdatePurchase()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/purchases/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateSettings()
    {
        $this->actingAs($this->user)->ajax()
            ->post('/app/settings', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateSystemSettings()
    {
        $this->actingAs($this->user)->ajax()
            ->post('/app/settings/system', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateTax()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/taxes/1', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotUpdateUser()
    {
        $this->actingAs($this->user)->ajax()
            ->put('/app/users/admin', ['name' => 'Staff Update'])
            ->assertStatus(403);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('staff');
    }
}
