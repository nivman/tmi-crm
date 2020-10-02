<?php

// App Config
if (!function_exists('app_config')) {
    function app_config($label = null)
    {
        //TODO go to thew bottom of all the installation thing  and the settings.json file
        //For now i live it like that
        if (!($settings = app()->request->session()->get('appSettings'))) {
            $settings = json_decode(Storage::disk('local')->get('settings.json'), true);
        }

        return true;
    }
}

// App Version
if (!function_exists('app_version')) {
    function app_version()
    {
        $cf = json_decode(file_get_contents(base_path('composer.json')));
        return $cf->version;
    }
}

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

// Is Demo Enabled
if (!function_exists('php_date_formate')) {
    function php_date_formate()
    {
        $settings = session()->get('appSettings');
        return (string) str_replace(['YYYY', 'MM', 'DD'], ['Y', 'm', 'd'], $settings['ac']['dateformat']);
    }
}

// Is Demo Enabled
if (!function_exists('demo')) {
    function demo()
    {
        return env('DEMO', false);
    }
}

// Is Demo Enabled
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
