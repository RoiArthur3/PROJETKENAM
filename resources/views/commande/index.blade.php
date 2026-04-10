<x-dashboard-layout title="Historique des Commandes" icon="fa-solid fa-clipboard-list">
    <x-slot name="kpis">
        <x-kpi-card title="Total Commandes" value="{{ DB::table('commandes_recentes')->count() }}" icon="fa-solid fa-clipboard-list" color="primary" />
        <x-kpi-card title="En Attente" value="{{ DB::table('commandes_recentes')->where('statut', 'en_attente')->count() }}" icon="fa-solid fa-clock" color="warning" />
        <x-kpi-card title="Confirmées" value="{{ DB::table('commandes_recentes')->where('statut', 'confirmee')->count() }}" icon="fa-solid fa-play" color="info" />
        <x-kpi-card title="Livrées" value="{{ DB::table('commandes_recentes')->where('statut', 'livree')->count() }}" icon="fa-solid fa-check-circle" color="success" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fa-solid fa-clipboard-list me-2"></i>
                        Historique des Commandes
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('commercial.commande.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus me-1"></i>
                            Nouvelle Commande
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('commercial.commande.index') }}" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select">
                                <option value="">Tous</option>
                                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="livree" {{ request('statut') == 'livree' ? 'selected' : '' }}>Livrée</option>
                                <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control"
                                   value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="form-control"
                                   value="{{ request('date_fin') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-search me-1"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('commercial.commande.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fa-solid fa-undo me-1"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>

                    <!-- Tableau des commandes -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $commandes = DB::table('commandes_recentes');

                                    // Filtres
                                    if (request()->filled('statut')) {
                                        $commandes->where('statut', request('statut'));
                                    }

                                    if (request()->filled('date_debut')) {
                                        $commandes->where('date_commande', '>=', request('date_debut'));
                                    }

                                    if (request()->filled('date_fin')) {
                                        $commandes->where('date_commande', '<=', request('date_fin'));
                                    }

                                    $commandes = $commandes->orderBy('date_commande', 'desc')->paginate(15);
                                @endphp
                                @forelse($commandes as $commande)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $commande->reference }}</span>
                                        </td>
                                        <td>{{ $commande->client_nom }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td>
                                            {{ $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('commercial.commande.show', $commande->id) }}" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('commercial.commande.edit', $commande->id) }}" class="btn btn-outline-warning">
                                                    <i class="fa-solid fa-edit"></i>
                                                </a>
                                                <form action="{{ route('commercial.commande.destroy', $commande->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa-solid fa-clipboard-list fa-3x mb-3"></i>
                                            <h5 class="mb-0">Aucune commande trouvée</h5>
                                            <p class="text-muted">Aucune commande n'a été trouvée pour le moment.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $commandes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
