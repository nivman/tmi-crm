<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Support\Str;

class StaffCanAddTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('staff');
    }

    /** @test */
    public function testStaffCanAddCustomer()
    {
        $customer = factory('App\Customer')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/customers', $customer)
            ->assertStatus(201);
    }

    /** @test */
    public function testStaffCanAddExpense()
    {
        $expense             = factory('App\Expense')->make(['user_id' => $this->user->id])->toArray();
        $expense['category'] = mt_rand(1, 5);
        $this->actingAs($this->user)->ajax()
            ->post('/app/expenses', $expense)
            ->assertStatus(201);
    }

    /** @test */
    public function testStaffCanAddIncome()
    {
        $income             = factory('App\Income')->make()->toArray();
        $income['category'] = mt_rand(1, 5);
        $this->actingAs($this->user)->ajax()
            ->post('/app/incomes', $income)
            ->assertStatus(201);
    }

    /** @test */
    public function testStaffCanAddInvoice()
    {
        $invoice           = factory('App\Invoice')->make(['reference' => 'R003' . Str::random()])->toArray();
        $product           = Product::find(mt_rand(1, 5));
        $product->discount = 0;
        $product->load('taxes');
        $product->qty        = mt_rand(1, 3);
        $invoice['date']     = date('Y-m-d');
        $product->product_id = $product->id;
        $product->allTaxes   = $product->taxes->toArray();
        $invoice['products'] = [$product->toArray()];
        $this->actingAs($this->user)->ajax()
            ->post('/app/invoices', $invoice)
            ->assertStatus(201);
    }

    // /** @test */
    // public function testStaffCanAddProduct()
    // {
    //     $product = factory('App\Product')->make(['code' => 'P003' . Str::random(8)])->toArray();
    //     $product['taxes'] = [1, 2, 3];
    //     $product['category'] = mt_rand(1, 5);
    //     $this->actingAs($this->user)->ajax()
    //         ->post('/app/products', $product)
    //         ->assertStatus(201);
    // }

    // /** @test */
    // public function testStaffCanAddPurchase()
    // {
    //     $purchase = factory('App\Purchase')->make(['reference' => 'R003' . Str::random()])->toArray();
    //     $product = Product::find(mt_rand(1, 5));
    //     $product->qty = mt_rand(1, 3);
    //     $purchase['date'] = date('Y-m-d');
    //     $product->product_id = $product->id;
    //     $product->allTaxes = $product->taxes->toArray();
    //     $purchase['products'] = [$product->toArray()];
    //     $this->actingAs($this->user)->ajax()
    //         ->post('/app/purchases', $purchase)
    //         ->assertStatus(201);
    // }

    /** @test */
    public function testStaffCanAddVendor()
    {
        $vendor = factory('App\Vendor')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/vendors', $vendor)
            ->assertStatus(201);
    }

    /** @test */
    public function testStaffCanNotAddAccount()
    {
        $account = factory('App\Account')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/accounts', $account)
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotAddCategory()
    {
        $category = factory('App\Category')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/categories', $category)
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotAddTax()
    {
        $tax = factory('App\Tax')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/taxes', $tax)
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotAddTransfer()
    {
        $transfer = factory('App\Transfer')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/transfers', $transfer)
            ->assertStatus(403);
    }

    /** @test */
    public function testStaffCanNotAddUser()
    {
        $account = factory('App\User')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/users', $account)
            ->assertStatus(403);
    }
}
