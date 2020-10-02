<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaffCanNotDeleteTest extends TestCase
{
    /** @test */
    public function testStaffCanNotDeleteAccount()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/accounts/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteCategory()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/categories/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteCustomer()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/customers/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteCustomField()
    {
        $this->actingAs($this->user)->ajax()
        ->delete('/app/custom_fields/1')
        ->assertStatus(403);
    }

    // /** @test */
    // public function testStaffCanDeleteEvent()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/events/1')
    //         ->assertStatus(403);
    // }

    /** @test */
    public function testStaffCanNotDeleteExpense()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/expenses/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteIncome()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/incomes/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteInvoice()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/invoices/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeletePayment()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/payments/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteProduct()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/products/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeletePurchase()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/purchases/1')
            ->assertStatus(403);
    }

    // /** @test */
    // public function testStaffCanNotDeleteRecurring()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/recurrings/1')
    //         ->assertStatus(403);
    // }

    /** @test */
    public function testStaffCanNotDeleteTax()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/taxes/1')
            ->assertStatus(403);
    }

    // /** @test */
    // public function testStaffCanNotDeleteTransfer()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/transfers/1')
    //         ->assertStatus(403);
    // }

    /** @test */
    public function testStaffCanNotDeleteUser()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/products/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotDeleteVendor()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/vendors/1')
            ->assertStatus(403);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('staff');
    }
}
