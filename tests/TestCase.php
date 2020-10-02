<?php

namespace Tests;

use App\Role;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function ajax()
    {
        return $this->withHeaders([
            'HTTP_ACCEPT'           => 'application/json',
            'HTTP_CONTENT_TYPE'     => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    public function create($class, $attributes = [], $times = null)
    {
        return factory($class, $times)->create($attributes);
    }

    public function createUser($username = 'super')
    {
        // $role = Role::create(['name' => 'Test User', 'slug' => $username]);
        // $user = factory('App\User')->create(['username' => $username]);
        // $user->roles()->attach($role);
        // return $user;
        return \App\User::where('username', $username)->first();
    }

    public function make($class, $attributes = [], $times = null)
    {
        return factory($class, $times)->make($attributes);
    }
}
