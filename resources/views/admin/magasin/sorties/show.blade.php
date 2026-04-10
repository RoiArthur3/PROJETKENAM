@extends('layouts.app')

@section('title', 'Détails Sortie - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        Détails de la Sortie
                    </h4>
                </div>
                <div class="card-body">
                    
                    <!-- Informations de la sortie -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informations générales</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Référence:</strong></td>
                                            <td><span class="badge bg-warning text-dark">{{ $sortie->reference_sortie }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $sortie->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Utilisateur:</strong></td>
                                            <td>{{ $sortie->user_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Détails du mouvement</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Produit:</strong></td>
                                            <td>{{ $sortie->produit_nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Quantité:</strong></td>
                                            <td><span class="badge bg-danger">{{ $sortie->quantite }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Destination:</strong></td>
                                            <td>{{ $sortie->destination }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Motif -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Motif de la sortie</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $sortie->motif }}</p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('magasin.sorties') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                        <div>
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </button>
                            <a href="{{ route('magasin.sorties.edit', $sortie->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Modifier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
