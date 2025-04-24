<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class testResend extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Correo de prueba desde Resend')
            ->html('<h1>¡Todo funciona bien!</h1><p>Este es un correo enviado desde Laravel con Resend.</p>');
    }
}
