<?php

namespace App\Modules\Auth\Listener;

use App\Modules\Auth\Event\UserRegisterEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Product\Product;
use App\Helper\Mail\MailService;

class SendEmailToNewUserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(public MailService $mailService)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserRegisterEvent  $event
     * @return void
     */
    public function handle(UserRegisterEvent $event)
    {
        $mailTemplate = 'welcome';
        $mailData = [
            'subject' => 'TEST EVENT'
        ];
        $event->user->email && $this->mailService->sendMail($mailTemplate, $mailData, $event->user->email);
    }
}
