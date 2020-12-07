<?php


namespace App\Helpers;


use Carbon\Carbon;

class Date
{
    public static function formatDateTime($request)
    {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        $start = Carbon::createFromFormat('d/m/Y H:i', $start_date)->format('Y-m-d H:i');
        $end = Carbon::createFromFormat('d/m/Y H:i', $end_date)->format('Y-m-d H:i');

        return ['start_date' => $start, 'end_date' => $end];
    }

    public static function formatDate($request)
    {
        if (isset($request['date_to_complete'])) {
            return Carbon::createFromFormat('d/m/Y', $request['date_to_complete'])->format('Y-m-d');
        }
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        $start = Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
        $end = Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');

        return ['start_date' => $start, 'end_date' => $end];
    }

    public static function formatDateGeneralTime($request, $dateName)
    {

        $dateValue =  $request[$dateName];

        return Carbon::createFromFormat('d/m/Y H:i', $dateValue)->format('Y-m-d H:i');

    }

    public static function convertToViewFormat($dateToFormat)
    {

        return Carbon::createFromFormat('Y-m-d H:i:s', $dateToFormat)->format('d/m/Y H:i');
    }
}