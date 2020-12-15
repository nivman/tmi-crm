<?php


namespace App\Http\Controllers\mail;


use App\EmailSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class EmailController extends Controller
{
    public function index()
    {
        $emailSettings = EmailSettings::all();
        if(count($emailSettings->toArray()) > 0) {
            return EmailSettings::all();
        }

        return response()->json()->setData(null);
    }

    /**
     * @param EmailRequest $request
     */
    public function store(EmailRequest $request)
    {
        $v = $request->validated();

        EmailSettings::create($v);
    }

    public function update(EmailRequest $request , EmailSettings $emailSettings)
    {

        $v = $request->validated();
        $emailSettings->update($v);
        return $emailSettings;
    }
}