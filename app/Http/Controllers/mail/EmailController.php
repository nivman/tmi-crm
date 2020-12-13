<?php


namespace App\Http\Controllers\mail;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class EmailController extends Controller
{
    public function create()
    {

    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        dd($request->request);
    }
}