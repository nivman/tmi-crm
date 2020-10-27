<?php

use App\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        echo "Creating Roles & Users\n";
        $super_role    = Role::create(['name' => 'Super Admin', 'slug' => 'super']);
        $admin_role    = Role::create(['name' => 'Site Admin', 'slug' => 'admin']);
        $staff_role    = Role::create(['name' => 'Site Staff', 'slug' => 'staff']);
        $customer_role = Role::create(['name' => 'Site Customer', 'slug' => 'customer']);
        $vendor_role   = Role::create(['name' => 'Site Vendor', 'slug' => 'vendor']);

        $super = factory('App\User')->create([
            'username' => 'super',
            'name'     => 'Super Admin',
            'password' => bcrypt('123456'),
            'email'    => 'super@tmi.com',
        ]);
        $super->roles()->attach($super_role);
        $super->roles()->attach($admin_role);
        \Auth::login($super);

        $admin = factory('App\User')->create([
            'username' => 'admin',
            'name'     => 'Site Admin',
            'password' => bcrypt('123456'),
            'email'    => 'admin@tmi.com',
        ]);
        $admin->roles()->attach($admin_role);

        $staff = factory('App\User')->create([
            'username' => 'staff',
            'name'     => 'Sales Staff',
            'password' => bcrypt('123456'),
            'email'    => 'staff@tmi.com',
        ]);
        $staff->roles()->attach($staff_role);

        $customer = factory('App\User')->create([
            'username' => 'customer',
            'name'     => 'Site Customer',
            'password' => bcrypt('123456'),
            'email'    => 'customer@tmi.com',
        ]);
        $customer->roles()->attach($customer_role);

        $vendor = factory('App\User')->create([
            'username' => 'vendor',
            'name'     => 'Site Vendor',
            'password' => bcrypt('123456'),
            'email'    => 'vendor@tmi.com',
        ]);
        $vendor->roles()->attach($vendor_role);

        echo "Creating Customers\n";
        factory('App\Customer')->create(['name' => 'Site Customer', 'state' => 'IN-DL']);
        factory('App\Customer')->create(['name' => 'Site Customer 2', 'state' => 'IN-DL']);
        factory('App\Customer')->create(['name' => 'Site Customer 3', 'state' => 'IN-DL']);
        factory('App\Customer', 30)->create();
        $customer->customer()->associate(1)->save();

        echo "Creating Vendors\n";
        factory('App\Vendor')->create(['name' => 'Site Vendor', 'state' => 'IN-DL']);
        factory('App\Vendor', 30)->create();
        $vendor->vendor()->associate(1)->save();

        factory('App\User', 3)->create()->each(function ($u) use ($staff_role) {
            $u->roles()->attach($staff_role);
        });
    }
}
