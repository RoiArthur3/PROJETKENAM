@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Gestion des Projets</h1>
            <p class="text-gray-600 mt-2">Suivi complet des projets de l'entreprise</p>
        </div>
        <a href="{{ route('projets.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">+ Nouveau Projet</a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Brouillon</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['brouillon'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Validés</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['valides'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">En Cours</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['en_cours'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-gray-600 text-sm font-semibold uppercase">Terminés</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['termines'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Projets Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Projet</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Client</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Responsable</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Statut</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Avancement</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Dates</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('projets.show', $project->id) }}" class="font-semibold text-blue-600 hover:text-blue-700">
                                    {{ $project->nom }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">{{ ucfirst($project->type) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $project->client->nom ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $project->responsable->nom ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ 
                                    $project->statut === 'brouillon' ? 'bg-gray-100 text-gray-800' :
                                    ($project->statut === 'valide' ? 'bg-blue-100 text-blue-800' :
                                    ($project->statut === 'en_cours' ? 'bg-green-100 text-green-800' :
                                    ($project->statut === 'termine' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')))
                                }}">
                                    {{ ucfirst($project->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 justify-center min-w-fit">
                                    <div class="w-20 bg-gray-200 h-2 rounded">
                                        <div class="bg-blue-600 h-2 rounded" style="width: {{ $project->pourcentage_avancement }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $project->pourcentage_avancement }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="text-xs">
                                    <p class="text-gray-600">Début: {{ $project->date_debut ? $project->date_debut->format('d/m/y') : '-' }}</p>
                                    <p class="text-gray-600">Fin: {{ $project->date_fin_prevue ? $project->date_fin_prevue->format('d/m/y') : '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('projets.show', $project->id) }}" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200" title="Voir">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('projets.edit', $project->id) }}" class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200" title="Modifier">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('projets.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200" title="Supprimer">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-gray-500 text-lg">Aucun projet pour le moment.</p>
                                <a href="{{ route('projets.create') }}" class="text-blue-600 hover:text-blue-700 font-semibold mt-2 inline-block">Créer un nouveau projet →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($projects->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
