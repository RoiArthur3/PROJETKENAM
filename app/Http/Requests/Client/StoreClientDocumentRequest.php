<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientDocumentRequest extends FormRequest
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
            'document' => 'required|file|max:10240', // Max 10MB
            'document_type' => 'required|in:id_card,passport,commercial_register,tax_certificate,contract,other',
            'document_name' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'issue_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:255',
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
            'document.required' => 'Le document est requis.',
            'document.file' => 'Le fichier doit être un document valide.',
            'document.max' => 'Le document ne doit pas dépasser 10MB.',
            'document_type.required' => 'Le type de document est requis.',
            'document_type.in' => 'Le type de document doit être: id_card, passport, commercial_register, tax_certificate, contract, ou other.',
            'document_name.required' => 'Le nom du document est requis.',
            'issue_date.before_or_equal' => 'La date d\'émission doit être antérieure ou égale à aujourd\'hui.',
            'expiry_date.after' => 'La date d\'expiration doit être postérieure à la date d\'émission.',
        ];
    }
}
