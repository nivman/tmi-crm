<?php

namespace Tests\Feature;

use Tests\TestCase;

class SuperCanDeleteTest extends TestCase
{
    /** @test */
    public function testSuperCanDeleteAccount()
    {
        $account = $this->create('App\Account');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/accounts/' . $account->id)
            ->assertStatus(204);
    }

    /** @test */
    public function testSuperCanDeleteCategory()
    {
        $category = $this->create('App\Category');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/categories/' . $category->id)
            ->assertStatus(204);
    }

    /** @test */
    public function testSuperCanDeleteCustomer()
    {
        $customer = $this->create('App\Customer');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/customers/' . $customer->id)
            ->assertStatus(204);
    }

    /** @test */
    public function testSuperCanDeleteCustomField()
    {
        $field = $this->create('App\CustomField');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/custom_fields/' . $field->id)
            ->assertStatus(204);
    }

    // /** @test */
    // public function testSuperCanDeleteEvent()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/events/1')
    //         ->assertStatus(204);
    // }

    /** @test */
    public function testSuperCanDeleteExpense()
    {
        $expenses = $this->create('App\Expense');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/expenses/' . $expenses->id)
            ->assertStatus(204);
    }

    /** @test */
    public function testSuperCanDeleteIncome()
    {
        $income = $this->create('App\Income');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/incomes/' . $income->id)
            ->assertStatus(204);
    }

    // /** @test */
    // public function testSuperCanDeleteInvoice()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/invoices/1')
    //         ->assertStatus(204);
    // }

    // /** @test */
    // public function testSuperCanDeletePayment()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/payments/1')
    //         ->assertStatus(204);
    // }

    /** @test */
    public function testSuperCanDeleteProduct()
    {
        $product = $this->create('App\Product');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/products/' . $product->id)
            ->assertStatus(204);
    }

    // /** @test */
    // public function testSuperCanDeletePurchase()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/purchases/1')
    //         ->assertStatus(204);
    // }

    // /** @test */
    // public function testSuperCanDeleteRecurring()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/recurrings/1')
    //         ->assertStatus(204);
    // }

    /** @test */
    public function testSuperCanDeleteTax()
    {
        $tax = $this->create('App\Tax', ['code' => 'T00' . mt_rand()]);
        $this->actingAs($this->user)->ajax()
            ->delete('/app/taxes/' . $tax->id)
            ->assertStatus(204);
    }

    // /** @test */
    // public function testSuperCanDeleteTransfer()
    // {
    //     $this->actingAs($this->user)->ajax()
    //         ->delete('/app/transfers/1')
    //         ->assertStatus(204);
    // }

    /** @test */
    public function testSuperCanDeleteUser()
    {
        $user = $this->create('App\User');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/users/' . $user->username)
            ->assertStatus(204);
    }

    /** @test */
    public function testSuperCanDeleteVendor()
    {
        $vendor = $this->create('App\Vendor');
        $this->actingAs($this->user)->ajax()
            ->delete('/app/vendors/' . $vendor->id)
            ->assertStatus(204);
    }

    /** @test */
    public function testSuperCanNotDeleteCompany()
    {
        $this->actingAs($this->user)->ajax()
            ->delete('/app/companies/1')
            ->assertStatus(405);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('super');
    }
}
