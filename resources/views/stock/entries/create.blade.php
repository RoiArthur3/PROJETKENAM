@extends('layouts.app')

@section('title', 'Nouvelle Entrée de Stock')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-plus-circle text-success me-2"></i>Nouvelle entrée de stock</h5>
      <a href="{{ route('stock.entries') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('stock.entries.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
          <label class="form-label small">Produit *</label>
          <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
            <option value="">Sélectionner un produit</option>
            @foreach($products as $product)
              <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                {{ $product->name }} ({{ $product->reference }})
              </option>
            @endforeach
          </select>
          @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Entrepôt *</label>
          <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
            <option value="">Sélectionner un entrepôt</option>
            @foreach($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
            @endforeach
          </select>
          @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Fournisseur</label>
          <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
            <option value="">Aucun fournisseur</option>
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
            @endforeach
          </select>
          @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Date de mouvement *</label>
          <input type="date" name="movement_date" class="form-control @error('movement_date') is-invalid @enderror" value="{{ old('movement_date', date('Y-m-d')) }}" required>
          @error('movement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Quantité *</label>
          <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" min="0.01" step="0.01" required>
          @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Prix unitaire (FCFA)</label>
          <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}" min="0" step="0.01">
          @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Notes</label>
          <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
          @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('stock.entries') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
