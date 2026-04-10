@extends('layouts.app')

@section('title', 'Nouvelle Commande | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Nouvelle demande (engins)"
    icon="fa-solid fa-clipboard-list"
    createRoute="commercial.commandes.index"
    createText="Retour à la liste"
>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nouvelle demande
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commercial.commandes.store') }}" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label for="email_service" class="form-label">Email service logistique *</label>
                            <input type="email" name="email_service" id="email_service" class="form-control @error('email_service') is-invalid @enderror" value="{{ old('email_service') }}" required>
                            @error('email_service')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="cc_emails" class="form-label">Emails en copie (CC)</label>
                            <input type="text" name="cc_emails" id="cc_emails" class="form-control @error('cc_emails') is-invalid @enderror" value="{{ old('cc_emails') }}" placeholder="ex: a@mail.com, b@mail.com">
                            @error('cc_emails')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="type_engin" class="form-label">Type d'engin *</label>
                            <input type="text" name="type_engin" id="type_engin" class="form-control @error('type_engin') is-invalid @enderror" value="{{ old('type_engin') }}" required>
                            @error('type_engin')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="delai" class="form-label">Délai *</label>
                            <input type="date" name="delai" id="delai" class="form-control @error('delai') is-invalid @enderror" value="{{ old('delai') }}" required>
                            @error('delai')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Description *</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('commercial.commandes.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Retour à la liste
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fa-solid fa-undo me-2"></i>
                                        Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-2"></i>
                                        Enregistrer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-list-layout>
@endsection
