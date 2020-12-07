<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Thomasjohnkane\Snooze\Traits\SnoozeNotifiable;

class User extends Authenticatable
{
    use LogActivity;
    use Notifiable;
    use VueTable;
    use SnoozeNotifiable;

    public static $columns = ['id', 'name', 'username', 'email', 'phone'];

    protected $fillable = ['name', 'username', 'email', 'password', 'phone', 'customer_id', 'vendor_id'];

    protected $guard_name = 'web';

    protected $hidden = ['password', 'remember_token'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
