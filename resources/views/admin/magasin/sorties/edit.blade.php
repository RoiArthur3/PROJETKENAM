@extends('layouts.app')

@section('title', 'Modifier Sortie - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier la Sortie
                    </h4>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('magasin.sorties.update', $sortie->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="produit_id" class="form-label">Produit *</label>
                                    <select class="form-select" id="produit_id" name="produit_id" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($produits as $produit)
                                            <option value="{{ $produit->id }}" {{ $produit->id == $sortie->produit_id ? 'selected' : '' }}>
                                                {{ $produit->designation }} ({{ $produit->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité *</label>
                                    <input type="number" class="form-control" id="quantite" name="quantite" 
                                           value="{{ $sortie->quantite }}" min="1" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination *</label>
                                    <input type="text" class="form-control" id="destination" name="destination" 
                                           value="{{ $sortie->destination }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reference_sortie" class="form-label">Référence Sortie</label>
                                    <input type="text" class="form-control" id="reference_sortie" name="reference_sortie" 
                                           value="{{ $sortie->reference_sortie }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="motif" class="form-label">Motif de la sortie *</label>
                            <textarea class="form-control" id="motif" name="motif" rows="3" required>{{ $sortie->motif }}</textarea>
                        </div>
                        
                        <!-- Informations actuelles -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Informations actuelles</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Date de création:</strong> {{ $sortie->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Utilisateur:</strong> {{ $sortie->user_name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Dernière modification:</strong> {{ $sortie->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('magasin.sorties.show', $sortie->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
