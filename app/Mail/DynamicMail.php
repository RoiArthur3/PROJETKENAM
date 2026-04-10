<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $subject;
    public $data;

    public function __construct($templateName, $data = [])
    {
        $this->subject = $data['subject'] ?? 'Notification';
        $this->content = $data['message'] ?? '';
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject($this->subject)
                   ->view('emails.dynamic')
                   ->with([
                       'content' => $this->content,
                       'data' => $this->data,
                       'subject' => $this->subject
                   ]);
    }
}
