<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Groupeur;
use App\Models\ShippingPrice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GroupeurController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function profile(Request $request)
    {
        $groupeur = Groupeur::with('user')->firstOrNew(['user_id' => Auth::id()]);
        $shippingPrices = ShippingPrice::all()->keyBy('transport_mode');

        return view('admin.groupeur.profile', [
            'groupeur' => $groupeur,
            'shippingPrices' => $shippingPrices,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $groupeur = Groupeur::firstOrCreate(['user_id' => Auth::id()]);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'notes' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        $groupeur->update([
            'business_name' => $validated['business_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('admin.groupeur.profile')
            ->with('success', 'Profil du groupeur mis à jour avec succès.');
    }

    public function businessCard(Request $request)
    {
        $groupeur = Groupeur::with('user')->firstOrNew(['user_id' => Auth::id()]);

        return view('admin.groupeur.business-card', [
            'groupeur' => $groupeur,
        ]);
    }

    public function uploadBusinessCard(Request $request)
    {
        $groupeur = Groupeur::firstOrCreate(['user_id' => Auth::id()]);

        $validated = $request->validate([
            'business_card' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Supprimer l'ancienne carte si elle existe
        if ($groupeur->business_card_path) {
            Storage::disk('public')->delete($groupeur->business_card_path);
        }

        // Stocker la nouvelle carte
        $path = $request->file('business_card')->store('groupeur/business-cards', 'public');

        $groupeur->update([
            'business_card_path' => $path,
            'business_card_uploaded_at' => now(),
        ]);

        return redirect()->route('admin.groupeur.business-card')
            ->with('success', 'Carte de visite téléchargée avec succès.');
    }

    public function shippingAddress(Request $request)
    {
        $groupeur = Groupeur::with('user')->firstOrNew(['user_id' => Auth::id()]);

        return view('admin.groupeur.shipping-address', [
            'groupeur' => $groupeur,
        ]);
    }

    public function updateShippingAddress(Request $request)
    {
        $groupeur = Groupeur::firstOrCreate(['user_id' => Auth::id()]);

        $validated = $request->validate([
            'shipping_address' => 'required|string|max:2000',
            'shipping_city' => 'required|string|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
        ]);

        $groupeur->update([
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_country' => $validated['shipping_country'],
            'shipping_postal_code' => $validated['shipping_postal_code'],
        ]);

        return redirect()->route('admin.groupeur.shipping-address')
            ->with('success', 'Adresse de livraison mise à jour avec succès.');
    }

    public function smsConfig(Request $request)
    {
        // Récupérer la configuration SMS actuelle depuis le .env
        $smsConfig = [
            'provider' => config('services.sms.default', 'smseco'),
            'smseco' => [
                'email' => config('services.sms.smseco.email'),
                'sender' => config('services.sms.smseco.sender'),
                'reseller_code' => config('services.sms.smseco.reseller_code'),
                'base_url' => config('services.sms.smseco.base_url'),
            ],
        ];

        return view('admin.groupeur.sms-config', [
            'smsConfig' => $smsConfig,
        ]);
    }

    public function updateSmsConfig(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'required|in:twilio,ovh,smsc,infobip,smseco',
            'smseco_sender' => 'required_if:sms_provider,smseco|string|max:50',
        ]);

        // Valeurs fixes pour SMSECO
        $smsecoEmail = 'info@chromvisio.net';
        $smsecoResellerCode = '2656631451';
        $smsecoBaseUrl = 'https://www.netsmspro.net';

        // Mettre à jour le fichier .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $updates = [
            'SMS_PROVIDER' => $validated['sms_provider'],
            'SMSECO_EMAIL' => $smsecoEmail,
            'SMSECO_SENDER' => $validated['smseco_sender'] ?? '',
            'SMSECO_RESELLER_CODE' => $smsecoResellerCode,
            'SMSECO_BASE_URL' => $smsecoBaseUrl,
        ];

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);

        return redirect()->route('admin.groupeur.sms-config')
            ->with('success', 'Configuration SMS mise à jour avec succès.');
    }

    public function usersIndex(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.groupeur.users', [
            'users' => $users,
        ]);
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['client', 'admin', 'agent', 'warehouse'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.groupeur.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function usersUpdateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['client', 'admin', 'agent', 'warehouse'])],
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.groupeur.users.index')
            ->with('success', 'Rôle mis à jour.');
    }

    public function usersToggle(Request $request, User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('admin.groupeur.users.index')
            ->with('success', $user->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.');
    }

    public function updateShippingPrices(Request $request)
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*.price_per_kg' => 'required|numeric|min:0',
            'prices.*.minimum_price' => 'required|numeric|min:0',
            'prices.*.insurance_rate' => 'required|numeric|min:0|max:100',
            'prices.*.customs_rate' => 'required|numeric|min:0|max:100',
            'prices.*.handling_fee' => 'required|numeric|min:0',
            'prices.*.packaging_fee_per_carton' => 'required|numeric|min:0',
            'prices.*.is_active' => 'boolean',
            'prices.*.notes' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['prices'] as $transportMode => $priceData) {
            ShippingPrice::updateOrCreate(
                ['transport_mode' => $transportMode],
                $priceData
            );
        }

        return redirect()->route('admin.groupeur.profile')
            ->with('success', 'Prix d\'envoi mis à jour avec succès');
    }
}
