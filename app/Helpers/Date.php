<?php


namespace App\Helpers;


use Carbon\Carbon;

class Date
{
        public static function formatDate ($request) {

            $start_date = $request['start_date'];
            $end_date = $request['end_date'];

            $start = Carbon::createFromFormat('d/m/Y H:i', $start_date)->format('Y-m-d H:i');
            $end = Carbon::createFromFormat('d/m/Y H:i', $end_date)->format('Y-m-d H:i');

            return ['start_date' => $start, 'end_date' => $end];
        }
}