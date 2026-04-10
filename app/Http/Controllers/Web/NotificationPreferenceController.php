<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $preference = Auth::user()->notificationPreference ?? new NotificationPreference();
        $notifications = Auth::user()->notifications()->latest()->paginate(20);

        return view('client.notifications.index', compact('preference', 'notifications'));
    }

    public function create()
    {
        $preference = Auth::user()->notificationPreference ?? new NotificationPreference();
        $availableEvents = NotificationPreference::getDefaultEvents();

        return view('client.notifications.create', compact('preference', 'availableEvents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sms_enabled' => 'boolean',
            'phone_number' => 'required_if:sms_enabled,true|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'sms_events' => 'array',
            'sms_events.*' => 'string',
            'email_enabled' => 'boolean',
            'email_events' => 'array',
            'email_events.*' => 'string',
        ]);

        $validated['user_id'] = Auth::id();

        // Si SMS est activé mais aucun événement sélectionné, activer les événements par défaut
        if ($validated['sms_enabled'] && empty($validated['sms_events'])) {
            $validated['sms_events'] = ['parcel_received', 'parcel_shipped', 'parcel_delivered'];
        }

        // Si email est activé mais aucun événement sélectionné, activer tous les événements
        if ($validated['email_enabled'] && empty($validated['email_events'])) {
            $validated['email_events'] = array_keys(NotificationPreference::getDefaultEvents());
        }

        NotificationPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect()->route('client.client.notifications.index')
            ->with('success', 'Préférences de notification mises à jour avec succès');
    }

    public function edit()
    {
        $preference = Auth::user()->notificationPreference ?? new NotificationPreference();
        $availableEvents = NotificationPreference::getDefaultEvents();

        return view('client.notifications.edit', compact('preference', 'availableEvents'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'sms_enabled' => 'boolean',
            'phone_number' => 'required_if:sms_enabled,true|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'sms_events' => 'array',
            'sms_events.*' => 'string',
            'email_enabled' => 'boolean',
            'email_events' => 'array',
            'email_events.*' => 'string',
        ]);

        $preference = Auth::user()->notificationPreference;

        if (!$preference) {
            $validated['user_id'] = Auth::id();
            $preference = NotificationPreference::create($validated);
        } else {
            $preference->update($validated);
        }

        return redirect()->route('client.client.notifications.index')
            ->with('success', 'Préférences de notification mises à jour avec succès');
    }

    public function testSms()
    {
        $preference = Auth::user()->notificationPreference;

        if (!$preference || !$preference->sms_enabled || !$preference->phone_number) {
            return back()->with('error', 'Veuillez activer les SMS et configurer votre numéro de téléphone');
        }

        try {
            $notification = Notification::create([
                'user_id' => Auth::id(),
                'type' => 'sms',
                'event' => 'test',
                'title' => 'Test SMS',
                'message' => 'GROUPAGE PRO: Ceci est un message de test pour vérifier que les notifications SMS fonctionnent correctement.',
                'recipient' => $preference->phone_number,
                'status' => 'pending',
            ]);

            $result = \App\Services\SmsService::send($preference->phone_number, $notification->message);

            if ($result['success']) {
                $notification->markAsSent();
                return back()->with('success', 'SMS de test envoyé avec succès');
            } else {
                $notification->markAsFailed($result['error']);
                return back()->with('error', 'Erreur lors de l\'envoi du SMS: ' . $result['error']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi du SMS: ' . $e->getMessage());
        }
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Vous pouvez ajouter un champ read_at si nécessaire
        // $notification->update(['read_at' => now()]);

        return back()->with('success', 'Notification marquée comme lue');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification supprimée avec succès');
    }
}
