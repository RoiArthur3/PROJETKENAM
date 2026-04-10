@extends('layouts.app')

@section('title', 'Émettre une facture')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Émettre une nouvelle facture</h1>
            <p class="text-sm text-gray-500">Créez une facture pour un colis existant</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn-outline-primary">
            Retour aux factures
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire de création -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Informations de la facture</h2>
                    <p class="text-sm text-gray-500">Remplissez les détails de la facture</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf

                        <!-- Sélection du client -->
                        <div class="space-y-2 mb-6">
                            <label class="block text-sm font-medium text-gray-700" for="client_id">
                                Client *
                            </label>
                            <select id="client_id" name="client_id" class="input-select" required>
                                <option value="">Sélectionnez un client...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                            data-email="{{ $client->email }}"
                                            data-phone="{{ $client->phone ?? '' }}"
                                            data-company="{{ $client->company_name ?? '' }}">
                                        {{ $client->name }}
                                        @if($client->company_name)
                                            ({{ $client->company_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Informations du client sélectionné -->
                        <div id="client-info" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-medium text-blue-900 mb-2">Informations du client</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-blue-700">Email:</span>
                                    <span id="client-email" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Téléphone:</span>
                                    <span id="client-phone" class="font-medium ml-1">-</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-blue-700">Société:</span>
                                    <span id="client-company" class="font-medium ml-1">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sélection du colis -->
                        <div class="space-y-2 mb-6">
                            <label class="block text-sm font-medium text-gray-700" for="parcel_id">
                                Colis concerné *
                            </label>
                            <select id="parcel_id" name="parcel_id" class="input-select" required>
                                <option value="">Sélectionnez un colis...</option>
                                @foreach($parcelsWithoutInvoice as $parcel)
                                    <option value="{{ $parcel->id }}"
                                            data-user="{{ $parcel->user->name }}"
                                            data-user-email="{{ $parcel->user->email }}"
                                            data-user-phone="{{ $parcel->user->phone ?? '' }}"
                                            data-user-company="{{ $parcel->user->company_name ?? '' }}"
                                            data-tracking="{{ $parcel->tracking_number }}"
                                            data-weight="{{ $parcel->weight ?? 'N/A' }}"
                                            data-mode="{{ $parcel->transport_mode }}"
                                            data-destination="{{ $parcel->destination_country ?? 'N/A' }}"
                                            data-recipient="{{ $parcel->recipient_name ?? 'N/A' }}">
                                        {{ $parcel->tracking_number }} - {{ $parcel->user->name }}
                                        @if($parcel->user->company_name)
                                            ({{ $parcel->user->company_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500">
                                Seuls les colis sans facture sont affichés
                            </p>
                        </div>

                        <!-- Informations du colis sélectionné -->
                        <div id="parcel-info" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-medium text-blue-900 mb-2">Informations du colis et du client</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-blue-700">Numéro de suivi:</span>
                                    <span id="info-tracking" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Client:</span>
                                    <span id="info-user" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Email client:</span>
                                    <span id="info-user-email" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Téléphone:</span>
                                    <span id="info-user-phone" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Société:</span>
                                    <span id="info-user-company" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Destination:</span>
                                    <span id="info-destination" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Poids:</span>
                                    <span id="info-weight" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Mode:</span>
                                    <span id="info-mode" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Destinataire:</span>
                                    <span id="info-recipient" class="font-medium ml-1">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700" for="issue_date">
                                    Date d'émission *
                                </label>
                                <input
                                    type="date"
                                    id="issue_date"
                                    name="issue_date"
                                    value="{{ now()->format('Y-m-d') }}"
                                    class="input-text"
                                    required
                                >
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700" for="due_date">
                                    Date d'échéance
                                </label>
                                <input
                                    type="date"
                                    id="due_date"
                                    name="due_date"
                                    class="input-text"
                                >
                            </div>
                        </div>

                        <!-- Coûts -->
                        <div class="space-y-4 mb-6">
                            <h3 class="text-sm font-medium text-gray-900">Détail des coûts</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="shipping_cost">
                                        Coût de transport (FCFA) *
                                    </label>
                                    <input
                                        type="number"
                                        id="shipping_cost"
                                        name="shipping_cost"
                                        value="0"
                                        min="0"
                                        step="0.01"
                                        class="input-text cost-input"
                                        required
                                    >
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="cartons_cost">
                                        Coût des cartons (FCFA) *
                                    </label>
                                    <input
                                        type="number"
                                        id="cartons_cost"
                                        name="cartons_cost"
                                        value="0"
                                        min="0"
                                        step="0.01"
                                        class="input-text cost-input"
                                        required
                                    >
                                </div>
                                                            </div>
                        </div>

                        <!-- Résumé des coûts -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Résumé des coûts</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Coût de transport:</span>
                                    <span id="summary-shipping" class="font-medium">0 FCFA</span>
                                </div>
                                <div class="flex justify-between font-medium text-blue-600">
                                    <span>Coût des cartons:</span>
                                    <span id="summary-cartons">0 FCFA</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between font-semibold text-lg">
                                    <span>Total:</span>
                                    <span id="summary-total" class="text-blue-600">0 FCFA</span>
                                </div>
                            </div>
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
                                placeholder="Ajoutez des notes concernant cette facture..."
                            ></textarea>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex gap-3">
                            <button type="submit" class="btn-primary">
                                Créer la facture
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn-outline-primary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="space-y-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Aide</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div>
                        <h3 class="font-medium text-gray-900">Étapes de création</h3>
                        <ol class="list-decimal list-inside mt-2 space-y-1 text-gray-600">
                            <li>Sélectionnez un client (optionnel)</li>
                            <li>Sélectionnez un colis sans facture</li>
                            <li>Vérifiez les informations du colis</li>
                            <li>Saisissez les différents coûts</li>
                            <li>Ajoutez des notes si nécessaire</li>
                            <li>Validez la création</li>
                        </ol>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Important</h3>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-gray-600">
                            <li>Un colis ne peut avoir qu'une seule facture</li>
                            <li>Le numéro de facture est généré automatiquement</li>
                            <li>Le statut initial sera "brouillon"</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Statistiques</h2>
                </div>
                <div class="card-body space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Colis sans facture:</span>
                        <span class="font-medium">{{ $parcelsWithoutInvoice->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total des clients:</span>
                        <span class="font-medium">{{ $clients->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du formulaire
    const clientSelect = document.getElementById('client_id');
    const clientInfo = document.getElementById('client-info');
    const parcelSelect = document.getElementById('parcel_id');
    const parcelInfo = document.getElementById('parcel-info');

    // Éléments d'information client
    const clientEmail = document.getElementById('client-email');
    const clientPhone = document.getElementById('client-phone');
    const clientCompany = document.getElementById('client-company');

    // Éléments d'information colis
    const infoTracking = document.getElementById('info-tracking');
    const infoUser = document.getElementById('info-user');
    const infoUserEmail = document.getElementById('info-user-email');
    const infoUserPhone = document.getElementById('info-user-phone');
    const infoUserCompany = document.getElementById('info-user-company');
    const infoDestination = document.getElementById('info-destination');
    const infoWeight = document.getElementById('info-weight');
    const infoMode = document.getElementById('info-mode');
    const infoRecipient = document.getElementById('info-recipient');

    // Éléments de résumé
    const summaryShipping = document.getElementById('summary-shipping');
    const summaryCartons = document.getElementById('summary-cartons');
    const summaryTotal = document.getElementById('summary-total');

    // Gestion de la sélection du client
    clientSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            // Afficher les informations du client
            clientInfo.classList.remove('hidden');
            clientEmail.textContent = selectedOption.dataset.email || '-';
            clientPhone.textContent = selectedOption.dataset.phone || '-';
            clientCompany.textContent = selectedOption.dataset.company || '-';
        } else {
            // Masquer les informations
            clientInfo.classList.add('hidden');
        }
    });

    // Gestion de la sélection du colis
    parcelSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            // Afficher les informations du colis et du client
            parcelInfo.classList.remove('hidden');
            infoTracking.textContent = selectedOption.dataset.tracking || '-';
            infoUser.textContent = selectedOption.dataset.user || '-';
            infoUserEmail.textContent = selectedOption.dataset.userEmail || '-';
            infoUserPhone.textContent = selectedOption.dataset.userPhone || '-';
            infoUserCompany.textContent = selectedOption.dataset.userCompany || '-';
            infoDestination.textContent = selectedOption.dataset.destination || '-';
            infoWeight.textContent = selectedOption.dataset.weight || '-';
            infoRecipient.textContent = selectedOption.dataset.recipient || '-';

            // Formatter le mode de transport
            const mode = selectedOption.dataset.mode || '-';
            let modeText = mode;
            switch(mode) {
                case 'air_express': modeText = '✈️ Avion Express'; break;
                case 'air_normal': modeText = '✈️ Avion Normal'; break;
                case 'sea': modeText = '🚢 Bateau'; break;
                case 'boat': modeText = '🚢 Bateau'; break;
            }
            infoMode.textContent = modeText;
        } else {
            // Masquer les informations
            parcelInfo.classList.add('hidden');
        }
    });

    // Mise à jour des totaux
    function updateTotals() {
        const shipping = parseFloat(document.getElementById('shipping_cost').value) || 0;
        const cartons = parseFloat(document.getElementById('cartons_cost').value) || 0;

        const total = shipping + cartons;

        // Mettre à jour le résumé
        summaryShipping.textContent = shipping.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' FCFA';
        summaryCartons.textContent = cartons.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' FCFA';
        summaryTotal.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' FCFA';
    }

    // Écouteurs pour les champs de coût
    costInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    // Initialisation
    updateTotals();
});
</script>
@endsection
