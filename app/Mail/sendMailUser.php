<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendMailUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->to($user->email, $user->name);
    }

    public function build()
    {
        return $this->subject('Account created')
            ->view('user_created')
            ->with(['user' => $this->user]);
    }
}
