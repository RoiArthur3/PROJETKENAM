<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
    public function rules(): array
    {
        $clientId = $this->route('client')->id;

        return [
            'user_id' => 'nullable|exists:users,id',
            'company_name' => 'sometimes|required_if:client_type,company|string|max:255',
            'contact_person' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('clients')->ignore($clientId),
            ],
            'secondary_email' => 'nullable|email|max:255',
            'address' => 'nullable|array',
            'address.address_line_1' => 'required_with:address|string|max:255',
            'address.address_line_2' => 'nullable|string|max:255',
            'address.city' => 'required_with:address|string|max:100',
            'address.state_province' => 'nullable|string|max:100',
            'address.country' => 'required_with:address|string|max:100',
            'address.postal_code' => 'nullable|string|max:20',
            'address.contact_person' => 'nullable|string|max:255',
            'address.phone' => 'nullable|string|max:20',
            'address.email' => 'nullable|email|max:255',
            'address.special_instructions' => 'nullable|string',
            'client_type' => 'sometimes|in:individual,company,regular,vip',
            'credit_limit' => 'nullable|numeric|min:0|max:999999.99',
            'payment_terms' => 'sometimes|in:cod,7days,14days,30days',
            'preferred_transport_mode' => 'nullable|in:air_normal,air_express,sea',
            'business_sector' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'is_vip' => 'boolean',
            'referral_source' => 'nullable|string|max:255',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'language_preference' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'currency_preference' => 'nullable|string|max:3',
            'auto_billing_enabled' => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'sms_notifications_enabled' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required_if' => 'Le nom de l\'entreprise est requis pour les clients de type entreprise.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'address.address_line_1.required_with' => 'L\'adresse est requise.',
            'address.city.required_with' => 'La ville est requise.',
            'address.country.required_with' => 'Le pays est requis.',
        ];
    }
}
