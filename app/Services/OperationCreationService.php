<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationCreationService
{
    public function __construct(
        private readonly OperationValidationService $validationService,
        private readonly OperationNotificationService $notificationService,
    ) {
    }

    public function createFromMainForm(array $validated, User $user, array $options = []): array
    {
        $operation = $this->persistOperation(
            [
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? '',
                'montant' => (float) ($validated['montant'] ?? 0),
                'priorite' => $validated['priorite'],
                'echeance' => $validated['echeance'] ?? null,
                'operational_service_id' => $validated['destinataire_principal'],
                'type_operation_id' => $validated['type_operation_id'],
                'client_id' => $validated['client_id'] ?? null,
            ],
            $user,
            $options,
        );

        return [
            'operation' => $operation,
            'warning' => $this->sendNotificationIfNeeded($operation, $options),
        ];
    }

    public function createFromTerrain(array $validated, User $user): array
    {
        $description = $this->buildTerrainDescription($validated);

        $operation = $this->persistOperation(
            [
                'titre' => $validated['titre'],
                'description' => $description,
                'montant' => (float) ($validated['montant_estime'] ?? 0),
                'priorite' => !empty($validated['urgence']) ? 'urgente' : 'moyenne',
                'echeance' => null,
                'operational_service_id' => $validated['service_operationnel_id'],
                'type_operation_id' => $validated['type_operation_id'],
                'client_id' => $validated['client_id'] ?? null,
            ],
            $user,
            [
                'is_draft' => false,
                'custom_validators' => [],
                'cc_services' => '',
            ],
        );

        return [
            'operation' => $operation,
            'warning' => $this->sendNotificationIfNeeded($operation, []),
        ];
    }

    public function createProject(array $validated, User $user): array
    {
        $operation = $this->persistOperation(
            [
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? '',
                'montant' => 0,
                'priorite' => $validated['priorite'] ?? 'moyenne',
                'echeance' => $validated['echeance'] ?? null,
                'operational_service_id' => null,
                'type_operation_id' => null,
                'client_id' => $validated['client_id'] ?? null,
            ],
            $user,
            [
                'is_draft' => true,
                'custom_validators' => [],
                'cc_services' => '',
            ],
        );

        if (!empty($validated['service'])) {
            $operation->service = $validated['service'];
        }

        if (!empty($validated['responsable_name'])) {
            $operation->responsable_name = $validated['responsable_name'];
        }

        if ($operation->isDirty()) {
            $operation->save();
        }

        return [
            'operation' => $operation,
            'warning' => null,
        ];
    }

    private function persistOperation(array $attributes, User $user, array $options): Operation
    {
        $isDraft = (bool) ($options['is_draft'] ?? false);
        $files = $options['files'] ?? [];
        $customValidators = array_values(array_filter($options['custom_validators'] ?? []));

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return DB::transaction(function () use ($attributes, $user, $isDraft, $files, $customValidators) {
                    $operation = Operation::create([
                        'titre' => $attributes['titre'],
                        'description' => $attributes['description'] ?? '',
                        'montant' => (float) ($attributes['montant'] ?? 0),
                        'priorite' => $attributes['priorite'],
                        'echeance' => $attributes['echeance'] ?? null,
                        'operational_service_id' => $attributes['operational_service_id'] ?? null,
                        'type_operation_id' => $attributes['type_operation_id'] ?? null,
                        'client_id' => $attributes['client_id'] ?? null,
                        'type' => 'operation',
                        'statut_courant' => $isDraft ? 'brouillon' : 'pending_validation',
                        'user_id' => $user->id,
                        'demandeur_name' => $user->name,
                        'demandeur_email' => $user->email,
                        'date_operation' => now(),
                        'numero_operation' => OperationNumberService::generateOperationNumber(),
                    ]);

                    $this->storeFiles($operation, $files, $user);

                    if (!$isDraft) {
                        $this->validationService->setupValidationChain(
                            $operation,
                            $attributes['operational_service_id'],
                            $customValidators,
                        );
                    }

                    return $operation;
                });
            } catch (\Throwable $e) {
                if ($this->isDuplicateOperationNumberException($e) && $attempt < 4) {
                    usleep(50000);
                    continue;
                }

                throw $e;
            }
        }

        throw new \RuntimeException('Impossible de générer un numéro d\'opération unique.');
    }

    private function storeFiles(Operation $operation, array $files, User $user): void
    {
        $normalizedFiles = array_filter($files, fn($file) => $file instanceof UploadedFile);

        if ($normalizedFiles === []) {
            return;
        }

        $payload = [];

        foreach ($normalizedFiles as $file) {
            $path = $file->store('operations/' . $operation->id . '/fichiers', 'public');
            $payload[] = [
                'operation_id' => $operation->id,
                'nom' => $file->getClientOriginalName(),
                'chemin' => $path,
                'type_mime' => $file->getMimeType(),
                'taille' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'uploaded_by' => $user->id,
            ];
        }

        if ($payload !== []) {
            $operation->fichiers()->createMany($payload);
        }
    }

    private function sendNotificationIfNeeded(Operation $operation, array $options): ?string
    {
        if (($options['is_draft'] ?? false) === true) {
            return null;
        }

        try {
            $mailSent = $this->notificationService->sendValidationRequestMail(
                $operation,
                (string) ($options['cc_services'] ?? ''),
            );

            return $mailSent
                ? null
                : 'L\'opération a été créée, mais la notification email n\'a pas pu être envoyée.';
        } catch (\Throwable $e) {
            Log::error('Echec envoi notification après création opération', [
                'operation_id' => $operation->id,
                'user_id' => $operation->user_id,
                'message' => $e->getMessage(),
            ]);

            return 'L\'opération a été créée, mais l\'envoi du mail a échoué.';
        }
    }

    private function buildTerrainDescription(array $validated): string
    {
        $parts = [];

        if (!empty($validated['description'])) {
            $parts[] = trim($validated['description']);
        }

        $metadata = array_filter([
            '[SOURCE:TERRAIN]',
            !empty($validated['lieu_intervention']) ? 'Lieu: ' . $validated['lieu_intervention'] : null,
            isset($validated['latitude'], $validated['longitude']) ? 'GPS: ' . $validated['latitude'] . ', ' . $validated['longitude'] : null,
            !empty($validated['urgence']) ? 'Urgence: Oui' : 'Urgence: Non',
            !empty($validated['notes_terrain']) ? 'Notes terrain: ' . $validated['notes_terrain'] : null,
        ]);

        if ($metadata !== []) {
            $parts[] = implode(PHP_EOL, $metadata);
        }

        return trim(implode(PHP_EOL . PHP_EOL, $parts));
    }

    private function isDuplicateOperationNumberException(\Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, 'operations_numero_operation_unique')
            || str_contains($message, 'Duplicate entry');
    }
}