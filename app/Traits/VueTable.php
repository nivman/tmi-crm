<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


trait VueTable
{
    /**
     * @param Builder $data
     * @param $fields
     * @param false $special_field
     * @param bool $joinTables
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function scopeVueTable($data, $fields, $special_field = false, $joinTables = false)
    {
        $byColumn = null;
        $request = app()->make('request');
        $ascending = null;
        extract($request->only('filters', 'query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn'));

        $limit = $request->request->filter('limit') == '' ? 10 : (int)$request->request->filter('limit');
        $page = (int)$request->request->filter('page');
        if ((isset($query) && $query) || (isset($filters) && $filters)) {
            if ($byColumn == 1) {
                $filters_query = json_decode($filters);
                static::filterByColumn($data, $filters_query);
            } else {
                static::filter($data, $query, $fields, $joinTables);
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

                foreach ($results as $value) {

                    $value->{$sf} = $value->{$sf};
                }
            }
        }

        return ['data' => $results->toArray(), 'count' => $count];
    }

    protected static function filterByColumn($data, $fields)
    {

        $optionals = ['taxes', 'tasks', 'events'];
        $filterable = [
            'id', 'code', 'name', 'reference', 'categories', 'customer', 'vendor', 'phone', 'user',
            'taxes', 'draft', 'date', 'gateway', 'price', 'cost', 'range', 'email', 'company', 'title', 'amount',
            'date_range', 'created_at', 'account', 'received', 'description', 'log_name', 'subject_id', 'subject_type', 'type'
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

    /**   * @param Builder $data */
    protected static function filter($data, $query, $fields, $joinTables = false)
    {
        $queryFields = json_decode($query, true);
        $data->where(function ($q) use ($query, $fields, $joinTables, $data, $queryFields) {


            foreach ($fields as $index => $field) {
                $relation = explode('.', $field);

                if (isset($relation[0]) && isset($relation[1])) {

                    $method = $index ? 'orWhereHas' : 'whereHas';
                    $q->{$method}($relation[0], function ($qu) use ($relation, $query, $data, $joinTables, $queryFields) {

                        if (count($queryFields) > 0 && $joinTables) {
                            foreach ($queryFields as $key => $queryField) {
                                if ( $key == 'start_date' || $key == 'end_date') {

                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $queryField['start']);

                                    $end = Carbon::createFromFormat('Y-m-d H:i:s', $queryField['end']);
                                    $qu->whereBetween($key, [$start, $end]);
                                }
                                if ($data->getModel()->checkRelation($key)  && $key !== 'start_date') {

                                    $qu->where($joinTables . '.' . $key, 'like', "%{$queryField}%");
                                }
                            }
                        } else {
                            $qu->where($relation[1], 'like', "%{$query}%");
                        }
                    });
                } else {
                    $queryFields = json_decode($query, true);
                    $method = (!$queryFields && $index) ? 'orWhere' : 'where';

                    if ($queryFields  && $joinTables) {

                        self::runOverFields($queryFields, $data, $method, $q, $joinTables);
                    }
                    else {
                        if ($queryFields) {
                             self::runOverFields($queryFields, $data, $method, $q, $joinTables);
                        } else {

                            $q->{$method}($field, 'LIKE', "%{$query}%");
                        }

                    }
                }
            }
        });
        return $data;
    }

    /**
     * @param $queryFields
     * @param Builder $data
     * @param string $method
     * @param $q
     * @param $joinTables
     * @return array
     */
    protected static function runOverFields($queryFields, Builder $data, string $method, $q, $joinTables): array
    {
        if(!is_array($queryFields)) {
            return [];
        }
        foreach ($queryFields as $key => $queryField) {

            if ($data->getModel()->checkRelation($key)) {
                if ($key == 'start_date' || $key == 'end_date') {

                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $queryField['start']);

                    $end = Carbon::createFromFormat('Y-m-d H:i:s', $queryField['end']);
                    $q->whereBetween($key, [$start, $end]);
                }else{
                    $q->{$method}($joinTables . '.' . $key, 'LIKE', "%{$queryField}%");
                }


            }
        }
        return array($key, $queryField);
    }
}
