<?php

namespace App\Mail;

use App\Models\Parcel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParcelNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Parcel $parcel;
    public string $event;
    public array $messageData;

    public function __construct(Parcel $parcel, string $event, array $messageData)
    {
        $this->parcel = $parcel;
        $this->event = $event;
        $this->messageData = $messageData;
    }

    public function build()
    {
        $trackingUrl = url('/login');

        return $this->subject($this->messageData['title'] ?? ('Mise à jour colis ' . $this->parcel->tracking_number))
            ->view('emails.parcels.status-update')
            ->with([
                'parcel' => $this->parcel,
                'event' => $this->event,
                'messageData' => $this->messageData,
                'trackingUrl' => $trackingUrl,
            ]);
    }
}