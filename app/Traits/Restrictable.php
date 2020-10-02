<?php

namespace App\Traits;

use App\Scopes\CreatedByScope;

trait Restrictable
{
    // protected static function bootRestrictable()
    // {
    //     static::addGlobalScope(new CreatedByScope);
    // }

    public static function scopeMine($query)
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'super'])) {
            if ($user->hasRole('vendor')) {
                return $query->where('vendor_id', $user->vendor_id);
            } elseif ($user->hasRole('customer')) {
                return $query->where('customer_id', $user->customer_id);
            } else {
                return $query->where('user_id', $user->id);
            }
        }
    }

    public static function scopeMyInvoices($query)
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'super'])) {
            if ($user->hasRole('customer')) {
                return $query->where('customer_id', $user->customer_id);
            } else {
                return $query->where('user_id', $user->id);
            }
        }
    }

    public static function scopeMyPurchases($query)
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'super'])) {
            if ($user->hasRole('vendor')) {
                return $query->where('vendor_id', $user->vendor_id);
            } else {
                return $query->where('user_id', $user->id);
            }
        }
    }

    public static function scopemyPayments($query)
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'super'])) {
            if ($user->hasRole('customer')) {
                return $query->where('payable_id', $user->customer_id)->where('payable_type', 'App\Customer');
            } elseif ($user->hasRole('vendor')) {
                return $query->where('payable_id', $user->vendor_id)->where('payable_type', 'App\Vendor');
            } else {
                return $query->where('user_id', $user->id);
            }
        }
    }

    public static function scopeYearly($query, $year = null, $field = 'created_at')
    {
        return $query->whereYear($field, ($year ?: date('Y')));
    }

    public static function scopeMonthly($query, $year = null, $month = null, $field = 'created_at')
    {
        return $query->whereMonth($field, ($month ?: date('m')))->whereYear($field, ($year ?: date('Y')));
    }

    public static function scopeInterval($query, $interval = '1 DAY', $field = 'created_at')
    {
        return $query->where($field, '<=', \DB::raw("(NOW() + INTERVAL {$interval})"));
    }
}
