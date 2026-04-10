<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientContactRequest extends FormRequest
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
        return [
            'contact_type' => 'required|in:primary,billing,technical,logistics',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'preferred_contact_method' => 'nullable|in:email,phone,sms',
            'language_preference' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
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
            'contact_type.required' => 'Le type de contact est requis.',
            'contact_type.in' => 'Le type de contact doit être: primary, billing, technical, ou logistics.',
            'first_name.required' => 'Le prénom est requis.',
            'last_name.required' => 'Le nom est requis.',
            'preferred_contact_method.in' => 'La méthode de contact préférée doit être: email, phone, ou sms.',
        ];
    }
}
