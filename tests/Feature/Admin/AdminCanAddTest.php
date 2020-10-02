<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Support\Str;

class AdminCanAddTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('admin');
    }

    /** @test */
    public function testAdminCanAddCategory()
    {
        $category = factory('App\Category')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/categories', $category)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddCustomer()
    {
        $customer = factory('App\Customer')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/customers', $customer)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddExpense()
    {
        $expense             = factory('App\Expense')->make(['user_id' => $this->user->id])->toArray();
        $expense['category'] = mt_rand(1, 5);
        $this->actingAs($this->user)->ajax()
            ->post('/app/expenses', $expense)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddIncome()
    {
        $income             = factory('App\Income')->make()->toArray();
        $income['category'] = mt_rand(1, 5);
        $this->actingAs($this->user)->ajax()
            ->post('/app/incomes', $income)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddInvoice()
    {
        $invoice           = factory('App\Invoice')->make(['reference' => 'R002' . Str::random()])->toArray();
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

    /** @test */
    public function testAdminCanAddProduct()
    {
        $product             = factory('App\Product')->make(['code' => 'P002' . Str::random(8)])->toArray();
        $product['taxes']    = [1, 2, 3];
        $product['category'] = mt_rand(1, 5);
        $this->actingAs($this->user)->ajax()
            ->post('/app/products', $product)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddPurchase()
    {
        $purchase          = factory('App\Purchase')->make(['reference' => 'R002' . Str::random()])->toArray();
        $product           = Product::find(mt_rand(1, 5));
        $product->discount = 0;
        $product->load('taxes');
        $product->qty         = mt_rand(1, 3);
        $purchase['date']     = date('Y-m-d');
        $product->product_id  = $product->id;
        $product->allTaxes    = $product->taxes->toArray();
        $purchase['products'] = [$product->toArray()];
        $this->actingAs($this->user)->ajax()
            ->post('/app/purchases', $purchase)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddTax()
    {
        $tax = factory('App\Tax')->make(['code' => 'T002' . Str::random()])->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/taxes', $tax)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddTransfer()
    {
        $transfer = factory('App\Transfer')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/transfers', $transfer)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanAddVendor()
    {
        $vendor = factory('App\Vendor')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/vendors', $vendor)
            ->assertStatus(201);
    }

    /** @test */
    public function testAdminCanNotAddAccount()
    {
        $account = factory('App\Account')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/accounts', $account)
            ->assertStatus(403);
    }

    /** @test */
    public function testAdminCanNotAddUser()
    {
        $account = factory('App\User')->make()->toArray();
        $this->actingAs($this->user)->ajax()
            ->post('/app/users', $account)
            ->assertStatus(403);
    }
}
