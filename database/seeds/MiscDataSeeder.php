<?php

use Illuminate\Database\Seeder;

class MiscDataSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        factory('App\Company')->create();

        echo "Creating Taxes\n";
        factory('App\Tax')->create(['name' => 'CGST @ 9%', 'code' => 'cgst@9', 'rate' => 9, 'compound' => true, 'state' => true, 'same' => true]);
        factory('App\Tax')->create(['name' => 'SGST @ 9%', 'code' => 'sgst@9', 'rate' => 9, 'compound' => true, 'state' => true, 'same' => true]);
        factory('App\Tax')->create(['name' => 'IGST @ 18%', 'code' => 'igst@18', 'rate' => 18, 'compound' => true, 'state' => true, 'same' => false]);
        factory('App\Tax')->create(['name' => 'CGST @ 11%', 'code' => 'cgst@11', 'rate' => 11, 'compound' => true, 'state' => true, 'same' => true]);
        factory('App\Tax')->create(['name' => 'SGST @ 11%', 'code' => 'sgst@11', 'rate' => 11, 'compound' => true, 'state' => true, 'same' => true]);
        factory('App\Tax')->create(['name' => 'IGST @ 22%', 'code' => 'igst@22', 'rate' => 22, 'compound' => true, 'state' => true, 'same' => false]);
        factory('App\Tax')->create(['name' => 'CGST @ 14%', 'code' => 'cgst@14', 'rate' => 14, 'compound' => true, 'state' => true, 'same' => true]);
        factory('App\Tax')->create(['name' => 'SGST @ 14%', 'code' => 'sgst@14', 'rate' => 14, 'compound' => true, 'state' => true, 'same' => true]);
        factory('App\Tax')->create(['name' => 'IGST @ 28%', 'code' => 'igst@28', 'rate' => 28, 'compound' => true, 'state' => true, 'same' => false]);
        factory('App\Tax')->create();

        echo "Creating Events\n";
        factory('App\Event', 30)->create();

        echo "Creating Accounts\n";
        factory('App\Account', 5)->create();

        echo "Creating Categories\n";
        factory('App\Category', 10)->create();

        $cIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        echo "Creating Products\n";
        for ($i = 1; $i < 4; $i++) {
            $product = factory('App\Product')->create(['code' => 'PR00' . $i]);
            $product->categories()->sync($faker->randomElement($cIds));
            if ($i == 1) {
                $product->taxes()->sync([1, 2, 3]);
            } elseif ($i == 2) {
                $product->taxes()->sync([4, 5, 6]);
            } else {
                $product->taxes()->sync([7, 8, 9]);
            }
            $product->stock()->create(['company_id' => 1, 'qty' => mt_rand(5, 10)]);
        }
        factory('App\Product', 50)->create()->each(function ($p) use ($faker, $cIds) {
            $p->categories()->sync($faker->randomElement($cIds));
            $p->taxes()->sync($faker->randomElements($cIds, mt_rand(1, 2)));
            $p->stock()->create(['company_id' => 1, 'qty' => mt_rand(10, 100)]);
        });

        echo "Creating Expenses\n";
        factory('App\Expense', 30)->create()->each(function ($e) use ($faker, $cIds) {
            $e->categories()->sync($faker->randomElement($cIds));
            // $e->taxes()->sync($faker->randomElements($cIds, mt_rand(2, 4)));
        });

        echo "Creating Incomes\n";
        factory('App\Income', 30)->create()->each(function ($i) use ($faker, $cIds) {
            $i->categories()->sync($faker->randomElement($cIds));
            // $i->taxes()->sync($faker->randomElements($cIds, mt_rand(2, 4)));
        });

        echo "Creating Custom Fields\n";
        $attributes = factory('App\CustomField', 5)->make()->toArray();
        foreach ($attributes as $attribute) {
            $cf = app('rinvex.attributes.attribute')->create($attribute);
            $entities = ["App\Account", "App\Customer", "App\Expense", "App\Income", "App\Invoice", "App\Vendor", "App\Product", "App\Purchase"];
            $attribute_entities = $faker->randomElements($entities, mt_rand(2, 5));
            if (in_array('App\Invoice', $attribute_entities)) {
                array_push($attribute_entities, 'App\Recurring');
            }
            $cf->fill(['entities' => $attribute_entities])->save();
        }
    }
}
