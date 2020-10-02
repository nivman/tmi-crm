<?php

namespace App\Traits;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\InputBag;

trait VueTable
{
    public static function scopeVueTable($data, $fields, $special_field = false)
    {
        $request = app()->make('request');
        extract($request->only('filters', 'query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn'));

        $limit = $request->request->filter('limit') == '' ? 10 : (int)$request->request->filter('limit');
        $page = (int)$request->request->filter('page');
        if ((isset($query) && $query) || (isset($filters) && $filters)) {
            if ($byColumn == 1) {
                $filters_query = json_decode($filters);
                static::filterByColumn($data, $filters_query);
            } else {
                static::filter($data, $query, $fields);
            }
        }

        $count = $data->count();

        $data->limit($limit)->skip($limit * ($page - 1));

        if (isset($orderBy) && $orderBy != 'false' && $orderBy && $orderBy != 'actions') {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }

        $results = $data->get();

        if ($special_field) {
            foreach ($special_field as $sf) {
                foreach ($results as &$value) {
                    $value->{$sf} = $value->{$sf};
                }
            }
        }

        return ['data' => $results->toArray(), 'count' => $count];
    }

    protected static function filterByColumn($data, $fields)
    {
        $optionals = ['taxes'];
        $filterable = [
            'id', 'code', 'name', 'reference', 'categories', 'customer', 'vendor', 'phone', 'user',
            'taxes', 'draft', 'date', 'gateway', 'price', 'cost', 'range', 'email', 'company', 'title', 'amount',
            'date_range', 'created_at', 'account', 'received', 'description', 'log_name', 'subject_id', 'subject_type'
        ];
        foreach ($fields as $field => $value) {
            if ($field != 'draft' && $field != 'received' && (empty($value) || !in_array($field, $filterable))) {
                continue;
            }

            if (!empty($value) && $field == 'created_at') {
                if (!empty($value)) {
                    $end = Carbon::createFromFormat('Y-m-d', $value)->endOfDay();
                    $start = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
                    $data->whereBetween('created_at', [$start, $end]);
                }
            } elseif (!empty($value) && $field == 'date_range' || $field == 'range') {
                if ($field == 'date_range' && !empty($value)) {
                    $range = explode(' to ', $value);
                    $end = Carbon::createFromFormat('Y-m-d', $range[1])->endOfDay();
                    $start = Carbon::createFromFormat('Y-m-d', $range[0])->startOfDay();
                    $data->whereBetween($fields->range == 'date' ? 'date' : 'created_at', [$start, $end]);
                }
            } elseif (!empty($value) && is_object($value)) {
                $method = in_array($field, $optionals) || (isset($value->optional) && !empty($value->optional)) ? 'orWhereHas' : 'whereHas';
                if (!empty($value->name)) {
                    $data->{$method}($field, function ($query) use ($value) {
                        foreach ($value as $f => $q) {
                            if (!empty($f) && !empty($q)) {
                                $query->where($f, 'like', "%{$q}%");
                            }
                        }
                    });
                }
            } else {
                $data->where($field, 'like', "%{$value}%");
            }
        }
        return $data;
    }

    protected static function filter($data, $query, $fields)
    {
        $data->where(function ($q) use ($query, $fields) {
            foreach ($fields as $index => $field) {
                $relation = explode('.', $field);
                if (isset($relation[0]) && isset($relation[1])) {
                    $method = $index ? 'orWhereHas' : 'whereHas';
                    $q->{$method}($relation[0], function ($qu) use ($relation, $query) {
                        $qu->where($relation[1], 'like', "%{$query}%");
                    });
                } else {
                    $method = $index ? 'orWhere' : 'where';
                    $q->{$method}($field, 'LIKE', "%{$query}%");
                }
            }
        });
        return $data;
    }
}
