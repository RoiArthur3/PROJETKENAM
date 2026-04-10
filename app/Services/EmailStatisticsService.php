<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmailStatisticsService
{
    /**
     * Récupère les statistiques Email pour le dashboard
     */
    public function getEmailStatistics(): array
    {
        try {
            $stats = [
                'today' => $this->getTodayStats(),
                'week' => $this->getWeekStats(),
                'month' => $this->getMonthStats(),
                'total' => $this->getTotalStats(),
                'by_type' => $this->getStatsByType(),
                'by_status' => $this->getStatsByStatus(),
                'recent' => $this->getRecentEmails(),
                'trend' => $this->getEmailTrend()
            ];

            Log::info('Email Statistics retrieved successfully', [
                'total_emails' => $stats['total']['count'],
                'success_rate' => $stats['total']['success_rate']
            ]);

            return $stats;

        } catch (\Exception $e) {
            Log::error('Error retrieving Email statistics: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    /**
     * Statistiques Email du jour
     */
    private function getTodayStats(): array
    {
        $today = Carbon::today();

        $total = DB::table('email_logs')
            ->whereDate('created_at', $today)
            ->count();

        $success = DB::table('email_logs')
            ->whereDate('created_at', $today)
            ->where('status', 'success')
            ->count();

        $failed = DB::table('email_logs')
            ->whereDate('created_at', $today)
            ->where('status', 'failed')
            ->count();

        return [
            'count' => $total,
            'success' => $success,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
            'period' => "Aujourd'hui"
        ];
    }

    /**
     * Statistiques Email de la semaine
     */
    private function getWeekStats(): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $total = DB::table('email_logs')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $success = DB::table('email_logs')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('status', 'success')
            ->count();

        $failed = DB::table('email_logs')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('status', 'failed')
            ->count();

        return [
            'count' => $total,
            'success' => $success,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
            'period' => "Cette semaine"
        ];
    }

    /**
     * Statistiques Email du mois
     */
    private function getMonthStats(): array
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $total = DB::table('email_logs')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $success = DB::table('email_logs')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'success')
            ->count();

        $failed = DB::table('email_logs')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'failed')
            ->count();

        return [
            'count' => $total,
            'success' => $success,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
            'period' => "Ce mois"
        ];
    }

    /**
     * Statistiques Email totales
     */
    private function getTotalStats(): array
    {
        $total = DB::table('email_logs')->count();

        $success = DB::table('email_logs')
            ->where('status', 'success')
            ->count();

        $failed = DB::table('email_logs')
            ->where('status', 'failed')
            ->count();

        return [
            'count' => $total,
            'success' => $success,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
            'period' => "Total"
        ];
    }

    /**
     * Statistiques Email par type
     */
    private function getStatsByType(): array
    {
        $types = DB::table('email_logs')
            ->select('template', DB::raw('count(*) as count'), DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success'))
            ->groupBy('template')
            ->orderBy('count', 'desc')
            ->get();

        return $types->map(function($type) {
            return [
                'template' => $type->template,
                'count' => $type->count,
                'success' => $type->success,
                'success_rate' => $type->count > 0 ? round(($type->success / $type->count) * 100, 2) : 0
            ];
        })->toArray();
    }

    /**
     * Statistiques Email par statut
     */
    private function getStatsByStatus(): array
    {
        $statuses = DB::table('email_logs')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        return $statuses->toArray();
    }

    /**
     * Emails récents envoyés
     */
    private function getRecentEmails(): array
    {
        return DB::table('email_logs')
            ->join('users', 'email_logs.user_id', '=', 'users.id')
            ->select(
                'email_logs.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderBy('email_logs.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($email) {
                return [
                    'id' => $email->id,
                    'recipient' => $email->recipient,
                    'template' => $email->template,
                    'status' => $email->status,
                    'provider' => $email->provider,
                    'user_name' => $email->user_name,
                    'user_email' => $email->user_email,
                    'created_at' => $email->created_at,
                    'operation_reference' => $email->operation_reference ?? null,
                    'error_message' => $email->error_message ?? null
                ];
            })
            ->toArray();
    }

    /**
     * Tendance Email (derniers 7 jours)
     */
    private function getEmailTrend(): array
    {
        $trend = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $total = DB::table('email_logs')
                ->whereDate('created_at', $date)
                ->count();

            $success = DB::table('email_logs')
                ->whereDate('created_at', $date)
                ->where('status', 'success')
                ->count();

            $trend[] = [
                'date' => $date->format('d/m'),
                'total' => $total,
                'success' => $success,
                'failed' => $total - $success
            ];
        }

        return $trend;
    }

    /**
     * Statistiques par défaut en cas d'erreur
     */
    public function getDefaultStats(): array
    {
        return [
            'today' => ['count' => 0, 'success' => 0, 'failed' => 0, 'success_rate' => 0, 'period' => "Aujourd'hui"],
            'week' => ['count' => 0, 'success' => 0, 'failed' => 0, 'success_rate' => 0, 'period' => "Cette semaine"],
            'month' => ['count' => 0, 'success' => 0, 'failed' => 0, 'success_rate' => 0, 'period' => "Ce mois"],
            'total' => ['count' => 0, 'success' => 0, 'failed' => 0, 'success_rate' => 0, 'period' => "Total"],
            'by_type' => [],
            'by_status' => [],
            'recent' => [],
            'trend' => []
        ];
    }

    /**
     * Enregistre un Email dans les logs
     */
    public function logEmail(array $data): void
    {
        try {
            DB::table('email_logs')->insert([
                'recipient' => $data['recipient'],
                'template' => $data['template'] ?? 'unknown',
                'status' => $data['status'] ?? 'pending',
                'provider' => $data['provider'] ?? null,
                'user_id' => (Auth::check()) ? Auth::id() : null,
                'operation_reference' => $data['operation_reference'] ?? null,
                'subject' => $data['subject'] ?? null,
                'error_message' => $data['error_message'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Email logged', [
                'recipient' => $data['recipient'],
                'template' => $data['template'],
                'status' => $data['status'],
                'provider' => $data['provider']
            ]);

        } catch (\Exception $e) {
            Log::error('Error logging Email: ' . $e->getMessage());
        }
    }

    /**
     * Crée la table email_logs si elle n'existe pas
     */
    public function ensureEmailTable(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('email_logs')) {
            DB::statement("
                CREATE TABLE email_logs (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    recipient VARCHAR(255) NOT NULL,
                    template VARCHAR(100) NOT NULL,
                    status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
                    provider VARCHAR(50) NULL,
                    user_id BIGINT NULL,
                    operation_reference VARCHAR(50) NULL,
                    subject VARCHAR(255) NULL,
                    error_message TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_status (status),
                    INDEX idx_template (template),
                    INDEX idx_created_at (created_at),
                    INDEX idx_user_id (user_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            Log::info('Email logs table created successfully');
        }
    }
}
