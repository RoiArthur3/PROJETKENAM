<?php

namespace App\Mail;

use App\Models\Operation;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $steps;

    /**
     * Create a new message instance.
     */
    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
        $this->steps = DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->leftJoin('users', 'operation_service_validation.validateur_id', '=', 'users.id')
            ->select(
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'users.name as validator_name'
            )
            ->orderBy('ordre_validation')
            ->get();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Opération #' . $this->operation->id . ' approuvée avec succès',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.operations.approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
