<?php

namespace App\Helper\Mail;

use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class MailService extends Mailable
{
    /**
     * mail service.
     * @param string $mailTemplate string $recipient array $mailData
     */
    public function sendMail(string $mailTemplate, array $mailData, string $recipient, array $attaches = [])
    {
        try {
            Mail::send($mailTemplate, $mailData, function ($message) use ($recipient, $mailData, $attaches) {

                // NOTED: if sender address or sender name are null laravel will get default in config/mail.php
                if (isset($mailData['sender_address']) && isset($mailData['sender_name'])) {
                    $message->from($mailData['sender_address'], $mailData['sender_name']);
                }
                
                // if has attach files
                if (count($attaches) > 0) {
                    foreach ($attaches as $attach) {
                        $message->attach($attach);
                    }
                }

                $message->to($recipient)->subject($mailData['subject']);
            });
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function sendMails(string $mailTemplate, array $mailData, array $recipients, array $attaches = [])
    {
        foreach ($recipients as $recipient) {
            $this->sendMail($mailTemplate, $mailData, $recipient, $attaches);
        }
    }
}
