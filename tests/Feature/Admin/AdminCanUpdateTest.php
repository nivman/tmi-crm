<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminCanUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('admin');
    }

    /** @test */
    public function testAdminCanUpdateInvoice()
    {
        $invoice           = \App\Invoice::latest()->first()->toArray();
        $product           = \App\Product::find(mt_rand(1, 5));
        $product->discount = 0;
        $product->load('taxes');
        $product->qty         = mt_rand(1, 3);
        $invoice['date']      = date('Y-m-d');
        $product->product_id  = $product->id;
        $product->allTaxes    = $product->taxes->toArray();
        $invoice['products']  = [$product->toArray()];
        $invoice['reference'] = 'R003' . mt_rand();

        $this->actingAs($this->user)->ajax()
            ->put('/app/invoices/' . $invoice['id'], $invoice)
            ->assertOk()->assertJson([
                'reference' => $invoice['reference'],
            ]);
    }

    /** @test */
    public function testAdminCanUpdatePurchase()
    {
        $purchase          = \App\Purchase::latest()->first()->toArray();
        $product           = \App\Product::find(mt_rand(1, 5));
        $product->discount = 0;
        $product->load('taxes');
        $product->qty          = mt_rand(1, 3);
        $purchase['date']      = date('Y-m-d');
        $product->product_id   = $product->id;
        $product->allTaxes     = $product->taxes->toArray();
        $purchase['products']  = [$product->toArray()];
        $purchase['reference'] = 'R003' . mt_rand();

        $this->actingAs($this->user)->ajax()
            ->put('/app/purchases/' . $purchase['id'], $purchase)
            ->assertOk()->assertJson([
                'reference' => $purchase['reference'],
            ]);
    }

    // /** @test */
    // public function testAdminCanUpdateUser()
    // {
    //     $user = \App\User::latest()->first();
    //     $user->name = $user->name . ' Updated';
    //     $this->actingAs($this->user)->ajax()
    //         ->put('/app/users/' . $user->username, $user->toArray())
    //         ->assertOk()->assertJson([
    //             'name' => $user->name
    //         ]);
    // }
}
