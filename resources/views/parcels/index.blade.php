@extends('layouts.app')

@php
function getStatusColor($status) {
    $colors = [
        'announced' => 'bg-gray-100 text-gray-800',
        'received' => 'bg-blue-100 text-blue-800',
        'inspected' => 'bg-yellow-100 text-yellow-800',
        'rejected' => 'bg-red-100 text-red-800',
        'grouped' => 'bg-purple-100 text-purple-800',
        'in_transit' => 'bg-indigo-100 text-indigo-800',
        'arrived' => 'bg-green-100 text-green-800',
        'fees_calculated' => 'bg-orange-100 text-orange-800',
        'paid' => 'bg-green-100 text-green-800',
        'delivered' => 'bg-green-100 text-green-800',
    ];
    return $colors[$status] ?? 'bg-gray-100 text-gray-800';
}

function getStatusText($status) {
    $texts = [
        'announced' => 'Annoncé',
        'received' => 'Reçu',
        'inspected' => 'Inspecté',
        'rejected' => 'Rejeté',
        'grouped' => 'Groupé',
        'in_transit' => 'En transit',
        'arrived' => 'Arrivé',
        'fees_calculated' => 'Frais calculés',
        'paid' => 'Payé',
        'delivered' => 'Livré',
    ];
    return $texts[$status] ?? $status;
}
@endphp

