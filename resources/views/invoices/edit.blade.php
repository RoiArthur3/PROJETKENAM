@extends('layouts.app')

@section('title', 'Modifier la facture')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Modifier la facture #{{ $invoice->id }}</h1>
            <p class="text-sm text-gray-500">Colis: {{ $invoice->parcel->tracking_number }}</p>
        </div>
        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-outline-primary">
            Annuler
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire d'édition -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Modifier les coûts</h2>
                    <p class="text-sm text-gray-500">Ajustez les coûts des cartons utilisés</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Coût des cartons -->
                        <div class="space-y-2 mb-6">
                            <label class="block text-sm font-medium text-gray-700" for="cartons_cost">
                                Coût des cartons (XOF)
                            </label>
                            <input
                                type="number"
                                id="cartons_cost"
                                name="cartons_cost"
                                value="{{ $invoice->cartons_cost }}"
                                min="0"
                                step="0.01"
                                class="input-text"
                                required
                            >
                            <p class="text-xs text-gray-500">
                                Entrez le coût total des cartons utilisés pour cette expédition
                            </p>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2 mb-6">
                            <label class="block text-sm font-medium text-gray-700" for="notes">
                                Notes (optionnel)
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                class="input-text"
                                placeholder="Ajoutez des notes concernant cette modification..."
                            >{{ $invoice->notes ?? '' }}</textarea>
                        </div>

                        <!-- Résumé des coûts -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Résumé des coûts</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Coût de transport:</span>
                                    <span class="font-medium">{{ number_format($invoice->shipping_cost, 2) }} XOF</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Frais de douane:</span>
                                    <span class="font-medium">{{ number_format($invoice->customs_fee, 2) }} XOF</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Frais de manutention:</span>
                                    <span class="font-medium">{{ number_format($invoice->handling_fee, 2) }} XOF</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Frais d'assurance:</span>
                                    <span class="font-medium">{{ number_format($invoice->insurance_fee, 2) }} XOF</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Autres frais:</span>
                                    <span class="font-medium">{{ number_format($invoice->other_fees, 2) }} XOF</span>
                                </div>
                                <div class="flex justify-between font-medium text-blue-600">
                                    <span>Coût des cartons:</span>
                                    <span id="cartons-cost-display">{{ number_format($invoice->cartons_cost, 2) }} XOF</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between font-semibold text-lg">
                                    <span>Total:</span>
                                    <span id="total-cost-display">{{ number_format($invoice->shipping_cost + $invoice->customs_fee + $invoice->handling_fee + $invoice->insurance_fee + $invoice->other_fees + $invoice->cartons_cost, 2) }} XOF</span>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex gap-3">
                            <button type="submit" class="btn-primary">
                                Sauvegarder les modifications
                            </button>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-outline-primary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations sur l'expédition -->
        <div class="space-y-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Détails de l'expédition</h2>
                </div>
                <div class="card-body space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Numéro de suivi</p>
                        <p class="font-medium">{{ $invoice->parcel->tracking_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Client</p>
                        <p class="font-medium">{{ $invoice->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Mode de transport</p>
                        <p class="font-medium">
                            @switch($invoice->parcel->transport_mode)
                                @case('air_express')
                                    ✈️ Avion Express
                                    @break
                                @case('air_normal')
                                    ✈️ Avion Normal
                                    @break
                                @case('sea')
                                @case('boat')
                                    🚢 Bateau (Cargo)
                                    @break
                                @default
                                    {{ $invoice->parcel->transport_mode }}
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nombre de cartons</p>
                        <p class="font-medium">{{ $invoice->parcel->cartons_count ?? 1 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Poids</p>
                        <p class="font-medium">{{ $invoice->parcel->weight ?? 'En attente' }} {{ $invoice->parcel->weight ? 'kg' : '' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut de la facture</p>
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartonsCostInput = document.getElementById('cartons_cost');
    const cartonsCostDisplay = document.getElementById('cartons-cost-display');
    const totalCostDisplay = document.getElementById('total-cost-display');

    // Coûts fixes (récupérés depuis la vue)
    const fixedCosts = {
        shipping: {{ $invoice->shipping_cost }},
        customs: {{ $invoice->customs_fee }},
        handling: {{ $invoice->handling_fee }},
        insurance: {{ $invoice->insurance_fee }},
        other: {{ $invoice->other_fees }}
    };

    function updateTotals() {
        const cartonsCost = parseFloat(cartonsCostInput.value) || 0;
        const total = fixedCosts.shipping + fixedCosts.customs + fixedCosts.handling +
                    fixedCosts.insurance + fixedCosts.other + cartonsCost;

        cartonsCostDisplay.textContent = cartonsCost.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' XOF';
        totalCostDisplay.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' XOF';
    }

    cartonsCostInput.addEventListener('input', updateTotals);
});
</script>
@endsection
