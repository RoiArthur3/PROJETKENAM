<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Paramètres généraux
            'app.name' => 'KENAM SERVICES',
            'app.description' => 'Plateforme de gestion opérationnelle',
            'app.version' => '1.0.0',
            
            // Paramètres des opérations
            'operations.default_validation_chain' => json_encode([
                'service_head',
                'budget_manager',
                'operations_director'
            ]),
            'operations.auto_assign' => false,
            'operations.notification_email' => 'operations@kenam.com',
            
            // Paramètres du parc auto
            'parc.default_maintenance_interval' => 90, // jours
            'parc.fuel_alert_threshold' => 20, // litres
            'parc.insurance_reminder_days' => 30,
            
            // Paramètres du stock
            'stock.alert_threshold' => 10, // quantité minimale
            'stock.default_warehouse' => 'principal',
            'stock.enable_notifications' => true,
            
            // Paramètres RH
            'rh.working_days_per_month' => 22,
            'rh.overtime_rate' => 1.25,
            'rh.leave_request_days' => 3,
            
            // Paramètres financiers
            'finance.default_currency' => 'XOF',
            'finance.expense_validation_limit' => 50000,
            'finance.payment_terms' => 30,
            
            // Paramètres de notification
            'notifications.email_driver' => 'log',
            'notifications.sms_enabled' => false,
            'notifications.slack_webhook' => null,
            
            // Paramètres de sécurité
            'security.session_timeout' => 120, // minutes
            'security.max_login_attempts' => 5,
            'security.password_min_length' => 8,
            
            // Paramètres d'interface
            'ui.theme' => 'light',
            'ui.language' => 'fr',
            'ui.timezone' => 'Africa/Abidjan',
            'ui.items_per_page' => 25,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
