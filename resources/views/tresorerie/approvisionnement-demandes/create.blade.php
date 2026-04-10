@extends('layouts.app')

@section('title', 'Nouvelle Demande d\'Approvisionnement')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-paper-plane text-primary me-2"></i>
                Nouvelle Demande d'Approvisionnement
            </h4>
            <small class="text-muted">Votre demande sera envoyée au comptable pour validation</small>
        </div>
        <a href="{{ route('tresorerie.approvisionnement-demandes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Workflow info --}}
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-info-circle fa-lg mt-1 text-info"></i>
                    <div>
                        <strong>Comment fonctionne une demande d'approvisionnement ?</strong>
                        <div class="mt-2 d-flex flex-wrap gap-3 align-items-center">
                            <span><i class="fas fa-coins text-success me-1"></i> Vous renseignez le montant sollicité</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                            <span><i class="fas fa-wallet text-primary me-1"></i> Vous choisissez la caisse à approvisionner</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                            <span><i class="fas fa-envelope text-info me-1"></i> La comptabilité reçoit un email</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                            <span><i class="fas fa-share-square text-secondary me-1"></i> La comptabilité envoie un email à la DG</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Formulaire de Demande
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.approvisionnement-demandes.store') }}">
                        @csrf

                        <input type="hidden" name="devise" value="{{ old('devise', 'XOF') }}">

                        {{-- Montant sollicité --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-coins text-success me-1"></i>
                                    Montant sollicité
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="montant"
                                           class="form-control @error('montant') is-invalid @enderror"
                                           min="1"
                                           step="1"
                                           value="{{ old('montant') }}"
                                           placeholder="Ex: 250000"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Caisse à approvisionner --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-wallet text-primary me-1"></i>
                                    Caisse à approvisionner
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="caisse_destination_id" class="form-select @error('caisse_destination_id') is-invalid @enderror" required>
                                    <option value="">— Sélectionner la caisse à approvisionner —</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_destination_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_destination_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Raison --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left text-info me-1"></i>
                                Raison / Justification <span class="text-danger">*</span>
                            </label>
                            <textarea name="raison"
                                      class="form-control @error('raison') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Expliquez le motif de cet approvisionnement (achat urgence, règlement fournisseur, frais opérationnels...)"
                                      required>{{ old('raison') }}</textarea>
                            @error('raison')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Canal d'alerte --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-bell text-warning me-1"></i>
                                Canal d'alerte
                            </label>
                            <select name="alert_channel" class="form-select @error('alert_channel') is-invalid @enderror">
                                <option value="sms" {{ old('alert_channel', 'sms') === 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="whatsapp" {{ old('alert_channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp Web JS</option>
                            </select>
                            <small class="text-muted">
                                Le numéro du user connecté est inclus dans les destinataires d'alerte.
                            </small>
                            @error('alert_channel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tresorerie.approvisionnement-demandes.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Envoyer la Demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
