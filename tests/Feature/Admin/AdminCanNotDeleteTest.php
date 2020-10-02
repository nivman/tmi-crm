<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminCanNotDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('admin');
    }

    /** @test */
    public function testAdminCanNotDeleteAccount()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/accounts/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteCategory()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/categories/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteCustomer()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/customers/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteCustomField()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/custom_fields/1')
            ->assertStatus(403);
    }

    // /** @test */
    // public function testAdminCanDeleteEvent()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/events/1')
    //         ->assertStatus(403);
    // }

    /** @test */
    public function testAdminCanNotDeleteExpense()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/expenses/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteIncome()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/incomes/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteInvoice()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/invoices/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeletePayment()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/payments/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteProduct()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/products/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeletePurchase()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/purchases/1')
            ->assertStatus(403);
    }

    // /** @test */
    // public function testAdminCanNotDeleteRecurring()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/recurrings/1')
    //         ->assertStatus(403);
    // }

    /** @test */
    public function testAdminCanNotDeleteTax()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/taxes/1')
            ->assertStatus(403);
    }

    // /** @test */
    // public function testAdminCanNotDeleteTransfer()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/transfers/1')
    //         ->assertStatus(403);
    // }

    /** @test */
    public function testAdminCanNotDeleteUser()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/products/1')
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotDeleteVendor()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/vendors/1')
            ->assertStatus(403);
    }
}
