@extends('layouts.app')

@section('title', 'Test Devises')
@section('subtitle', 'Test du système de conversion')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Test Devises</h1>
                <p class="mt-1 text-sm text-blue-700">Test du système de conversion automatique</p>
            </div>
        </div>

        <!-- Sélecteur de devise -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-lg font-semibold text-gray-900">💰 Sélection de devise</div>
                    <div class="text-sm text-blue-700">Choisissez la devise pour afficher les montants</div>
                </div>
                <button onclick="refreshRates()" class="text-blue-600 hover:text-blue-800 text-sm">
                    🔄 Actualiser les taux
                </button>
            </div>

            <div class="flex items-center gap-3 mb-4">
                <select id="currencySelector" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="EUR">EUR - Euro</option>
                </select>
            </div>

            <div id="conversionInfo" class="hidden mb-4 p-3 bg-blue-50 rounded-lg">
                <div class="text-sm text-blue-800">
                    <span id="conversionText"></span>
                </div>
            </div>
        </div>

        <!-- Test de conversion -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🧪 Test de conversion</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant en EUR</label>
                    <input type="number" id="testAmount" value="100" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Devise cible</label>
                    <select id="targetCurrency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="USD">USD - Dollar Américain</option>
                        <option value="XOF">XOF - Franc CFA BCEAO</option>
                        <option value="GBP">GBP - Livre Sterling</option>
                        <option value="CAD">CAD - Dollar Canadien</option>
                        <option value="MAD">MAD - Dirham Marocain</option>
                    </select>
                </div>
            </div>

            <button onclick="testConversion()" class="mt-4 btn-primary px-4 py-2 rounded-lg">
                Convertir
            </button>

            <div id="conversionResult" class="hidden mt-4 p-4 bg-green-50 rounded-lg">
                <div class="text-green-800">
                    <div id="resultText"></div>
                </div>
            </div>
        </div>

        <!-- Simulation de facture -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🧾 Simulation de facture</h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Frais de transport</span>
                    <span class="font-semibold" data-amount="50" data-converted="true">50.00 €</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Frais de douane</span>
                    <span class="font-semibold" data-amount="25" data-converted="true">25.00 €</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Assurance</span>
                    <span class="font-semibold" data-amount="15" data-converted="true">15.00 €</span>
                </div>
                <div class="flex justify-between items-center pt-3">
                    <span class="text-lg font-bold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-blue-600" data-amount="90" data-converted="true">90.00 €</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        let availableCurrencies = {};
        let currentCurrency = 'EUR';
        let exchangeRates = {};

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrencies();
            setupCurrencySelector();
            refreshRates();
        });

        async function loadCurrencies() {
            try {
                const response = await fetch(`${API_BASE}/currencies`);
                const data = await response.json();

                if (data.success) {
                    availableCurrencies = data.data;
                    populateCurrencySelector();
                    populateTargetCurrencySelector();
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

        function populateTargetCurrencySelector() {
            const selector = document.getElementById('targetCurrency');
            selector.innerHTML = '';

            Object.entries(availableCurrencies).forEach(([code, info]) => {
                if (code !== 'EUR') {
                    const option = document.createElement('option');
                    option.value = code;
                    option.textContent = `${code} - ${info.name}`;
                    selector.appendChild(option);
                }
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
                resetAmounts();
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
            const elements = document.querySelectorAll('[data-converted="true"]');
            elements.forEach(element => {
                const originalAmount = parseFloat(element.getAttribute('data-amount'));
                if (originalAmount > 0 && currentCurrency !== 'EUR') {
                    const convertedValue = (originalAmount * exchangeRates[currentCurrency]).toFixed(2);
                    const formattedValue = formatCurrency(convertedValue, currentCurrency);
                    element.textContent = formattedValue;
                }
            });
        }

        function resetAmounts() {
            const elements = document.querySelectorAll('[data-converted="true"]');
            elements.forEach(element => {
                const originalAmount = parseFloat(element.getAttribute('data-amount'));
                element.textContent = formatCurrency(originalAmount, 'EUR');
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

        async function testConversion() {
            const amount = parseFloat(document.getElementById('testAmount').value);
            const targetCurrency = document.getElementById('targetCurrency').value;

            try {
                const response = await fetch(`${API_BASE}/currency/convert?amount=${amount}&from_currency=EUR&to_currency=${targetCurrency}`);
                const data = await response.json();

                if (data.success) {
                    const resultDiv = document.getElementById('conversionResult');
                    const resultText = document.getElementById('resultText');

                    resultDiv.classList.remove('hidden');
                    resultText.innerHTML = `
                        <div class="font-semibold">${data.data.formatted} = ${data.data.converted_formatted}</div>
                        <div class="text-sm mt-1">Taux: 1 EUR = ${data.data.rate} ${targetCurrency}</div>
                    `;
                }
            } catch (error) {
                console.error('Erreur lors de la conversion:', error);
                showNotification('Erreur lors de la conversion', 'error');
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
    </script>
@endsection
