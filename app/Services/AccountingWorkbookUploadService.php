<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Caisse;
use App\Models\Facture;
use App\Models\FactureFournisseur;
use App\Models\Fournisseur;
use App\Models\MouvementCaisse;
use App\Models\PaiementFournisseur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountingWorkbookUploadService
{
    public function __construct(private readonly ParamComptaImportService $paramComptaImportService)
    {
    }

    public function import(string $importType, string $filePath, bool $dryRun = false): array
    {
        return match ($importType) {
            'param_compta' => $this->paramComptaImportService->import($filePath, $dryRun),
            'creances_clients' => $this->importCreancesClients($filePath, $dryRun),
            'dettes_fournisseurs' => $this->importDettesFournisseurs($filePath, $dryRun),
            'caisse_logistique' => $this->importCaisseLogistique($filePath, $dryRun),
            default => throw new \InvalidArgumentException('Type de fichier non supporte: ' . $importType),
        };
    }

    private function importCreancesClients(string $filePath, bool $dryRun): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);

        $stats = [
            'dry_run' => $dryRun,
            'source' => $filePath,
            'clients_created' => 0,
            'clients_updated' => 0,
            'factures_created' => 0,
            'factures_updated' => 0,
            'reglements_detected' => 0,
            'encaissements_created' => 0,
            'encaissements_updated' => 0,
            'warnings' => [],
        ];

        DB::beginTransaction();
        try {
            $this->importClientsSheet($spreadsheet->getSheetByName('CLIENTS-CREANCES'), $stats);
            $this->importClientInvoicesSheet($spreadsheet->getSheetByName('FACTURES'), $stats);
            $this->scanReglementsSheet($spreadsheet->getSheetByName('REGLEMENT'), $stats);

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            return $stats;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function importDettesFournisseurs(string $filePath, bool $dryRun): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);

        $stats = [
            'dry_run' => $dryRun,
            'source' => $filePath,
            'fournisseurs_created' => 0,
            'fournisseurs_updated' => 0,
            'factures_rows_detected' => 0,
            'paiements_rows_detected' => 0,
            'factures_fournisseurs_created' => 0,
            'factures_fournisseurs_updated' => 0,
            'paiements_fournisseurs_created' => 0,
            'paiements_fournisseurs_updated' => 0,
            'warnings' => [],
        ];

        DB::beginTransaction();
        try {
            $this->importSuppliersFromDebtWorkbook($spreadsheet->getSheetByName('FACTURES FOURNISSEURS'), $stats);
            $this->scanSupplierPaymentsSheet($spreadsheet->getSheetByName('PAIEMENTS DES DETTES'), $stats);

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            return $stats;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function importCaisseLogistique(string $filePath, bool $dryRun): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);

        $stats = [
            'dry_run' => $dryRun,
            'source' => $filePath,
            'sheets_detected' => $spreadsheet->getSheetCount(),
            'mouvements_detected' => 0,
            'mouvements_created' => 0,
            'mouvements_ignored' => 0,
            'warnings' => [],
        ];

        if (!Schema::hasTable('mouvements_caisse')) {
            $stats['warnings'][] = 'Table mouvements_caisse absente.';
            return $stats;
        }

        $caisseId = $this->resolveImportCaisseId($stats);
        if (!$caisseId) {
            $stats['warnings'][] = 'Aucune caisse disponible pour enregistrer les mouvements.';
            return $stats;
        }

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $rows = $sheet->toArray(null, false, true, true);
            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex < 4) {
                    continue;
                }

                $this->upsertCaisseMovement(
                    $caisseId,
                    $sheet->getTitle(),
                    $row['A'] ?? null,
                    $row['B'] ?? null,
                    $row['C'] ?? null,
                    MouvementCaisse::TYPE_APPROVISIONNEMENT,
                    $stats
                );

                $this->upsertCaisseMovement(
                    $caisseId,
                    $sheet->getTitle(),
                    $row['D'] ?? null,
                    $row['E'] ?? null,
                    $row['F'] ?? null,
                    MouvementCaisse::TYPE_DEPENSE,
                    $stats
                );

                $this->upsertCaisseMovement(
                    $caisseId,
                    $sheet->getTitle(),
                    $row['G'] ?? null,
                    $row['H'] ?? null,
                    $row['I'] ?? null,
                    MouvementCaisse::TYPE_DEPENSE,
                    $stats
                );
            }
        }

        if ($dryRun) {
            DB::rollBack();
        } else {
            // no-op
        }

        return $stats;
    }

    private function importClientsSheet(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('clients')) {
            $stats['warnings'][] = 'Feuille CLIENTS-CREANCES ou table clients absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'entite' => ['entite'],
            'nom_client' => ['nom client'],
            'categorie' => ['categorie'],
            'telephone' => ['telephone'],
            'activite' => ['activite'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans CLIENTS-CREANCES.';
            return;
        }

        foreach ($rows as $row) {
            $name = $this->cleanCell($row[$headerMap['nom_client']] ?? null);
            if ($name === '' || str_starts_with($name, '=')) {
                continue;
            }

            $payload = [
                'raison_sociale' => $name,
                'nom' => $name,
                'telephone' => $this->cleanCell($row[$headerMap['telephone']] ?? null),
                'type' => $this->cleanCell($row[$headerMap['categorie']] ?? null) ?: null,
                'entreprise' => $this->cleanCell($row[$headerMap['entite']] ?? null) ?: null,
                'description' => $this->cleanCell($row[$headerMap['activite']] ?? null) ?: null,
                'actif' => true,
            ];
            $payload = $this->filterPayloadForTable('clients', $payload);

            $client = $this->findClientByName($name);

            if ($client) {
                $client->fill(array_filter($payload, fn ($value) => $value !== null && $value !== ''));
                $client->save();
                $stats['clients_updated']++;
            } else {
                if (Schema::hasColumn('clients', 'created_by') && Auth::id()) {
                    $payload['created_by'] = Auth::id();
                }
                if (Schema::hasColumn('clients', 'user_id') && Auth::id()) {
                    $payload['user_id'] = Auth::id();
                }
                if (Schema::hasColumn('clients', 'contact_nom')) {
                    $payload['contact_nom'] = $name;
                }
                if (Schema::hasColumn('clients', 'nom_complet')) {
                    $payload['nom_complet'] = $name;
                }

                $generatedCode = Client::generateClientCode();
                if (Schema::hasColumn('clients', 'code')) {
                    $payload['code'] = $generatedCode;
                }
                if (Schema::hasColumn('clients', 'code_client')) {
                    $payload['code_client'] = $generatedCode;
                }
                if (Schema::hasColumn('clients', 'client_code')) {
                    $payload['client_code'] = $generatedCode;
                }

                $payload = $this->filterPayloadForTable('clients', $payload);
                $payload = $this->completeRequiredColumns('clients', $payload, [
                    'name' => $name,
                    'code' => $generatedCode,
                ]);

                Client::query()->create($payload);
                $stats['clients_created']++;
            }
        }
    }

    private function importClientInvoicesSheet(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('factures')) {
            $stats['warnings'][] = 'Feuille FACTURES ou table factures absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'date_facturation' => ['date de facturation'],
            'numero_facture' => ['num de facture'],
            'client' => ['client'],
            'montant_ht' => ['montant ht'],
            'montant_ttc' => ['montant ttc'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans FACTURES.';
            return;
        }

        foreach ($rows as $row) {
            $numero = $this->cleanCell($row[$headerMap['numero_facture']] ?? null);
            $clientName = $this->cleanCell($row[$headerMap['client']] ?? null);

            if ($numero === '' || $clientName === '' || str_starts_with($numero, '=') || str_starts_with($clientName, '=')) {
                continue;
            }

            $client = $this->findClientByName($clientName);

            if (!$client) {
                continue;
            }

            $montantHt = $this->parseNumber($row[$headerMap['montant_ht']] ?? null);
            $montantTtc = $this->parseNumber($row[$headerMap['montant_ttc']] ?? null);
            if ($montantTtc <= 0 && $montantHt > 0) {
                $montantTtc = round($montantHt * 1.18, 2);
            }

            $dateFacture = $this->parseDate($row[$headerMap['date_facturation']] ?? null);
            if (!$dateFacture) {
                $dateFacture = now()->toDateString();
            }

            $payload = [
                'numero' => $numero,
                'client_id' => $client->id,
                'date_facture' => $dateFacture,
                'montant_ht' => $montantHt,
                'tva' => $montantTtc > 0 ? max(0, $montantTtc - $montantHt) : 0,
                'montant_ttc' => $montantTtc,
                'statut' => 'en_attente',
            ];

            if (Schema::hasColumn('factures', 'created_by') && Auth::id()) {
                $payload['created_by'] = Auth::id();
            }
            $payload = $this->filterPayloadForTable('factures', $payload);
            $payload = $this->completeRequiredColumns('factures', $payload, [
                'code' => $numero,
            ]);

            $facture = Facture::query()->where('numero', $numero)->first();
            if ($facture) {
                $facture->fill($payload);
                $facture->save();
                $stats['factures_updated']++;
            } else {
                Facture::query()->create($payload);
                $stats['factures_created']++;
            }
        }
    }

    private function scanReglementsSheet(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet) {
            $stats['warnings'][] = 'Feuille REGLEMENT absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'clients' => ['clients'],
            'montant_ttc' => ['montant ttc'],
            'date_paiement' => ['date de paiement'],
        ]);

        if (!$headerMap) {
            return;
        }

        foreach ($rows as $row) {
            $client = $this->cleanCell($row[$headerMap['clients']] ?? null);
            $montant = $this->parseNumber($row[$headerMap['montant_ttc']] ?? null);
            if ($client !== '' && $montant > 0) {
                $stats['reglements_detected']++;

                if (!Schema::hasTable('encaissements')) {
                    continue;
                }

                $clientModel = $this->findClientByName($client);
                if (!$clientModel) {
                    continue;
                }

                $datePaiement = $this->parseDate($row[$headerMap['date_paiement']] ?? null) ?: now()->toDateString();
                $modePaiement = $this->cleanCell($row['G'] ?? null);
                $referencePaiement = $this->cleanCell($row['H'] ?? null);
                $facture = $this->findClientInvoiceForPayment((int) $clientModel->id, $montant, $datePaiement);

                $lookup = [
                    'client_id' => (int) $clientModel->id,
                    'date_encaissement' => $datePaiement,
                    'montant' => $montant,
                    'reference_paiement' => $referencePaiement !== '' ? $referencePaiement : null,
                ];
                $lookup = $this->filterPayloadForTable('encaissements', $lookup);

                $payload = [
                    'type' => 'reglement_client',
                    'montant' => $montant,
                    'date_encaissement' => $datePaiement,
                    'mode_paiement' => $modePaiement !== '' ? $modePaiement : 'non_precise',
                    'reference_paiement' => $referencePaiement !== '' ? $referencePaiement : null,
                    'description' => 'Import REGLEMENT - ' . $client,
                    'statut' => 'valide',
                    'facture_id' => $facture?->id,
                    'client_id' => (int) $clientModel->id,
                ];

                if (Schema::hasColumn('encaissements', 'created_by') && Auth::id()) {
                    $payload['created_by'] = Auth::id();
                }
                if (Schema::hasColumn('encaissements', 'user_id') && Auth::id()) {
                    $payload['user_id'] = Auth::id();
                }
                if (Schema::hasColumn('encaissements', 'reference')) {
                    $payload['reference'] = 'ENC-IMP-' . strtoupper(substr(md5($client . '|' . $datePaiement . '|' . $montant . '|' . $referencePaiement), 0, 10));
                }

                $payload = $this->filterPayloadForTable('encaissements', $payload);

                if (empty($lookup)) {
                    $lookup = [
                        'reference' => $payload['reference'] ?? null,
                    ];
                    $lookup = array_filter($lookup, fn ($v) => $v !== null && $v !== '');
                }

                $encaissement = !empty($lookup)
                    ? DB::table('encaissements')->where($lookup)->first()
                    : null;

                if ($encaissement) {
                    DB::table('encaissements')->where('id', $encaissement->id)->update(array_merge($payload, [
                        'updated_at' => now(),
                    ]));
                    $stats['encaissements_updated']++;
                } else {
                    $payload = array_merge($payload, [
                        'type' => $payload['type'] ?? 'reglement_client',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('encaissements')->insert($payload);
                    $stats['encaissements_created']++;
                }
            }
        }
    }

    private function importSuppliersFromDebtWorkbook(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('fournisseurs')) {
            $stats['warnings'][] = 'Feuille FACTURES FOURNISSEURS ou table fournisseurs absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'fournisseurs' => ['fournisseurs'],
            'date' => ['date'],
            'numero_facture' => ['n f'],
            'montant_ht_biens' => ['montant ht biens'],
            'montant_ht_services' => ['montant ht services'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans FACTURES FOURNISSEURS.';
            return;
        }

        foreach ($rows as $row) {
            $name = $this->cleanCell($row[$headerMap['fournisseurs']] ?? null);
            if ($name === '') {
                continue;
            }

            $supplier = $this->findSupplierByName($name);

            if ($supplier) {
                $supplier->est_actif = true;
                $supplier->save();
                $stats['fournisseurs_updated']++;
            } else {
                $payload = [
                    'raison_sociale' => $name,
                    'est_actif' => true,
                ];

                if (Schema::hasColumn('fournisseurs', 'user_id') && Auth::id()) {
                    $payload['user_id'] = Auth::id();
                }
                $payload = $this->filterPayloadForTable('fournisseurs', $payload);
                $payload = $this->completeRequiredColumns('fournisseurs', $payload, [
                    'name' => $name,
                    'code' => 'FOURN-' . strtoupper(substr(md5($name), 0, 8)),
                ]);

                Fournisseur::query()->create($payload);
                $stats['fournisseurs_created']++;
            }

            $montant = $this->parseNumber($row[$headerMap['montant_ht_biens']] ?? null)
                + $this->parseNumber($row[$headerMap['montant_ht_services']] ?? null);
            if ($montant > 0) {
                $stats['factures_rows_detected']++;

                if ($montant > 99999999.99) {
                    $numeroOverflow = $this->cleanCell($row[$headerMap['numero_facture']] ?? null);
                    $stats['warnings'][] = 'Facture fournisseur ignoree (montant hors plage): ' . ($numeroOverflow !== '' ? $numeroOverflow : '[sans numero]');
                    continue;
                }

                if (!Schema::hasTable('facture_fournisseurs')) {
                    continue;
                }

                $supplierModel = $this->findSupplierByName($name);
                if (!$supplierModel) {
                    continue;
                }

                $numero = $this->cleanCell($row[$headerMap['numero_facture']] ?? null);
                if ($numero === '') {
                    $numero = 'IMP-' . strtoupper(substr(md5($name . '|' . $this->cleanCell($row[$headerMap['date']] ?? null) . '|' . $montant), 0, 10));
                }

                $dateFacture = $this->parseDate($row[$headerMap['date']] ?? null) ?: now()->toDateString();

                $userId = $this->resolveSystemUserId();
                $commandeId = $this->resolveDefaultSupplierOrderId((int) $supplierModel->id, $dateFacture, $userId);
                if (!$commandeId) {
                    $stats['warnings'][] = 'Commande fournisseur introuvable pour ' . $name . ' (facture ' . $numero . ').';
                    continue;
                }

                $payload = [
                    'fournisseur_id' => (int) $supplierModel->id,
                    'commande_id' => $commandeId,
                    'numero_facture' => $numero,
                    'date_facture' => $dateFacture,
                    'date_echeance' => $dateFacture,
                    'conditions_paiement' => 'Import comptable',
                    'montant_ht' => $montant,
                    'tva' => 0,
                    'montant_ttc' => $montant,
                    'montant_paye' => 0,
                    'reste_a_payer' => $montant,
                    'statut' => 'non_payee',
                    'notes' => 'Import DETTES FOURNISSEURS',
                ];

                if (Schema::hasColumn('facture_fournisseurs', 'reference')) {
                    $payload['reference'] = 'FF-IMP-' . strtoupper(substr(md5($supplierModel->id . '|' . $numero), 0, 10));
                }
                if (Schema::hasColumn('facture_fournisseurs', 'user_id') && $userId) {
                    $payload['user_id'] = $userId;
                }

                $payload = $this->filterPayloadForTable('facture_fournisseurs', $payload);

                $facture = FactureFournisseur::query()
                    ->where('fournisseur_id', (int) $supplierModel->id)
                    ->where('numero_facture', $numero)
                    ->first();

                if ($facture) {
                    $facture->fill($payload);
                    $facture->save();
                    $stats['factures_fournisseurs_updated']++;
                } else {
                    FactureFournisseur::query()->create($payload);
                    $stats['factures_fournisseurs_created']++;
                }
            }
        }
    }

    private function scanSupplierPaymentsSheet(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet) {
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'fournisseur' => ['fournisseur organisme', 'fournisseur/organisme'],
            'montant' => ['montant paye fcfa', 'montant paye (fcfa)'],
            'type_dette' => ['type de dette'],
        ]);

        if (!$headerMap) {
            return;
        }

        foreach ($rows as $row) {
            $name = $this->cleanCell($row[$headerMap['fournisseur']] ?? null);
            $amount = $this->parseNumber($row[$headerMap['montant']] ?? null);
            $typeDette = $this->cleanCell($row[$headerMap['type_dette']] ?? null);

            if ($name !== '' && $amount > 0) {
                if ($typeDette !== '' && !str_contains(mb_strtolower($typeDette), 'fournisseur')) {
                    continue;
                }

                $stats['paiements_rows_detected']++;

                if (!Schema::hasTable('paiement_fournisseurs')) {
                    continue;
                }

                $supplier = $this->findSupplierByName($name);
                if (!$supplier) {
                    $supplierPayload = [
                        'raison_sociale' => $name,
                        'est_actif' => true,
                    ];
                    if (Schema::hasColumn('fournisseurs', 'user_id')) {
                        $userId = $this->resolveSystemUserId();
                        if ($userId) {
                            $supplierPayload['user_id'] = $userId;
                        }
                    }
                    $supplierPayload = $this->filterPayloadForTable('fournisseurs', $supplierPayload);
                    $supplierPayload = $this->completeRequiredColumns('fournisseurs', $supplierPayload, [
                        'name' => $name,
                        'code' => 'FOURN-' . strtoupper(substr(md5($name), 0, 8)),
                    ]);
                    $supplier = Fournisseur::query()->create($supplierPayload);
                    $stats['fournisseurs_created']++;
                }

                $paymentDate = $this->parseDate($row['B'] ?? null) ?: now()->toDateString();
                $reference = $this->cleanCell($row['J'] ?? null);
                $mode = $this->cleanCell($row['H'] ?? null);

                $linkedInvoice = FactureFournisseur::query()
                    ->where('fournisseur_id', (int) $supplier->id)
                    ->where('reste_a_payer', '>', 0)
                    ->orderBy('date_facture')
                    ->orderBy('id')
                    ->first();

                $userId = $this->resolveSystemUserId();
                if (!$linkedInvoice) {
                    $linkedInvoice = $this->createSupplierPlaceholderInvoice($supplier, $amount, $paymentDate, $userId);
                }

                if (!$linkedInvoice) {
                    $stats['warnings'][] = 'Paiement ignore (facture fournisseur non resolue): ' . $name . ' / ' . $amount;
                    continue;
                }

                $lookup = [
                    'fournisseur_id' => (int) $supplier->id,
                    'date_paiement' => $paymentDate,
                    'montant' => $amount,
                    'reference_paiement' => $reference !== '' ? $reference : null,
                ];
                $lookup = $this->filterPayloadForTable('paiement_fournisseurs', $lookup);

                $payload = [
                    'fournisseur_id' => (int) $supplier->id,
                    'facture_id' => (int) $linkedInvoice->id,
                    'date_paiement' => $paymentDate,
                    'montant' => $amount,
                    'mode_paiement' => $mode !== '' ? $mode : 'non_precise',
                    'reference_paiement' => $reference !== '' ? $reference : null,
                    'est_encaisse' => true,
                    'est_annule' => false,
                    'notes' => 'Import PAIEMENTS DES DETTES',
                ];

                if (Schema::hasColumn('paiement_fournisseurs', 'reference')) {
                    $payload['reference'] = 'PF-IMP-' . strtoupper(substr(md5($supplier->id . '|' . $paymentDate . '|' . $amount . '|' . $reference), 0, 10));
                }
                if (Schema::hasColumn('paiement_fournisseurs', 'user_id') && $userId) {
                    $payload['user_id'] = $userId;
                }

                $payload = $this->filterPayloadForTable('paiement_fournisseurs', $payload);

                $paiement = PaiementFournisseur::query()->where($lookup)->first();
                if ($paiement) {
                    $paiement->fill($payload);
                    $paiement->save();
                    $stats['paiements_fournisseurs_updated']++;
                } else {
                    $paiement = PaiementFournisseur::query()->create($payload);
                    $stats['paiements_fournisseurs_created']++;
                }

                if ($linkedInvoice) {
                    $paid = (float) $linkedInvoice->montant_paye;
                    $ttc = (float) $linkedInvoice->montant_ttc;
                    $paid = round($paid + $amount, 2);
                    $remaining = max(0, round($ttc - $paid, 2));
                    $linkedInvoice->montant_paye = $paid;
                    $linkedInvoice->reste_a_payer = $remaining;
                    $linkedInvoice->statut = $remaining <= 0 ? 'payee' : ($paid > 0 ? 'partiellement_payee' : 'non_payee');
                    $linkedInvoice->date_paiement = $remaining <= 0 ? $paymentDate : $linkedInvoice->date_paiement;
                    $linkedInvoice->save();
                }
            }
        }
    }

    private function resolveImportCaisseId(array &$stats): ?int
    {
        $existing = Caisse::query()
            ->where(function ($query): void {
                $query->where('nom', 'CAISSE LOGISTIQUE IMPORT');

                if (Schema::hasColumn('caisses', 'libelle')) {
                    $query->orWhere('libelle', 'CAISSE LOGISTIQUE IMPORT');
                }
            })
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        $payload = [
            'nom' => 'CAISSE LOGISTIQUE IMPORT',
            'libelle' => 'CAISSE LOGISTIQUE IMPORT',
            'type' => 'secondaire',
            'solde_initial' => 0,
            'solde_actuel' => 0,
            'devise' => 'XOF',
            'description' => 'Caisse technique pour import automatique du journal logistique',
            'est_active' => true,
        ];

        $payload = $this->filterPayloadForTable('caisses', $payload);
        $payload = $this->completeRequiredColumns('caisses', $payload, [
            'name' => 'CAISSE LOGISTIQUE IMPORT',
            'code' => 'CAISSE-IMPORT',
        ]);

        try {
            $caisse = Caisse::query()->create($payload);
            return (int) $caisse->id;
        } catch (\Throwable $e) {
            $stats['warnings'][] = 'Impossible de creer la caisse d\'import: ' . $e->getMessage();
            return null;
        }
    }

    private function upsertCaisseMovement(
        int $caisseId,
        string $sheetName,
        mixed $dateValue,
        mixed $labelValue,
        mixed $amountValue,
        string $movementType,
        array &$stats
    ): void {
        $label = $this->cleanCell($labelValue);
        $amount = $this->parseNumber($amountValue);

        if ($label === '' || $amount <= 0) {
            return;
        }

        if ($amount > 9999999999999.99) {
            $stats['mouvements_ignored']++;
            return;
        }

        $stats['mouvements_detected']++;

        $date = $this->parseDate($dateValue) ?: now()->toDateString();
        $description = sprintf('[%s] %s | %s', trim($sheetName), $date, $label);

        $lookup = [
            'caisse_id' => $caisseId,
            'type_mouvement' => $movementType,
            'montant' => $amount,
            'description' => $description,
        ];

        $lookup = $this->filterPayloadForTable('mouvements_caisse', $lookup);
        $payload = $lookup;
        $userId = $this->resolveSystemUserId();
        if (Schema::hasColumn('mouvements_caisse', 'created_by') && $userId) {
            $payload['created_by'] = $userId;
        }

        $existing = MouvementCaisse::query()->where($lookup)->first();
        if ($existing) {
            $stats['mouvements_ignored']++;
            return;
        }

        $payload = $this->completeRequiredColumns('mouvements_caisse', $payload, [
            'name' => $label,
            'code' => $sheetName,
        ]);

        MouvementCaisse::query()->create($payload);
        $stats['mouvements_created']++;
    }

    private function resolveSystemUserId(): ?int
    {
        $authId = Auth::id();
        if ($authId) {
            return (int) $authId;
        }

        if (!Schema::hasTable('users')) {
            return null;
        }

        $id = DB::table('users')->orderBy('id')->value('id');
        return $id ? (int) $id : null;
    }

    private function resolveDefaultSupplierOrderId(int $supplierId, string $date, ?int $userId): ?int
    {
        if (!Schema::hasTable('commande_fournisseurs')) {
            return null;
        }

        $existing = DB::table('commande_fournisseurs')
            ->where('fournisseur_id', $supplierId)
            ->orderBy('id')
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        $payload = [
            'reference' => 'CMD-IMP-' . strtoupper(substr(md5($supplierId . '|' . $date), 0, 10)),
            'fournisseur_id' => $supplierId,
            'date_commande' => $date,
            'montant_ht' => 0,
            'tva' => 0,
            'montant_ttc' => 0,
            'frais_livraison' => 0,
            'remise' => 0,
            'type_remise' => 'pourcentage',
            'conditions_paiement' => 'Import comptable',
            'statut' => 'validee',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('commande_fournisseurs', 'user_id') && $userId) {
            $payload['user_id'] = $userId;
        }

        if (Schema::hasColumn('commande_fournisseurs', 'mode_paiement')) {
            $payload['mode_paiement'] = 'virement';
        }

        $payload = $this->filterPayloadForTable('commande_fournisseurs', $payload);
        if (empty($payload['user_id']) && Schema::hasColumn('commande_fournisseurs', 'user_id') && $userId) {
            $payload['user_id'] = $userId;
        }

        $id = DB::table('commande_fournisseurs')->insertGetId($payload);
        return $id ? (int) $id : null;
    }

    private function createSupplierPlaceholderInvoice(Fournisseur $supplier, float $amount, string $paymentDate, ?int $userId): ?FactureFournisseur
    {
        if (!Schema::hasTable('facture_fournisseurs')) {
            return null;
        }

        $commandeId = $this->resolveDefaultSupplierOrderId((int) $supplier->id, $paymentDate, $userId);
        if (!$commandeId) {
            return null;
        }

        $numero = 'PF-AUTO-' . strtoupper(substr(md5($supplier->id . '|' . $paymentDate . '|' . $amount), 0, 10));
        $existing = FactureFournisseur::query()
            ->where('fournisseur_id', (int) $supplier->id)
            ->where('numero_facture', $numero)
            ->first();

        if ($existing) {
            return $existing;
        }

        $payload = [
            'reference' => 'FF-AUTO-' . strtoupper(substr(md5($numero), 0, 10)),
            'fournisseur_id' => (int) $supplier->id,
            'commande_id' => $commandeId,
            'numero_facture' => $numero,
            'date_facture' => $paymentDate,
            'date_echeance' => $paymentDate,
            'conditions_paiement' => 'Import paiement dettes',
            'montant_ht' => $amount,
            'tva' => 0,
            'montant_ttc' => $amount,
            'montant_paye' => 0,
            'reste_a_payer' => $amount,
            'statut' => 'non_payee',
            'notes' => 'Facture auto-creee pour lier un paiement importe',
        ];

        if (Schema::hasColumn('facture_fournisseurs', 'user_id') && $userId) {
            $payload['user_id'] = $userId;
        }

        $payload = $this->filterPayloadForTable('facture_fournisseurs', $payload);

        return FactureFournisseur::query()->create($payload);
    }

    private function findClientInvoiceForPayment(int $clientId, float $amount, string $paymentDate): ?Facture
    {
        $query = Facture::query()->where('client_id', $clientId);

        if (Schema::hasColumn('factures', 'date_facture')) {
            $query->whereDate('date_facture', '<=', $paymentDate);
        }

        if (Schema::hasColumn('factures', 'montant_ttc')) {
            $query->whereBetween('montant_ttc', [max(0, $amount - 1), $amount + 1]);
        }

        return $query->orderBy('date_facture')->orderBy('id')->first();
    }

    private function extractRows(Worksheet $sheet, array $expectedHeaders): array
    {
        $rows = $sheet->toArray(null, false, true, true);
        $headerMap = null;
        $dataStart = null;

        foreach ($rows as $rowNumber => $row) {
            $normalized = [];

            foreach ($row as $column => $value) {
                $clean = $this->normalizeHeader($value);
                if ($clean !== '') {
                    $normalized[$column] = $clean;
                }
            }

            if (empty($normalized)) {
                continue;
            }

            $candidateMap = [];
            foreach ($expectedHeaders as $target => $aliases) {
                foreach ($normalized as $column => $value) {
                    if (in_array($value, $aliases, true)) {
                        $candidateMap[$target] = $column;
                        break;
                    }
                }
            }

            if (count($candidateMap) >= max(2, count($expectedHeaders) - 1)) {
                $headerMap = $candidateMap;
                $dataStart = $rowNumber + 1;
                break;
            }
        }

        if (!$headerMap || !$dataStart) {
            return [null, []];
        }

        $result = [];
        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber < $dataStart || $this->isRowEmpty($row)) {
                continue;
            }
            $result[] = $row;
        }

        return [$headerMap, $result];
    }

    private function normalizeHeader(mixed $value): string
    {
        $value = $this->cleanCell($value);
        $value = str_replace(["\n", "\r", '/', '-', '°', 'n°', '(', ')'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?: '';
        $value = trim(mb_strtolower($value));
        $value = str_replace(['é', 'è', 'ê', 'ë'], 'e', $value);
        $value = str_replace(['à', 'â'], 'a', $value);
        $value = str_replace(['î', 'ï'], 'i', $value);
        $value = str_replace(['ô', 'ö'], 'o', $value);
        $value = str_replace(['ù', 'û', 'ü'], 'u', $value);
        $value = str_replace(['ç', "'", '’'], ['c', '', ''], $value);
        return $value;
    }

    private function cleanCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value) || is_int($value)) {
            $string = (string) $value;
            return str_ends_with($string, '.0') ? substr($string, 0, -2) : $string;
        }

        return trim((string) $value);
    }

    private function parseNumber(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $raw = $this->cleanCell($value);
        if ($raw === '') {
            return 0.0;
        }

        if (str_starts_with($raw, '=')) {
            return 0.0;
        }

        $normalized = str_replace([' ', "\u{00A0}", ','], ['', '', '.'], $raw);
        $normalized = preg_replace('/[^0-9.\-]/', '', $normalized) ?: '0';

        return (float) $normalized;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        try {
            return (new \DateTime($this->cleanCell($value)))->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function findClientByName(string $name): ?Client
    {
        $columns = ['raison_sociale', 'nom', 'company_name', 'nom_complet', 'contact_nom'];

        $query = Client::query();
        $hasCondition = false;

        foreach ($columns as $column) {
            if (!Schema::hasColumn('clients', $column)) {
                continue;
            }

            if (!$hasCondition) {
                $query->where($column, $name);
                $hasCondition = true;
            } else {
                $query->orWhere($column, $name);
            }
        }

        return $hasCondition ? $query->first() : null;
    }

    private function findSupplierByName(string $name): ?Fournisseur
    {
        if (!Schema::hasTable('fournisseurs') || !Schema::hasColumn('fournisseurs', 'raison_sociale')) {
            return null;
        }

        $normalized = mb_strtolower(trim($name));
        if ($normalized === '') {
            return null;
        }

        return Fournisseur::query()
            ->whereRaw('LOWER(TRIM(raison_sociale)) = ?', [$normalized])
            ->first();
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($this->cleanCell($value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function filterPayloadForTable(string $table, array $payload): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $columns = array_flip(Schema::getColumnListing($table));

        return array_filter(
            $payload,
            fn ($value, $key) => isset($columns[$key]) && $value !== null && $value !== '',
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function completeRequiredColumns(string $table, array $payload, array $context = []): array
    {
        if (!Schema::hasTable($table)) {
            return $payload;
        }

        $columnsMeta = DB::select('SHOW COLUMNS FROM ' . $table);

        foreach ($columnsMeta as $meta) {
            $field = (string) ($meta->Field ?? '');
            if ($field === '' || array_key_exists($field, $payload)) {
                continue;
            }

            if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'], true)) {
                continue;
            }

            $nullable = strtoupper((string) ($meta->Null ?? 'YES')) === 'YES';
            $default = $meta->Default ?? null;
            $extra = strtolower((string) ($meta->Extra ?? ''));

            if ($nullable || $default !== null || str_contains($extra, 'auto_increment')) {
                continue;
            }

            $type = strtolower((string) ($meta->Type ?? 'varchar'));
            $payload[$field] = $this->defaultValueForRequiredColumn($field, $type, $context);
        }

        return $payload;
    }

    private function defaultValueForRequiredColumn(string $field, string $type, array $context): mixed
    {
        if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
            return 0;
        }

        if (str_contains($type, 'date')) {
            return now()->toDateString();
        }

        if (str_contains($type, 'time')) {
            return now()->toDateTimeString();
        }

        if (str_contains($type, 'tinyint') || str_contains($type, 'bool')) {
            return 0;
        }

        if (str_contains($field, 'email')) {
            $base = preg_replace('/\s+/', '.', mb_strtolower((string) ($context['name'] ?? 'import')));
            $base = preg_replace('/[^a-z0-9.]/', '', $base) ?: 'import';
            return $base . '+' . substr(md5((string) ($context['code'] ?? uniqid('', true))), 0, 6) . '@placeholder.local';
        }

        if (str_contains($field, 'code') || str_contains($field, 'reference') || str_contains($field, 'numero')) {
            return (string) ($context['code'] ?? strtoupper(substr(md5($field . microtime(true)), 0, 10)));
        }

        if (str_contains($field, 'nom') || str_contains($field, 'raison') || str_contains($field, 'libelle') || str_contains($field, 'intitule')) {
            return (string) ($context['name'] ?? 'Import');
        }

        return 'Import';
    }

    private function loadSpreadsheet(string $filePath): Spreadsheet
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);

        return $reader->load($filePath);
    }
}