<?php

use App\Models\DepenseCaisse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill caisse balances for validated decaissements that were created
     * while the observer provider was not loaded.
     */
    public function up(): void
    {
        $validStatuses = ['validé', 'valide', 'validated', 'valid'];

        DB::transaction(function () use ($validStatuses): void {
            $fallbackUserId = DB::table('users')->min('id');

            $depensesToBackfill = DB::table('depense_caisses as d')
                ->leftJoin('mouvement_caisses as m', function ($join) {
                    $join->on('m.source_id', '=', 'd.id')
                        ->where('m.source_type', '=', DepenseCaisse::class);
                })
                ->whereNull('m.id')
                ->whereNull('d.deleted_at')
                ->whereNotNull('d.caisse_id')
                ->whereRaw('LOWER(TRIM(d.statut)) IN (?, ?, ?, ?)', $validStatuses)
                ->select([
                    'd.id',
                    'd.caisse_id',
                    'd.montant',
                    'd.libelle',
                    'd.reference',
                    'd.date_depense',
                    'd.created_by',
                    'd.createur_id',
                ])
                ->orderBy('d.id')
                ->get();

            foreach ($depensesToBackfill as $depense) {
                $montant = (float) $depense->montant;

                DB::table('caisses')
                    ->where('id', $depense->caisse_id)
                    ->decrement('solde_actuel', $montant);

                DB::table('mouvement_caisses')->insert([
                    'reference' => 'MVT-REC-' . $depense->id,
                    'caisse_id' => $depense->caisse_id,
                    'type_mouvement' => 'sortie',
                    'libelle' => 'Decaissement',
                    'description' => 'Rattrapage decaissement #' . $depense->id . ' (' . ($depense->reference ?? 'sans-reference') . ')',
                    'montant' => abs($montant),
                    'devise' => 'XOF',
                    'date_mouvement' => $depense->date_depense ?? now(),
                    'source_type' => DepenseCaisse::class,
                    'source_id' => $depense->id,
                    'created_by' => $depense->created_by ?? $depense->createur_id ?? $fallbackUserId,
                    'updated_by' => null,
                    'statut' => 'brouillon',
                    'notes' => 'Rattrapage automatique solde caisse suite a observer non charge.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty.
    }
};
