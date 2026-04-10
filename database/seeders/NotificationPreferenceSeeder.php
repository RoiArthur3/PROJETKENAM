<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationPreference;
use App\Models\User;

class NotificationPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Créer des préférences par défaut pour chaque utilisateur
            NotificationPreference::create([
                'user_id' => $user->id,
                'sms_enabled' => false, // Désactivé par défaut
                'phone_number' => $user->phone, // Utiliser le téléphone existant
                'sms_events' => [
                    'parcel_received',
                    'parcel_shipped',
                    'parcel_delivered'
                ],
                'whatsapp_enabled' => false, // Désactivé par défaut
                'whatsapp_number' => $user->phone, // Utiliser le téléphone existant
                'whatsapp_events' => [
                    'parcel_received',
                    'parcel_shipped',
                    'parcel_delivered'
                ],
                'email_enabled' => true, // Activé par défaut
                'email_events' => [
                    'parcel_declared',
                    'parcel_received',
                    'parcel_inspected',
                    'parcel_grouped',
                    'parcel_shipped',
                    'parcel_in_transit',
                    'parcel_arrived',
                    'parcel_customs',
                    'parcel_delivered',
                    'payment_required',
                    'payment_confirmed'
                ],
            ]);
        }
    }
}
