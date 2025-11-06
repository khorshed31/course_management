<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMailable extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public bool $isConfirmation;

    public function __construct(array $data, bool $isConfirmation = false)
    {
        $this->data = $data;
        $this->isConfirmation = $isConfirmation;

        // If you queue mail and want to wait for DB commit:
        // $this->afterCommit();
    }

    public function build()
    {
        $subject = $this->isConfirmation
            ? 'We received your message'
            : 'New contact message from '.$this->data['name'];

        // Always send "from" your app's configured address
        $this->from(config('mail.from.address'), config('mail.from.name'))
             ->subject($subject);

        // For the admin copy, make replies go to the user
        if (!$this->isConfirmation && !empty($this->data['email'])) {
            $this->replyTo($this->data['email'], $this->data['name']);
        }

        return $this->markdown('emails.contact_message', [
            'data' => $this->data,
            'isConfirmation' => $this->isConfirmation,
        ]);
    }
}
