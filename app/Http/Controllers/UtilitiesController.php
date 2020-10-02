<?php

namespace App\Http\Controllers;

use App\Activity;

class UtilitiesController extends Controller
{
    public function logs()
    {
        return response()->json(Activity::vueTable(Activity::$columns));
    }

    public function showLog(Activity $activity)
    {
        $activity->load('subject');
        return $activity;
    }
}
