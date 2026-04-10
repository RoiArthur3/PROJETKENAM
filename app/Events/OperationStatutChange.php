<?php

namespace App\Events;

use App\Models\Operation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OperationStatutChange
{
    use Dispatchable, SerializesModels;

    public $operation;
    public $ancienStatut;
    public $nouveauStatut;

    public function __construct(Operation $operation, string $ancienStatut, string $nouveauStatut)
    {
        $this->operation = $operation;
        $this->ancienStatut = $ancienStatut;
        $this->nouveauStatut = $nouveauStatut;
    }
}
