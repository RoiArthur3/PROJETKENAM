<x-dashboard-layout title="Détails de la demande" icon="fa-solid fa-clipboard-list">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-clipboard-list me-2"></i>
                        {{ $commande->reference }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('commercial.commandes.edit', $commande->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-edit me-1"></i>
                            Modifier
                        </a>
                        <a href="{{ route('commercial.commandes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i>
                            Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        Informations
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Référence :</strong></td>
                                            <td><span class="badge bg-primary">{{ $commande->reference }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type engin :</strong></td>
                                            <td>{{ $commande->type_engin }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Délai :</strong></td>
                                            <td>{{ $commande->date_fin ? $commande->date_fin->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email logistique :</strong></td>
                                            <td>{{ $commande->email_service }}</td>
                                        </tr>
                                        @if(!empty($commande->cc_emails))
                                            <tr>
                                                <td><strong>CC :</strong></td>
                                                <td>{{ $commande->cc_emails }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Statut :</strong></td>
                                            <td>
                                                @switch($commande->statut)
                                                    @case('brouillon')
                                                        <span class="badge bg-secondary">Brouillon</span>
                                                    @break
                                                    @case('envoye_logistique')
                                                        <span class="badge bg-info">Envoyé logistique</span>
                                                    @break
                                                    @case('repondu')
                                                        <span class="badge bg-warning">Répondu</span>
                                                    @break
                                                    @case('valide')
                                                        <span class="badge bg-success">Validé</span>
                                                    @break
                                                    @case('refuse')
                                                        <span class="badge bg-danger">Refusé</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $commande->statut }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    </table>

                                    @if($commande->commentaire)
                                        <div class="mt-3">
                                            <strong>Description</strong>
                                            <div class="border rounded p-2 mt-1">{{ $commande->commentaire }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <form method="POST" action="{{ route('commercial.commandes.envoyer-logistique', $commande->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-info" {{ in_array($commande->statut, ['brouillon', 'refuse'], true) ? '' : 'disabled' }}>
                                        <i class="fa-solid fa-paper-plane me-2"></i>
                                        Envoyer à la logistique
                                    </button>
                                </form>

                                <a href="{{ route('commercial.commandes.reponse.create', $commande->id) }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-reply me-2"></i>
                                    Réponse logistique
                                </a>

                                <form method="POST" action="{{ route('commercial.commandes.valider', $commande->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success" {{ $commande->statut === 'repondu' ? '' : 'disabled' }}>
                                        <i class="fa-solid fa-check me-2"></i>
                                        Valider
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('commercial.commandes.refuser', $commande->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" {{ in_array($commande->statut, ['envoye_logistique', 'repondu'], true) ? '' : 'disabled' }}>
                                        <i class="fa-solid fa-times me-2"></i>
                                        Refuser
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa-solid fa-truck me-2"></i>
                                        Réponse logistique
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @if($commande->reponse)
                                        @if($commande->reponse->commentaire)
                                            <div class="mb-3">
                                                <strong>Commentaire</strong>
                                                <div class="border rounded p-2 mt-1">{{ $commande->reponse->commentaire }}</div>
                                            </div>
                                        @endif

                                        @if($commande->reponse->fournisseurs && $commande->reponse->fournisseurs->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Fournisseur</th>
                                                            <th>Engin</th>
                                                            <th>Prix</th>
                                                            <th>Détails</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($commande->reponse->fournisseurs as $f)
                                                            <tr>
                                                                <td>{{ $f->fournisseur_nom }}</td>
                                                                <td>{{ $f->engin_disponible ?? '-' }}</td>
                                                                <td>
                                                                    {{ $f->prix !== null ? number_format((float) $f->prix, 2, ',', ' ') : '-' }}
                                                                    {{ $f->devise ?? '' }}
                                                                </td>
                                                                <td>{{ $f->details ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">Aucun fournisseur proposé.</p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">Aucune réponse enregistrée.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
