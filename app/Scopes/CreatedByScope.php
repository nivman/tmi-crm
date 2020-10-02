<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CreatedByScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'super'])) {
            if ($user->hasRole('customer')) {
                $builder->where('customer_id', $user->customer_id);
            } else {
                $builder->where('user_id', $user->id);
            }
        }
    }
}
