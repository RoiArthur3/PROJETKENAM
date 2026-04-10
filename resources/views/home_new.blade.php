@extends('layouts.marketing')

@section('title', 'Groupage - Votre Solution d\'Expédition Internationale')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-orange-50">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-gray-900">Groupage</span>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#how-it-works" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Comment ça marche</a>
                        <a href="{{ route('tarifications') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Tarifs</a>
                        <a href="{{ url('/login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Connexion</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-orange-500 text-white px-6 py-2 rounded-full text-sm font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        Commencer gratuitement
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-24 pb-32 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-orange-500/10"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-blue-500/20 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-orange-500/20 rounded-full filter blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Économisez jusqu'à 70% sur vos expéditions
                </div>

                <!-- Titre principal -->
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 mb-6">
                    Expédiez vos colis
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-orange-500">
                        partout dans le monde
                    </span>
                </h1>

                <!-- Sous-titre -->
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                    La solution simple et économique pour vos expéditions internationales.
                    Suivez vos colis en temps réel et bénéficiez des meilleurs tarifs du marché.
                </p>

                <!-- Formulaire de suivi -->
                <div class="max-w-2xl mx-auto mb-12">
                    <form method="GET" action="{{ url('/') }}" class="bg-white rounded-2xl shadow-xl p-2 flex flex-col sm:flex-row gap-2">
                        <input
                            name="tracking_number"
                            value="{{ $trackingNumber ?? '' }}"
                            type="text"
                            class="flex-1 px-6 py-4 text-gray-900 placeholder-gray-500 bg-transparent focus:outline-none focus:ring-0"
                            placeholder="Entrez votre numéro de suivi..."
                        />
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-orange-500 text-white px-8 py-4 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Suivre mon colis
                        </button>
                    </form>

                    @if(($trackingNumber ?? '') !== '')
                        <div class="mt-4">
                            @if($parcel)
                                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 6 9 17l-5-5"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-green-800">Colis trouvé</div>
                                            <div class="text-sm text-green-600">Statut: {{ $parcel->status }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-red-800">Aucun colis trouvé</div>
                                            <div class="text-sm text-red-600">Vérifiez le numéro de suivi</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 mb-2">15K+</div>
                        <div class="text-gray-600">Colis expédiés</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-orange-500 mb-2">50+</div>
                        <div class="text-gray-600">Pays desservis</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-green-600 mb-2">98%</div>
                        <div class="text-gray-600">Clients satisfaits</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <!-- En-tête de section -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-800 rounded-full text-sm font-semibold mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Simple et rapide
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                    Comment ça marche ?
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Expédiez vos colis en 3 étapes simples. Pas de complications, juste des résultats.
                </p>
            </div>

            <!-- Étapes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <!-- Ligne de connexion -->
                <div class="hidden md:block absolute top-12 left-1/4 right-1/4 h-1 bg-gradient-to-r from-blue-200 via-orange-200 to-blue-200"></div>

                <!-- Étape 1 -->
                <div class="relative text-center group">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center mx-auto shadow-lg transform transition-transform duration-300 group-hover:scale-110">
                            <span class="text-3xl font-bold text-white">1</span>
                        </div>
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white border-4 border-blue-600 rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Créez votre compte</h3>
                    <p class="text-gray-600 mb-6">Inscrivez-vous gratuitement en moins de 2 minutes et accédez à votre espace personnel.</p>
                    <div class="bg-blue-50 rounded-xl p-4">
                        <ul class="text-sm text-blue-800 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Inscription 100% gratuite
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Vérification email instantanée
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Accès immédiat à toutes les fonctionnalités
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="relative text-center group">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto shadow-lg transform transition-transform duration-300 group-hover:scale-110">
                            <span class="text-3xl font-bold text-white">2</span>
                        </div>
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white border-4 border-orange-500 rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Envoyez vos colis</h3>
                    <p class="text-gray-600 mb-6">Expédiez vos colis vers notre entrepôt et nous nous occupons de tout le reste.</p>
                    <div class="bg-orange-50 rounded-xl p-4">
                        <ul class="text-sm text-orange-800 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Envoi vers notre adresse dédiée
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Réception et pesée automatiques
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Photos de validation à chaque étape
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="relative text-center group">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto shadow-lg transform transition-transform duration-300 group-hover:scale-110">
                            <span class="text-3xl font-bold text-white">3</span>
                        </div>
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white border-4 border-green-500 rounded-full"></div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Recevez chez vous</h3>
                    <p class="text-gray-600 mb-6">Suivez vos colis en temps réel et recevez-chez vous ou en point relais.</p>
                    <div class="bg-green-50 rounded-xl p-4">
                        <ul class="text-sm text-green-800 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Suivi temps réel 24/7
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Groupage optimisé pour économiser
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Livraison à domicile ou point relais
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- CTA Principal -->
            <div class="text-center mt-20">
                <div class="bg-gradient-to-r from-blue-600 to-orange-500 rounded-3xl p-12 text-white max-w-4xl mx-auto shadow-2xl transform transition-transform duration-300 hover:scale-105">
                    <h3 class="text-3xl font-bold mb-4">Prêt à commencer ?</h3>
                    <p class="text-xl mb-8 text-blue-100">
                        Rejoignez des milliers de clients qui économisent chaque mois sur leurs expéditions
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            🚀 Créer mon compte gratuit
                        </a>
                        <a href="{{ route('tarifications') }}" class="bg-white/20 text-white px-8 py-4 rounded-full font-bold text-lg border-2 border-white/30 hover:bg-white/30 transition-all duration-300">
                            💰 Voir les tarifs
                        </a>
                    </div>
                    <div class="mt-6 flex items-center justify-center text-sm text-blue-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Sans engagement • Annulation à tout moment • Support 7j/7
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
