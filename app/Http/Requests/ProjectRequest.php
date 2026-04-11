<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'responsable_id' => 'nullable|exists:users,id',
            'type' => 'required|in:transport,location,chantier,livraison_reguliere,autre',
            'budget_estime' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|in:actif,en_attente,termine,archive',
            'avancement' => 'nullable|integer|min:0|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'nom.required' => 'Le nom du projet est obligatoire.',
            'description.required' => 'La description du projet est obligatoire.',
            'type.required' => 'Le type de projet est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'statut.required' => 'Le statut du projet est obligatoire.',
            'avancement.max' => 'L\'avancement ne peut pas dépasser 100%.',
        ];
    }
}
