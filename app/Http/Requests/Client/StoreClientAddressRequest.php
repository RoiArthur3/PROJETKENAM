<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientAddressRequest extends FormRequest
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
            'address_type' => 'required|in:billing,shipping,pickup,return',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'special_instructions' => 'nullable|string',
            'coordinates_lat' => 'nullable|numeric|between:-90,90',
            'coordinates_lng' => 'nullable|numeric|between:-180,180',
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
            'address_type.required' => 'Le type d\'adresse est requis.',
            'address_type.in' => 'Le type d\'adresse doit être: billing, shipping, pickup, ou return.',
            'address_line_1.required' => 'L\'adresse ligne 1 est requise.',
            'city.required' => 'La ville est requise.',
            'country.required' => 'Le pays est requis.',
            'coordinates_lat.between' => 'La latitude doit être entre -90 et 90.',
            'coordinates_lng.between' => 'La longitude doit être entre -180 et 180.',
        ];
    }
}