@section('title', 'Mes Colis')
@section('subtitle', 'Suivi et statut de vos envois')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Mes Colis</h1>
                <p class="mt-1 text-sm text-blue-700">Suivi et statut de vos envois</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ url('/shipments/create') }}" class="btn-primary rounded-xl px-4 py-3">Envoyer un colis</a>
                            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                <div class="pointer-events-none absolute -right-6 -top-6 text-blue-200/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-28 w-28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <path d="M3.3 7l8.7 5 8.7-5" />
                        <path d="M12 22V12" />
                    </svg>
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $parcelsCount ?? 0 }}</div>
                        <div class="mt-1 text-sm text-blue-700">Colis déclarés</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h.05a2.5 2.5 0 014.9 0H18a1 1 0 001-1v-2a1 1 0 00-.293-.707l-3-3A1 1 0 0015 7h-1V5a1 1 0 00-1-1H3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                <div class="pointer-events-none absolute -right-6 -top-6 text-green-200/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-28 w-28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7l-8.5 8.5L7 11" />
                        <path d="M12 22c5.5 0 10-4.5 10-10S17.5 2 12 2 2 6.5 2 12s4.5 10 10 10z" />
                    </svg>
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Livrés</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $parcels->where('status', 'delivered')->count() }}</div>
                        <div class="mt-1 text-sm text-blue-700">Colis livrés</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-green-50 text-green-700 flex items-center justify-center">
                        <span class="text-sm font-semibold">✓</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                <div class="pointer-events-none absolute -right-6 -top-6 text-yellow-200/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-28 w-28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">En transit</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $parcels->whereIn('status', ['in_transit', 'arrived'])->count() }}</div>
                        <div class="mt-1 text-sm text-blue-700">Colis en cours</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-yellow-50 text-yellow-700 flex items-center justify-center">
                        <span class="text-sm font-semibold">⏱</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                <div class="pointer-events-none absolute -right-6 -top-6 text-orange-200/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-28 w-28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 14l2 2 4-4" />
                        <path d="M21 12c0 6-4 10-9 10S3 18 3 12 7 2 12 2s9 4 9 10z" />
                        <path d="M12 6v6" />
                        <path d="M12 18h.01" />
                    </svg>
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">En attente</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $parcels->whereIn('status', ['announced', 'received', 'inspected'])->count() }}</div>
                        <div class="mt-1 text-sm text-blue-700">Colis en attente</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-orange-50 text-orange-700 flex items-center justify-center">
                        <span class="text-sm font-semibold">⏳</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recherche avancée -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">🔍 Recherche de colis</h2>
                <button onclick="toggleAdvancedSearch()" class="text-blue-600 hover:text-blue-800 text-sm">
                    <span id="advancedSearchToggle">Afficher les filtres avancés</span>
                </button>
            </div>

            <!-- Recherche simple -->
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Numéro de tracking, destination, contenu..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button onclick="performSearch()" class="btn-primary px-6 py-3 rounded-lg">
                    Rechercher
                </button>
                <button onclick="clearSearch()" class="btn-outline-primary px-6 py-3 rounded-lg">
                    Effacer
                </button>
            </div>

            <!-- Filtres avancés -->
            <div id="advancedSearch" class="hidden">
                <div class="border-t pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tous les statuts</option>
                                <option value="announced">Annoncé</option>
                                <option value="received">Reçu</option>
                                <option value="inspected">Inspecté</option>
                                <option value="rejected">Rejeté</option>
                                <option value="grouped">Groupé</option>
                                <option value="in_transit">En transit</option>
                                <option value="arrived">Arrivé</option>
                                <option value="fees_calculated">Frais calculés</option>
                                <option value="paid">Payé</option>
                                <option value="delivered">Livré</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mode de transport</label>
                            <select id="transportFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tous les modes</option>
                                <option value="air_normal">Air Normal</option>
                                <option value="air_express">Air Express</option>
                                <option value="sea">Maritime</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays de destination</label>
                            <input type="text" id="destinationFilter" placeholder="Ex: France, Côte d'Ivoire..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                            <input type="date" id="dateStartFilter"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                            <input type="date" id="dateEndFilter"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button onclick="applyAdvancedFilters()" class="btn-primary w-full px-4 py-2 rounded-lg">
                                Appliquer les filtres
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sélecteur de devise -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-900">💰 Devise</div>
                    <div class="text-xs text-blue-700">Afficher les montants dans votre devise</div>
                </div>
                <div class="flex items-center gap-3">
                    <select id="currencySelector" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="EUR">EUR - Euro</option>
                    </select>
                    <button onclick="refreshRates()" class="text-blue-600 hover:text-blue-800 text-sm">
                        🔄 Actualiser les taux
                    </button>
                </div>
            </div>
            <div id="conversionInfo" class="hidden mt-3 p-3 bg-blue-50 rounded-lg">
                <div class="text-sm text-blue-800">
                    <span id="conversionText"></span>
                </div>
            </div>
        </div>

        <!-- Filtres rapides -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex flex-wrap gap-2">
                <button onclick="filterParcels('all')" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-blue-100 text-blue-800">
                    Tous ({{ $parcelsCount ?? 0 }})
                </button>
                <button onclick="filterParcels('delivered')" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">
                    Livrés ({{ $parcels->where('status', 'delivered')->count() }})
                </button>
                <button onclick="filterParcels('transit')" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">
                    En transit ({{ $parcels->whereIn('status', ['in_transit', 'arrived'])->count() }})
                </button>
                <button onclick="filterParcels('pending')" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">
                    En attente ({{ $parcels->whereIn('status', ['announced', 'received', 'inspected'])->count() }})
                </button>
            </div>
        </div>

        <!-- Liste des colis -->
        <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-100" id="parcelsList">
                @forelse($parcels as $parcel)
                    <div class="parcel-item px-5 py-4 hover:bg-gray-50"
                         data-status="{{ $parcel->status }}"
                         data-destination="{{ $parcel->destination_country }}"
                         data-content="{{ $parcel->content_description ?? '' }}"
                         data-transport="{{ $parcel->transport_mode }}"
                         data-date="{{ $parcel->created_at->format('Y-m-d') }}"
                         data-value="{{ $parcel->declared_value ?? '' }}">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="font-semibold text-gray-900">{{ $parcel->tracking_number }}</div>
                                    <span class="px-2.5 py-1 text-xs rounded-full {{ getStatusColor($parcel->status) }}">
                                        {{ getStatusText($parcel->status) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div><strong>Destination:</strong> {{ $parcel->destination_country }}</div>
                                    <div><strong>Transport:</strong> {{ ucfirst(str_replace('_', ' ', $parcel->transport_mode)) }}</div>
                                    @if($parcel->content_description)
                                        <div class="sm:col-span-2"><strong>Contenu:</strong> {{ $parcel->content_description }}</div>
                                    @endif
                                    <div><strong>Créé le:</strong> {{ $parcel->created_at->format('d/m/Y') }}</div>
                                    @if($parcel->declared_value)
                                        <div><strong>Valeur:</strong> {{ number_format($parcel->declared_value, 2) }}€</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('parcels.show', $parcel->id) }}" class="btn-primary px-3 py-2 rounded-lg text-sm">
                                    Détails
                                </a>
                                <button onclick="trackParcel('{{ $parcel->tracking_number }}')" class="btn-outline-primary px-3 py-2 rounded-lg text-sm">
                                    Suivre
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <div class="text-sm font-medium text-gray-900">Aucun colis</div>
                        <div class="mt-1 text-sm text-blue-700">Commencez par déclarer un colis.</div>
                        <div class="mt-4">
                            <a href="{{ url('/shipments/create') }}" class="btn-primary rounded-xl px-4 py-3">Envoyer un colis</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        let allParcels = [];
        let availableCurrencies = {};
        let currentCurrency = 'EUR';
        let exchangeRates = {};

        // Initialiser les données des colis
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrencies();
            initializeParcels();
            setupCurrencySelector();
        });

        function initializeParcels() {
            // Collecter toutes les données des colis
            const parcelElements = document.querySelectorAll('.parcel-item');
            parcelElements.forEach(element => {
                const parcelData = {
                    element: element,
                    trackingNumber: element.querySelector('.font-semibold')?.textContent || '',
                    destination: element.querySelector('[data-destination]')?.getAttribute('data-destination') || '',
                    content: element.querySelector('[data-content]')?.getAttribute('data-content') || '',
                    status: element.getAttribute('data-status') || '',
                    transport: element.querySelector('[data-transport]')?.getAttribute('data-transport') || '',
                    date: element.querySelector('[data-date]')?.getAttribute('data-date') || '',
                    value: element.querySelector('[data-value]')?.getAttribute('data-value') || ''
                };
                allParcels.push(parcelData);
            });
        }

        async function loadCurrencies() {
            try {
                const response = await fetch(`${API_BASE}/currencies`);
                const data = await response.json();

                if (data.success) {
                    availableCurrencies = data.data;
                    populateCurrencySelector();
                }
            } catch (error) {
                console.error('Erreur lors du chargement des devises:', error);
            }
        }

        function populateCurrencySelector() {
            const selector = document.getElementById('currencySelector');
            selector.innerHTML = '';

            Object.entries(availableCurrencies).forEach(([code, info]) => {
                const option = document.createElement('option');
                option.value = code;
                option.textContent = `${code} - ${info.name}`;
                if (code === 'EUR') {
                    option.selected = true;
                }
                selector.appendChild(option);
            });
        }

        function setupCurrencySelector() {
            const selector = document.getElementById('currencySelector');
            selector.addEventListener('change', function() {
                currentCurrency = this.value;
                updateCurrencyDisplay();
            });
        }

        async function refreshRates() {
            try {
                const response = await fetch(`${API_BASE}/currency/rates?from_currency=EUR`);
                const data = await response.json();

                if (data.success) {
                    exchangeRates = {};
                    data.data.rates.forEach(rate => {
                        exchangeRates[rate.to_currency] = rate.rate;
                    });
                    updateCurrencyDisplay();
                    showNotification('Taux de change actualisés', 'success');
                }
            } catch (error) {
                console.error('Erreur lors de l\'actualisation des taux:', error);
                showNotification('Erreur lors de l\'actualisation des taux', 'error');
            }
        }

        function updateCurrencyDisplay() {
            if (currentCurrency === 'EUR') {
                hideConversionInfo();
                return;
            }

            const rate = exchangeRates[currentCurrency];
            if (rate) {
                showConversionInfo(rate);
                convertAllAmounts();
            } else {
                hideConversionInfo();
            }
        }

        function showConversionInfo(rate) {
            const info = document.getElementById('conversionInfo');
            const text = document.getElementById('conversionText');

            info.classList.remove('hidden');
            text.textContent = `1 EUR = ${rate.toFixed(4)} ${currentCurrency} (${availableCurrencies[currentCurrency]?.name || currentCurrency})`;
        }

        function hideConversionInfo() {
            document.getElementById('conversionInfo').classList.add('hidden');
        }

        function convertAllAmounts() {
            // Convertir les montants affichés dans les cartes de statistiques
            convertStatCards();

            // Convertir les montants dans les détails des colis
            convertParcelAmounts();
        }

        function convertStatCards() {
            // Cette fonction sera implémentée quand les montants seront disponibles
            // Pour l'instant, nous allons juste préparer la structure
        }

        function convertParcelAmounts() {
            allParcels.forEach(parcel => {
                const valueElements = parcel.element.querySelectorAll('[data-converted-value]');
                valueElements.forEach(element => {
                    const originalValue = parseFloat(element.getAttribute('data-original-value') || '0');
                    if (originalValue > 0 && currentCurrency !== 'EUR') {
                        const convertedValue = (originalValue * exchangeRates[currentCurrency]).toFixed(2);
                        const formattedValue = formatCurrency(convertedValue, currentCurrency);
                        element.textContent = formattedValue;
                    }
                });
            });
        }

        function formatCurrency(amount, currency) {
            const formats = {
                'EUR': { symbol: '€', position: 'after', decimals: 2 },
                'USD': { symbol: '$', position: 'before', decimals: 2 },
                'GBP': { symbol: '£', position: 'before', decimals: 2 },
                'XOF': { symbol: 'F CFA', position: 'after', decimals: 0 },
                'XAF': { symbol: 'F CFA', position: 'after', decimals: 0 },
                'CAD': { symbol: 'C$', position: 'before', decimals: 2 },
                'CHF': { symbol: 'CHF', position: 'before', decimals: 2 },
                'JPY': { symbol: '¥', position: 'before', decimals: 0 },
                'CNY': { symbol: '¥', position: 'before', decimals: 2 },
                'MAD': { symbol: 'MAD', position: 'before', decimals: 2 },
                'TND': { symbol: 'TND', position: 'before', decimals: 3 },
                'DZD': { symbol: 'DA', position: 'before', decimals: 2 },
                'NGN': { symbol: '₦', position: 'before', decimals: 2 },
                'GHS': { symbol: 'GH₵', position: 'before', decimals: 2 },
            };

            const format = formats[currency] || { symbol: currency, position: 'before', decimals: 2 };
            const formattedAmount = Number(amount).toFixed(format.decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

            if (format.position === 'before') {
                return format.symbol + ' ' + formattedAmount;
            } else {
                return formattedAmount + ' ' + format.symbol;
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Fonctions existantes...
        function toggleAdvancedSearch() {
            const advancedSearch = document.getElementById('advancedSearch');
            const toggle = document.getElementById('advancedSearchToggle');

            if (advancedSearch.classList.contains('hidden')) {
                advancedSearch.classList.remove('hidden');
                toggle.textContent = 'Masquer les filtres avancés';
            } else {
                advancedSearch.classList.add('hidden');
                toggle.textContent = 'Afficher les filtres avancés';
            }
        }

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();

            if (searchTerm === '') {
                showAllParcels();
                return;
            }

            let visibleCount = 0;
            allParcels.forEach(parcel => {
                const searchableText = [
                    parcel.trackingNumber,
                    parcel.destination,
                    parcel.content,
                    parcel.status,
                    parcel.transport
                ].join(' ').toLowerCase();

                if (searchableText.includes(searchTerm)) {
                    parcel.element.style.display = 'block';
                    visibleCount++;
                } else {
                    parcel.element.style.display = 'none';
                }
            });

            showSearchResults(visibleCount);
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('transportFilter').value = '';
            document.getElementById('destinationFilter').value = '';
            document.getElementById('dateStartFilter').value = '';
            document.getElementById('dateEndFilter').value = '';

            showAllParcels();
            hideSearchResults();
        }

        function applyAdvancedFilters() {
            const status = document.getElementById('statusFilter').value;
            const transport = document.getElementById('transportFilter').value;
            const destination = document.getElementById('destinationFilter').value.toLowerCase().trim();
            const dateStart = document.getElementById('dateStartFilter').value;
            const dateEnd = document.getElementById('dateEndFilter').value;

            let visibleCount = 0;

            allParcels.forEach(parcel => {
                let isVisible = true;

                // Filtre par statut
                if (status && parcel.status !== status) {
                    isVisible = false;
                }

                // Filtre par transport
                if (transport && parcel.transport !== transport) {
                    isVisible = false;
                }

                // Filtre par destination
                if (destination && !parcel.destination.toLowerCase().includes(destination)) {
                    isVisible = false;
                }

                // Filtre par date
                if (dateStart && parcel.date < dateStart) {
                    isVisible = false;
                }
                if (dateEnd && parcel.date > dateEnd) {
                    isVisible = false;
                }

                parcel.element.style.display = isVisible ? 'block' : 'none';
                if (isVisible) visibleCount++;
            });

            showSearchResults(visibleCount);
        }

        function showAllParcels() {
            allParcels.forEach(parcel => {
                parcel.element.style.display = 'block';
            });
            hideSearchResults();
        }

        function showSearchResults(count) {
            // Créer ou mettre à jour le message de résultats
            let resultsDiv = document.getElementById('searchResults');
            if (!resultsDiv) {
                resultsDiv = document.createElement('div');
                resultsDiv.id = 'searchResults';
                resultsDiv.className = 'bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4';
                document.querySelector('#parcelsList').parentNode.insertBefore(resultsDiv, document.querySelector('#parcelsList'));
            }

            resultsDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-blue-800">🔍 ${count} colis trouvé(s)</span>
                    <button onclick="clearSearch()" class="text-blue-600 hover:text-blue-800 text-sm">Effacer la recherche</button>
                </div>
            `;
        }

        function hideSearchResults() {
            const resultsDiv = document.getElementById('searchResults');
            if (resultsDiv) {
                resultsDiv.remove();
            }
        }

        function filterParcels(status) {
            const items = document.querySelectorAll('.parcel-item');
            const buttons = document.querySelectorAll('.filter-btn');

            // Mettre à jour les boutons
            buttons.forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-800');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            });
            event.target.classList.remove('bg-gray-100', 'text-gray-600');
            event.target.classList.add('bg-blue-100', 'text-blue-800');

            // Filtrer les colis
            items.forEach(item => {
                if (status === 'all') {
                    item.style.display = 'block';
                } else if (status === 'delivered') {
                    item.style.display = item.dataset.status === 'delivered' ? 'block' : 'none';
                } else if (status === 'transit') {
                    const transitStatuses = ['in_transit', 'arrived'];
                    item.style.display = transitStatuses.includes(item.dataset.status) ? 'block' : 'none';
                } else if (status === 'pending') {
                    const pendingStatuses = ['announced', 'received', 'inspected'];
                    item.style.display = pendingStatuses.includes(item.dataset.status) ? 'block' : 'none';
                }
            });
        }

        function trackParcel(trackingNumber) {
            // Rediriger vers la page de tracking
            window.location.href = '/?tracking_number=' + trackingNumber;
        }

        // Recherche en temps réel
        document.getElementById('searchInput').addEventListener('input', function(e) {
            if (e.target.value.length > 2 || e.target.value.length === 0) {
                performSearch();
            }
        });

        // Recherche avec la touche Entrée
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        }
    </script>
@endsection
