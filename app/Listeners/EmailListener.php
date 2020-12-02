<?php

namespace App\Listeners;

use App\Events\EmailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailEvent  $event
     * @return void
     */
    public function handle(EmailEvent $event)
    {
        //
    }
}
