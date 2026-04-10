<?php

namespace App\Imports;

use App\Models\Fournisseur;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class FournisseursImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    protected int $rowCount = 0;
    protected int $createdCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;
    protected array $errors = [];

    public function __construct(protected string $modeImport = 'create')
    {
    }

    public function model(array $row)
    {
        $this->rowCount++;
        
        // Chercher un fournisseur existant par email ou RCCM
        $fournisseur = null;
        
        if (!empty($row['email'])) {
            $fournisseur = Fournisseur::where('email', $row['email'])->first();
        }
        
        if (!$fournisseur && !empty($row['rccm'])) {
            $fournisseur = Fournisseur::where('rccm', $row['rccm'])->first();
        }

        // Mode mise à jour
        if ($this->modeImport === 'update' && $fournisseur) {
            $fournisseur->update($this->extractData($row));
            $this->updatedCount++;
            return $fournisseur;
        }

        // Mode création
        if (!$fournisseur) {
            $this->createdCount++;
            return new Fournisseur($this->extractData($row));
        }

        $this->skippedCount++;
        return null;
    }

    protected function extractData(array $row): array
    {
        return [
            'nom' => $row['nom'] ?? null,
            'email' => $row['email'] ?? null,
            'telephone' => $row['telephone'] ?? null,
            'adresse' => $row['adresse'] ?? null,
            'ville' => $row['ville'] ?? null,
            'pays' => $row['pays'] ?? null,
            'rccm' => $row['rccm'] ?? null,
            'ifu' => $row['ifu'] ?? null,
            'est_actif' => (bool) ($row['statut'] ?? true),
        ];
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row " . $failure->row() . ": " . implode(", ", $failure->errors());
        }
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

