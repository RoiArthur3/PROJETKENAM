<?php

namespace App\Services;

use App\Models\Operation;

class OperationNumberService
{
    /**
     * Générer un numéro d'opération formaté.
     * Format canonique: OP-AAAA-NNNN
     */
    public static function generateOperationNumber(?int $year = null): string
    {
        $currentYear = $year ?? (int) now()->format('Y');
        $prefix = 'OP-' . $currentYear . '-';

        $lastNumber = Operation::query()
            ->where('numero_operation', 'like', $prefix . '%')
            ->orderByDesc('numero_operation')
            ->value('numero_operation');

        $sequence = 1;

        if (is_string($lastNumber) && preg_match('/^OP-\d{4}-(\d{4})$/', $lastNumber, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir le prochain numéro d'ordre pour le mois en cours
     */
    private static function getNextOrderNumber($year, $month)
    {
        $count = Operation::whereYear('created_at', $year)
            ->count();

        return str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mettre à jour tous les numéros d'opération existants
     */
    public static function updateAllOperationNumbers()
    {
        $operations = Operation::orderBy('created_at')->get();

        foreach ($operations as $operation) {
            $year = (int) optional($operation->created_at)->format('Y');
            $operation->numero_operation = self::generateOperationNumber($year);
            $operation->save();
        }

        return $operations->count();
    }

    /**
     * Parser un numéro d'opération
     */
    public static function parseOperationNumber($numero)
    {
        if (!preg_match('/OP-(\d{4})-(\d{4})/', $numero, $matches)) {
            return null;
        }

        return [
            'year' => $matches[1],
            'month' => null,
            'order' => $matches[2],
            'date' => "{$matches[1]}-01-01"
        ];
    }

    /**
     * Obtenir les statistiques des numéros d'opération
     */
    public static function getNumberStats()
    {
        $stats = [];

        // Dernier 12 mois
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $year = $date->format('Y');
            $month = $date->format('m');

            $count = Operation::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $stats[] = [
                'period' => $date->format('Y-m'),
                'count' => $count,
                'last_number' => $count > 0 ? 'OP-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT) : null
            ];
        }

        return array_reverse($stats);
    }
}
