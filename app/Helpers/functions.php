<?php

// App Config
use Illuminate\Support\Facades\Storage;



// Log Activity
if (!function_exists('log_activity')) {
    function log_activity($activity, $properties = null, $model = null)
    {
        return activity()->performedOn($model)->withProperties($properties)->log($activity);
    }
}

// Format Decimal
if (!function_exists('formatDecimal')) {
    function formatDecimal($number, $decimals = 4, $ds = '.', $ts = '')
    {
        return number_format($number, $decimals, $ds, $ts);
    }
}

// Format Number
if (!function_exists('formatNumber')) {
    function formatNumber($number, $decimals = 2, $ds = '.', $ts = ',')
    {
        return number_format($number, $decimals, $ds, $ts);
    }
}

// Get Country and Sate
if (!function_exists('getCS')) {
    function getCS($country, $state)
    {
        $country = get_country($country);
        return ['country' => $country, 'state' => get_state($country, $state)];
    }
}

// Get Country by Code
if (!function_exists('get_country')) {
    function get_country($code)
    {
        return \Geographer::getCountries()->findOne(['isoCode' => $code]);
    }
}

// Get State by Code
if (!function_exists('get_state')) {
    function get_state($country, $code)
    {
        return $country->getStates()->findOne(['code' => $code]);
    }
}


if (!function_exists('stock')) {
    function stock()
    {
        return env('STOCK', false);
    }
}

// Prepare Monthly Data for chart
if (!function_exists('chartData')) {
    function chartData($model, $df, $d, $to, $year = null, $month = null)
    {
        $data = $model::without(['customer', 'taxes'])->active()->mine()
        ->where('date', 'like', ($year && $month ? "%{$year}-{$month}%" : "%{$year}%"))
        ->get(['date', 'grand_total'])->groupBy(function ($data) use ($df) {
            return \Carbon\Carbon::parse($data->date)->format($df);
        });

        $result = [];
        foreach ($data as $key => $items) {
            $result[(int) \Carbon\Carbon::parse($key)->format($d)] = formatDecimal($items->sum('grand_total'), 2);
        }
        ksort($result);

        return collect($result)->$to()->flatten();
    }
}
