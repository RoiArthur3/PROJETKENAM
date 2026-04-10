@extends('stock.layouts.main')

@section('module-title', $product->designation)
@section('module-description', 'Détails et historique du produit')

@section('module-actions')
    <a href="{{ route('stock.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    <a href="{{ route('stock.products.edit', $product) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Modifier
    </a>
    <a href="{{ route('stock.products.movements', $product) }}" class="btn btn-info">
        <i class="fas fa-exchange-alt"></i> Mouvements
    </a>
@endsection

@section('module-content')
    <div class="row">
        <!-- Détails Produit -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informations Produit
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Référence:</strong> {{ $product->reference }}
                    </div>
                    <div class="mb-3">
                        <strong>Catégorie:</strong> {{ $product->category->name ?? '-' }}
                    </div>
                    <div class="mb-3">
                        <strong>Unité:</strong> {{ $product->unit }}
                    </div>
                    <div class="mb-3">
                        <strong>Prix Unitaire HT:</strong> {{ number_format($product->unit_price, 0, ',', ' ') }} FCFA
                    </div>
                    <div class="mb-3">
                        <strong>Stock Minimum:</strong> {{ $product->min_stock_level ?? '-' }}
                    </div>
                    <div class="mb-3">
                        <strong>Statut:</strong>
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                            {{ $product->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <div>
                        <strong>Description:</strong>
                        <p class="text-muted">{{ $product->description ?? 'Aucune description' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock par Entrepôt -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-warehouse"></i> Stock par Entrepôt
                    </h6>
                </div>
                <div class="card-body">
                    @if($stockLevels->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Entrepôt</th>
                                        <th class="text-end">Stock</th>
                                        <th class="text-end">Valeur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockLevels as $level)
                                        <tr>
                                            <td>{{ $level->warehouse->name ?? 'Inconnu' }}</td>
                                            <td class="text-end">{{ $level->current_stock }}</td>
                                            <td class="text-end">{{ number_format($level->current_stock * $product->unit_price, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th>Total</th>
                                        <th class="text-end">{{ $stockLevels->sum('current_stock') }}</th>
                                        <th class="text-end">{{ number_format($stockLevels->sum('total_value'), 0, ',', ' ') }} FCFA</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p>Aucun stock enregistré</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Derniers Mouvements -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exchange-alt"></i> Derniers Mouvements
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentMovements->count() > 0)
                        <div class="list-group">
                            @foreach($recentMovements as $movement)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $movement->type === 'entry' ? 'Entrée' : 'Sortie' }}</strong>
                                            <div class="small text-muted">
                                                {{ $movement->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold">{{ $movement->quantity }}</span>
                                            <div class="small">
                                                {{ $movement->warehouse->name ?? 'Inconnu' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2 text-center">
                            <a href="{{ route('stock.products.movements', $product) }}" class="btn btn-sm btn-outline-primary">
                                Voir tout l'historique
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>Aucun mouvement récent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
