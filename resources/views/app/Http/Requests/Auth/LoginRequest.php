<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('telephone')).'|'.$this->ip());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'telephone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $phoneInput = $this->input('telephone');

        // 1. Nettoyer le numéro
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneInput);

        // Supprimer l'indicatif 225 s'il est présent au début pour avoir la base locale
        // Ex: 2250707070707 -> 0707070707
        // Attention: ne pas supprimer si c'est juste 225... mais un numéro Ivoirien fait 10 chiffres (nouveau format)
        if (strlen($cleanPhone) > 10 && str_starts_with($cleanPhone, '225')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // 2. Créer les variations possibles
        // On cherche ce que l'utilisateur a pu entrer vs ce qui est stocké
        // La base peut contenir: "0102030405", "2250102030405", "+2250102030405", "01 02 03 04 05"
        // On n'a pas accès direct aux variations formatées avec espaces via SQL 'IN' simple si le formatage est arbitraire.
        // Mais si on suppose une standardisation...

        // Pour être sûr, on va chercher via LIKE si le regex exact échoue, ou tenter les formats standards.
        $phoneVariations = [
            $phoneInput, // Tel quel
            $cleanPhone, // Juste chiffres
            '225' . $cleanPhone,
            '+225' . $cleanPhone,
            '+225 ' . $cleanPhone,
            // Formats avec espaces (01 02 03 04 05) - on pourrait faire un whereRaw replace(phone, ' ', '') = ? pour être robuste
        ];

        // 3. Chercher l'utilisateur (méthode robuste via Eloquent)
        // On cherche un user dont le téléphone correspond à une des variations
        $user = \App\Models\User::whereIn('telephone', $phoneVariations)->first();

        // Si pas trouvé par correspondance exacte, on tente une recherche plus "soft" en SQL
        // en retirant tout ce qui n'est pas chiffre dans la colonne telephone (si DB driver le permet facilement)
        // ou en tentant des LIKE %cleanPhone% (risque de faux positifs si numéros courts)
        if (!$user) {
             $user = \App\Models\User::where('telephone', 'LIKE', '%' . $cleanPhone)->first();
        }

        if (!$user || ! \Illuminate\Support\Facades\Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'telephone' => trans('auth.failed'),
            ]);
        }

        // Vérification actif
        if (!$user->is_active) {
             throw ValidationException::withMessages([
                'telephone' => 'Ce compte a été désactivé.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'phone' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}
