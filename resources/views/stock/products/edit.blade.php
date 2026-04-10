@extends('stock.layouts.main')

@section('module-title', 'Modifier le Produit')
@section('module-description', 'Mettre à jour les informations du produit')

@section('module-actions')
    <a href="{{ route('stock.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
@endsection

@section('module-content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit"></i> Modifier {{ $product->designation }}
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('stock.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Référence *</label>
                        <input type="text" class="form-control" name="reference" value="{{ $product->reference }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Désignation *</label>
                        <input type="text" class="form-control" name="designation" value="{{ $product->designation }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Catégorie *</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Unité *</label>
                        <input type="text" class="form-control" name="unit" value="{{ $product->unit }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Prix Unitaire HT</label>
                        <input type="number" step="0.01" class="form-control" name="unit_price" value="{{ $product->unit_price }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stock Minimum</label>
                        <input type="number" class="form-control" name="min_stock_level" value="{{ $product->min_stock_level }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="status">
                            <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $product->description }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
