@extends('layouts.marketing')

@section('title', 'Tarifications')

@section('content')
    <div class="relative">
        <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle at 15% 25%, rgba(37, 99, 235, .12), rgba(255,255,255,0) 45%), radial-gradient(circle at 80% 20%, rgba(37, 99, 235, .10), rgba(255,255,255,0) 50%), radial-gradient(circle at 70% 90%, rgba(37, 99, 235, .10), rgba(255,255,255,0) 50%), linear-gradient(to right, rgba(37, 99, 235, .06) 1px, rgba(255,255,255,0) 1px), linear-gradient(to bottom, rgba(37, 99, 235, .06) 1px, rgba(255,255,255,0) 1px); background-size: auto, auto, auto, 44px 44px, 44px 44px; background-position: 0 0, 0 0, 0 0, 0 0, 0 0;"></div>

        <header class="border-b border-blue-100 bg-white/80 backdrop-blur">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-10 w-auto" />
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ url('/') }}" class="btn btn-outline">Accueil</a>
                    <a href="{{ route('tarifications') }}" class="btn btn-primary">Tarifications</a>
                    <a href="{{ url('/login') }}" class="btn btn-outline">Connexion</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a>
                </div>
            </div>
        </header>

        <!-- Golden Gray Brilliant Separator -->
        <div class="h-2 bg-gradient-to-r from-gray-300 via-yellow-500 to-gray-300 shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent"></div>
        </div>

        <main>
            <!-- Hero Section -->
            <section class="relative bg-gradient-to-br from-blue-600 to-blue-700 text-white py-20">
                <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1200&auto=format&fit=crop&q=80');"></div>
                <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl sm:text-5xl font-bold mb-6">Nos Tarifs Transparents</h1>
                    <p class="text-xl text-blue-100 max-w-3xl mx-auto mb-8">
                        Des tarifs compétitifs et clairs pour tous vos besoins d'expédition internationale.
                        Consultez nos grilles tarifaires par mode de transport et destination.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="btn bg-white text-blue-600 hover:bg-blue-50 px-8 py-4 text-lg font-semibold">
                            Commencer à expédier
                        </a>
                        <a href="{{ url('/login') }}" class="btn bg-white/20 text-white border border-white/30 hover:bg-white/30 px-8 py-4 text-lg font-semibold">
                            Accéder à mon compte
                        </a>
                    </div>
                </div>
            </section>

            <!-- Tariffs Table Section -->
            <section class="py-20 bg-gray-50">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Grille Tarifaire Détaillée</h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Nos tarifs sont calculés en fonction du mode de transport, de la destination et du poids de vos colis.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold">Tarifs en vigueur</h3>
                                    <p class="text-blue-100 mt-1">Mis à jour en temps réel</p>
                                </div>
                                <div class="bg-white/20 rounded-lg px-4 py-2">
                                    <span class="text-sm font-semibold">{{ $tariffs->count() }} tarifs disponibles</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mode de transport</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Origine</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Destination</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tranche de poids</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prix / kg</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Frais fixes</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Devise</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($tariffs as $tariff)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                        @if($tariff->transport_mode === 'Avion')
                                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <span class="font-semibold text-gray-900">{{ $tariff->transport_mode }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">{{ $tariff->origin_country }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ $tariff->destination_country }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                    {{ $tariff->min_weight }}{{ !is_null($tariff->max_weight) ? ' - ' . $tariff->max_weight : '+' }} kg
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-semibold text-green-600">{{ number_format((float) $tariff->price_per_kg, 2) }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-semibold text-gray-900">{{ number_format((float) $tariff->fixed_fee, 2) }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $tariff->currency }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M9 14l2 2 4-4m6 2h2a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h2"></path>
                                                    </svg>
                                                    <p class="text-lg font-medium text-gray-600">Aucune tarification disponible</p>
                                                    <p class="text-sm text-gray-400 mt-1">Veuillez réessayer plus tard</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Tarifs compétitifs</h3>
                            <p class="text-gray-600">Jusqu'à 70% d'économie sur vos expéditions internationales</p>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Facturation transparente</h3>
                            <p class="text-gray-600">Aucun frais caché, vous ne payez que ce que vous consommez</p>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Expédition rapide</h3>
                            <p class="text-gray-600">Livraison garantie selon les délais de chaque mode de transport</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-20 bg-blue-600 text-white">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl sm:text-4xl font-bold mb-6">Prêt à expédier ?</h2>
                    <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                        Créez votre compte gratuitement et commencez à bénéficier de nos tarifs préférentiels dès aujourd'hui
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="btn bg-white text-blue-600 px-8 py-4 text-lg font-semibold hover:bg-blue-50 transition-colors">
                            Créer mon compte gratuit
                        </a>
                        <a href="{{ url('/') }}" class="btn bg-white/10 text-white border border-white/20 px-8 py-4 text-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection
