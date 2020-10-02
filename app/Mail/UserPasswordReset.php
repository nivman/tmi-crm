<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserPasswoedReset extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $company = \App\Company::first();
        return $this->subject('Admin reset your password!')
            ->view('mail.user.reset')
            ->with([
                'company' => $company,
                'login_url' => url('/login'),
                'name' => $this->data['name'],
                'email' => $this->data['email'],
                'username' => $this->data['username'],
                'password' => $this->data['password'],
            ]);
    }
}
