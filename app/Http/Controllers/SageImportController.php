<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SageImportController extends Controller
{
    // ── Mapping SAGE i7 → Colonnes de la base ──────────────────────────────────
    // SAGE peut exporter avec ces noms de colonnes (selon le module)
    private const SAGE_CLIENTS_MAP = [
        'CT_Num'           => 'code_client',       // Code tiers
        'CT_Intitule'      => 'nom_complet',        // Nom complet
        'CT_Adresse'       => 'adresse',
        'CT_Ville'         => 'ville',
        'CT_Pays'          => 'pays',
        'CT_Telephone'     => 'telephone',
        'CT_Email'         => 'email',
        'CT_Siret'         => 'registre_commerce',
        'CT_NumPayeur'     => 'numero_contribuable',
        'CT_Identifiant'   => 'raison_sociale',
        // Noms alternatifs selon version i7
        'Code'             => 'code_client',
        'Intitule'         => 'nom_complet',
        'Adresse'          => 'adresse',
        'Ville'            => 'ville',
        'Telephone'        => 'telephone',
        'Email'            => 'email',
    ];

    private const SAGE_FOURNISSEURS_MAP = [
        'CT_Num'           => 'reference',
        'CT_Intitule'      => 'raison_sociale',
        'CT_Adresse'       => 'adresse',
        'CT_CodePostal'    => 'code_postal',
        'CT_Ville'         => 'ville',
        'CT_Pays'          => 'pays',
        'CT_Telephone'     => 'telephone',
        'CT_Email'         => 'email',
        'CT_Site'          => 'site_web',
        'CT_Siret'         => 'siret',
        'CT_NumPayeur'     => 'tva_intracom',
        // Alternatifs
        'Code'             => 'reference',
        'Intitule'         => 'raison_sociale',
        'Adresse'          => 'adresse',
        'CodePostal'       => 'code_postal',
        'Ville'            => 'ville',
        'Telephone'        => 'telephone',
        'Email'            => 'email',
    ];

    private const SAGE_FACTURES_MAP = [
        'DO_Piece'         => 'numero_facture',
        'DO_Date'          => 'date_facturation',
        'CT_Num'           => 'client_id',
        'CT_Intitule'      => 'client_nom',
        'DL_Design'        => 'designation',
        'DO_TotalHT'       => 'montant_ht',
        'DO_TotalTTC'      => 'montant_ttc',
        'DO_TotalTVA'      => 'montant_tva',
        'DO_Statut'        => 'statut',
        'DO_DateEcheance'  => 'date_echeance',
        'DO_Ref'           => 'bon_commande_numero',
        // Alternatifs
        'Piece'            => 'numero_facture',
        'Date'             => 'date_facturation',
        'TotalHT'          => 'montant_ht',
        'TotalTTC'         => 'montant_ttc',
        'TotalTVA'         => 'montant_tva',
        'Client'           => 'client_nom',
    ];

    private const SAGE_ECRITURES_MAP = [
        'JO_Num'           => 'journal_code',
        'EC_Date'          => 'date',
        'EC_Reference'     => 'reference',
        'EC_Piece'         => 'piece_comptable',
        'EC_Libelle'       => 'libelle',
        'EC_CompteDebit'   => 'compte_debit',
        'EC_CompteCredit'  => 'compte_credit',
        'EC_Montant'       => 'montant',
        // Alternatifs
        'Journal'          => 'journal_code',
        'Date'             => 'date',
        'Reference'        => 'reference',
        'Libelle'          => 'libelle',
        'CompteDebit'      => 'compte_debit',
        'CompteCredit'     => 'compte_credit',
        'Montant'          => 'montant',
    ];

    // ── PAGE D'IMPORT ──────────────────────────────────────────────────────────

    public function index()
    {
        return view('comptabilite.sage-import.index');
    }

    // ── IMPORT CLIENTS ──────────────────────────────────────────────────────────

    public function importClients(Request $request)
    {
        $request->validate([
            'fichier_csv' => 'required|file|mimes:csv,txt|max:10240',
            'separateur'  => 'required|in:semicolon,comma,pipe,tab',
            'mode_import' => 'required|in:ignore,update',
        ]);

        $rows = $this->parseCsv($request->file('fichier_csv'), $request->separateur);

        if (empty($rows)) {
            return back()->with('error_clients', 'Fichier vide ou non lisible.');
        }

        $headers = array_keys($rows[0]);
        $mapping = $this->buildMapping($headers, self::SAGE_CLIENTS_MAP);
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                $data = $this->applyMapping($row, $mapping);

                if (empty($data['code_client'])) {
                    $stats['skipped']++;
                    continue;
                }

                $data['est_actif'] = $data['est_actif'] ?? 1;
                $data['pays']      = $data['pays'] ?? 'Côte d\'Ivoire';
                $data['type_client'] = $this->detectTypeClient($data);
                $data['updated_at'] = now();

                $existing = DB::table('clients')->where('code_client', $data['code_client'])->first();

                if ($existing) {
                    if ($request->mode_import === 'update') {
                        $data['created_at'] = $existing->created_at;
                        DB::table('clients')->where('code_client', $data['code_client'])->update($data);
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                } else {
                    $data['created_at'] = now();
                    DB::table('clients')->insert($data);
                    $stats['inserted']++;
                }
            } catch (\Throwable $e) {
                $stats['errors'][] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                Log::warning("SAGE import client ligne " . ($index + 2) . ": " . $e->getMessage());
            }
        }

        return back()->with('success_clients', "Import clients terminé: {$stats['inserted']} ajoutés, {$stats['updated']} mis à jour, {$stats['skipped']} ignorés.")
                     ->with('errors_clients', $stats['errors']);
    }

    // ── IMPORT FOURNISSEURS ──────────────────────────────────────────────────────

    public function importFournisseurs(Request $request)
    {
        $request->validate([
            'fichier_csv' => 'required|file|mimes:csv,txt|max:10240',
            'separateur'  => 'required|in:semicolon,comma,pipe,tab',
            'mode_import' => 'required|in:ignore,update',
        ]);

        $rows = $this->parseCsv($request->file('fichier_csv'), $request->separateur);

        if (empty($rows)) {
            return back()->with('error_fournisseurs', 'Fichier vide ou non lisible.');
        }

        $headers = array_keys($rows[0]);
        $mapping = $this->buildMapping($headers, self::SAGE_FOURNISSEURS_MAP);
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                $data = $this->applyMapping($row, $mapping);

                if (empty($data['reference']) && empty($data['raison_sociale'])) {
                    $stats['skipped']++;
                    continue;
                }

                // Générer une référence si absente
                if (empty($data['reference'])) {
                    $data['reference'] = 'FOUR-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['raison_sociale']), 0, 6)) . '-' . rand(100, 999);
                }

                $data['est_actif']  = $data['est_actif'] ?? 1;
                $data['pays']       = $data['pays'] ?? 'Côte d\'Ivoire';
                $data['updated_at'] = now();

                $existing = DB::table('fournisseurs')->where('reference', $data['reference'])->first();

                if ($existing) {
                    if ($request->mode_import === 'update') {
                        $data['created_at'] = $existing->created_at;
                        DB::table('fournisseurs')->where('reference', $data['reference'])->update($data);
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                } else {
                    $data['created_at'] = now();
                    DB::table('fournisseurs')->insert($data);
                    $stats['inserted']++;
                }
            } catch (\Throwable $e) {
                $stats['errors'][] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                Log::warning("SAGE import fournisseur ligne " . ($index + 2) . ": " . $e->getMessage());
            }
        }

        return back()->with('success_fournisseurs', "Import fournisseurs terminé: {$stats['inserted']} ajoutés, {$stats['updated']} mis à jour, {$stats['skipped']} ignorés.")
                     ->with('errors_fournisseurs', $stats['errors']);
    }

    // ── IMPORT FACTURES ──────────────────────────────────────────────────────────

    public function importFactures(Request $request)
    {
        $request->validate([
            'fichier_csv' => 'required|file|mimes:csv,txt|max:10240',
            'separateur'  => 'required|in:semicolon,comma,pipe,tab',
            'mode_import' => 'required|in:ignore,update',
        ]);

        $rows = $this->parseCsv($request->file('fichier_csv'), $request->separateur);

        if (empty($rows)) {
            return back()->with('error_factures', 'Fichier vide ou non lisible.');
        }

        $headers = array_keys($rows[0]);
        $mapping = $this->buildMapping($headers, self::SAGE_FACTURES_MAP);
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                $data = $this->applyMapping($row, $mapping);

                if (empty($data['numero_facture'])) {
                    $stats['skipped']++;
                    continue;
                }

                // Normaliser la date
                $data['date_facturation'] = $this->parseDate($data['date_facturation'] ?? null) ?? now()->toDateString();

                // Calculer les montants si manquants
                $ht  = (float) ($data['montant_ht']  ?? 0);
                $ttc = (float) ($data['montant_ttc'] ?? 0);
                $tva = (float) ($data['montant_tva'] ?? 0);

                if ($ttc > 0 && $ht === 0 && $tva === 0) {
                    $tva = round($ttc - ($ttc / 1.18), 2);
                    $ht  = round($ttc - $tva, 2);
                }
                if ($ht > 0 && $ttc === 0) {
                    $tva = round($ht * 0.18, 2);
                    $ttc = round($ht + $tva, 2);
                }

                $data['montant_ht']      = $ht;
                $data['montant_tva']     = $tva;
                $data['montant_ttc']     = $ttc;
                $data['tva_taux']        = $data['tva_taux'] ?? 18.00;
                $data['acompte']         = $data['acompte'] ?? 0;
                $data['montant_restant'] = $ttc - ($data['acompte'] ?? 0);
                $data['statut']          = $this->normalizeStatutFacture($data['statut'] ?? 'en_attente');
                $data['created_by']      = 'SAGE_IMPORT';
                $data['updated_at']      = now();

                if (empty($data['client_nom'])) {
                    $data['client_nom'] = $data['client_id'] ?? 'Client SAGE';
                }
                $data['designation'] = $data['designation'] ?? 'Importé depuis SAGE';

                $existing = DB::table('factures')->where('numero_facture', $data['numero_facture'])->first();

                if ($existing) {
                    if ($request->mode_import === 'update') {
                        $data['created_at'] = $existing->created_at;
                        DB::table('factures')->where('numero_facture', $data['numero_facture'])->update($data);
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                } else {
                    $data['created_at'] = now();
                    DB::table('factures')->insert($data);
                    $stats['inserted']++;
                }
            } catch (\Throwable $e) {
                $stats['errors'][] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                Log::warning("SAGE import facture ligne " . ($index + 2) . ": " . $e->getMessage());
            }
        }

        return back()->with('success_factures', "Import factures terminé: {$stats['inserted']} ajoutées, {$stats['updated']} mises à jour, {$stats['skipped']} ignorées.")
                     ->with('errors_factures', $stats['errors']);
    }

    // ── IMPORT ÉCRITURES COMPTABLES ──────────────────────────────────────────────

    public function importEcritures(Request $request)
    {
        $request->validate([
            'fichier_csv' => 'required|file|mimes:csv,txt|max:10240',
            'separateur'  => 'required|in:semicolon,comma,pipe,tab',
            'mode_import' => 'required|in:ignore,update',
        ]);

        $rows = $this->parseCsv($request->file('fichier_csv'), $request->separateur);

        if (empty($rows)) {
            return back()->with('error_ecritures', 'Fichier vide ou non lisible.');
        }

        if (!Schema::hasTable('ecritures_comptables')) {
            return back()->with('error_ecritures', 'Table ecritures_comptables introuvable. Exécutez php artisan migrate.');
        }

        $headers = array_keys($rows[0]);
        $mapping = $this->buildMapping($headers, self::SAGE_ECRITURES_MAP);

        // Précharger les journaux
        $journauxMap = DB::table('journal_comptables')->pluck('id', 'code')->toArray();

        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                $data = $this->applyMapping($row, $mapping);

                if (empty($data['reference']) || empty($data['montant'])) {
                    $stats['skipped']++;
                    continue;
                }

                $data['date']      = $this->parseDate($data['date'] ?? null) ?? now()->toDateString();
                $data['montant']   = (float) str_replace([' ', ','], ['', '.'], $data['montant']);
                $data['libelle']   = $data['libelle'] ?? 'Écriture SAGE';
                $data['source_type'] = 'sage_import';

                // Résoudre l'ID du journal
                if (!empty($data['journal_code'])) {
                    $data['journal_id'] = $journauxMap[strtoupper($data['journal_code'])] ?? null;
                    unset($data['journal_code']);
                }

                // Comptes SAGE : format 411000, 707000…
                $data['compte_debit']  = $data['compte_debit']  ?? '471000';
                $data['compte_credit'] = $data['compte_credit'] ?? '471000';

                $data['updated_at'] = now();

                // Vérifier doublon (même référence + date + montant)
                $exists = DB::table('ecritures_comptables')
                    ->where('reference', $data['reference'])
                    ->where('date', $data['date'])
                    ->where('montant', $data['montant'])
                    ->exists();

                if (!$exists) {
                    $data['created_at'] = now();
                    DB::table('ecritures_comptables')->insert($data);
                    $stats['inserted']++;
                } else {
                    $stats['skipped']++;
                }
            } catch (\Throwable $e) {
                $stats['errors'][] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                Log::warning("SAGE import ecriture ligne " . ($index + 2) . ": " . $e->getMessage());
            }
        }

        return back()->with('success_ecritures', "Import écritures terminé: {$stats['inserted']} ajoutées, {$stats['skipped']} ignorées (doublons).")
                     ->with('errors_ecritures', $stats['errors']);
    }

    // ── TELECHARGEMENT MODELE CSV ────────────────────────────────────────────────

    public function downloadTemplate(string $type)
    {
        $templates = [
            'clients' => [
                'headers' => ['CT_Num', 'CT_Intitule', 'CT_Adresse', 'CT_Ville', 'CT_Pays', 'CT_Telephone', 'CT_Email', 'CT_Identifiant', 'CT_NumPayeur'],
                'example' => ['CL001', 'Société Example SARL', '12 Rue du Commerce', 'Abidjan', 'Côte d\'Ivoire', '+225 07 00 00 00', 'contact@example.ci', 'Société Example SARL', 'CI123456'],
            ],
            'fournisseurs' => [
                'headers' => ['CT_Num', 'CT_Intitule', 'CT_Adresse', 'CT_CodePostal', 'CT_Ville', 'CT_Pays', 'CT_Telephone', 'CT_Email', 'CT_Siret'],
                'example' => ['FOUR001', 'Fournisseur A Sarl', 'Zone Industrielle', '01BP001', 'Abidjan', 'Côte d\'Ivoire', '+225 06 00 00 00', 'four@a.ci', ''],
            ],
            'factures' => [
                'headers' => ['DO_Piece', 'DO_Date', 'CT_Num', 'CT_Intitule', 'DL_Design', 'DO_TotalHT', 'DO_TotalTVA', 'DO_TotalTTC', 'DO_Statut', 'DO_DateEcheance'],
                'example' => ['FAC2024001', '01/04/2026', 'CL001', 'Société Example', 'Prestation de service', '100000', '18000', '118000', 'Non soldé', '30/04/2026'],
            ],
            'ecritures' => [
                'headers' => ['JO_Num', 'EC_Date', 'EC_Reference', 'EC_Piece', 'EC_Libelle', 'EC_CompteDebit', 'EC_CompteCredit', 'EC_Montant'],
                'example' => ['VT', '01/04/2026', 'FAC2024001', 'FAC2024001', 'Vente client CL001', '411000', '707000', '118000'],
            ],
        ];

        if (!isset($templates[$type])) {
            abort(404, 'Modèle non trouvé');
        }

        $tmpl = $templates[$type];
        $csv  = implode(';', $tmpl['headers']) . "\n";
        $csv .= implode(';', $tmpl['example']) . "\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"modele_sage_{$type}.csv\"",
        ]);
    }

    // ── HELPERS ─────────────────────────────────────────────────────────────────

    private function parseCsv($file, string $separateur): array
    {
        $sep = match ($separateur) {
            'semicolon' => ';',
            'comma'     => ',',
            'pipe'      => '|',
            'tab'       => "\t",
            default     => ';',
        };

        $content = file_get_contents($file->getRealPath());

        // Convertir Windows-1252 → UTF-8 si nécessaire
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Supprimer le BOM UTF-8 si present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (count($lines) < 2) {
            return [];
        }

        $headers = str_getcsv(array_shift($lines), $sep, '"');
        $headers = array_map('trim', $headers);
        $rows    = [];

        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $values = str_getcsv($line, $sep, '"');
            $values = array_map('trim', $values);
            // Compléter si moins de colonnes que l'entête
            while (count($values) < count($headers)) {
                $values[] = '';
            }
            $rows[] = array_combine($headers, array_slice($values, 0, count($headers)));
        }

        return $rows;
    }

    private function buildMapping(array $csvHeaders, array $mapping): array
    {
        $result = [];
        foreach ($csvHeaders as $csvCol) {
            $normalized = $mapping[$csvCol] ?? $mapping[trim($csvCol)] ?? null;
            if ($normalized) {
                $result[$csvCol] = $normalized;
            }
        }
        return $result;
    }

    private function applyMapping(array $row, array $mapping): array
    {
        $result = [];
        foreach ($mapping as $csvCol => $dbCol) {
            $val = $row[$csvCol] ?? '';
            if ($val !== '') {
                $result[$dbCol] = $val;
            }
        }
        return $result;
    }

    private function parseDate(?string $value): ?string
    {
        if (empty($value)) return null;
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'Ymd'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)?->toDateString();
            } catch (\Exception) {}
        }
        return null;
    }

    private function detectTypeClient(array $data): string
    {
        $nom = strtolower($data['nom_complet'] ?? $data['raison_sociale'] ?? '');
        foreach (['sarl', 'sa ', 's.a', 'sas', 'eurl', 'snc', 'sci', 'ltd', 'inc', 'cie', 'établissement', 'ministere', 'ministère'] as $keyword) {
            if (str_contains($nom, $keyword)) return 'entreprise';
        }
        foreach (['mairie', 'commune', 'prefecture', 'état', 'dgbf', 'dgd', 'gouvern'] as $keyword) {
            if (str_contains($nom, $keyword)) return 'administration';
        }
        return 'entreprise';
    }

    private function normalizeStatutFacture(string $statut): string
    {
        return match (strtolower(trim($statut))) {
            'soldé', 'solde', 'payé', 'paye', 'paid', '1' => 'payee',
            'non soldé', 'non solde', 'impayé', 'impaye', 'unpaid', '0' => 'en_attente',
            'partielle', 'partiel', 'acompte' => 'partiellement_payee',
            'annulé', 'annule', 'cancelled'  => 'annulee',
            default => 'en_attente',
        };
    }
}
