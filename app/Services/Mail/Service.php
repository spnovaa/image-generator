<?php

namespace App\Services\Mail;

use App\Mail\ImageGeneratorMail;
use Illuminate\Support\Facades\Mail;

class Service
{
    /**
     * @param string $email
     * @param string $text
     * @return void
     */
    public function send(string $email, string $text)
    {
        Mail::to($email)->send(new ImageGeneratorMail([
            'title' => 'Message From Mostafa Rahmati',
            'body' => $text
        ]));
    }
}
