@extends('layouts.app')

@section('title', 'Modifier la Vérification')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier la vérification #{{ $checking->id }}</h1>
        <a href="{{ route('fleet.checking.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('fleet.checking.update', $checking->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Titre *</label>
                        <input type="text" name="title" class="form-control" value="{{ $checking->title }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Type</label>
                        <input type="text" name="type" class="form-control" value="{{ $checking->type }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $checking->description }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Checklist</label>
                        <select name="checklist_id" class="form-select">
                            <option value="">—</option>
                            @foreach($checklists as $cl)
                                <option value="{{ $cl->id }}" {{ $checking->checklist_id == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Inspecteur</label>
                        <select name="inspector_id" class="form-select">
                            <option value="">—</option>
                            @foreach($inspectors as $u)
                                <option value="{{ $u->id }}" {{ $checking->inspector_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Planifiée le</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ $checking->scheduled_at ? $checking->scheduled_at->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Véhicule (immatriculation)</label>
                        <input list="vehiculesList" name="vehicle_input" class="form-control" value="{{ $vehiculeImmat }}" placeholder="Ex: CI-AB-1234-XX">
                        <datalist id="vehiculesList">
                            {{-- On pourrait charger une liste ici via AJAX si trop longue --}}
                        </datalist>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Enregistrer les modifications</button>
                    <a href="{{ route('fleet.checking.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
