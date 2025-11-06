<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReplyMailable extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->afterCommit();
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->payload['reply_to_email'] ?? config('mail.from.address'),
                      $this->payload['reply_to_name'] ?? config('mail.from.name'))
            ->subject($this->payload['subject'])
            ->markdown('emails.admin_reply', ['p' => $this->payload]);
    }
}

