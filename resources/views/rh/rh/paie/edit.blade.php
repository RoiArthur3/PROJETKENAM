@extends('layouts.app')

@section('title', 'Modifier la Paie - ' . ($paie->user->name ?? 'Agent') . ' - RH')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-warning me-2"></i>Modifier la Paie
                    </h1>
                    <p class="text-muted mb-0">
                        <strong>{{ $paie->user->name ?? 'Agent' }}</strong> - Période: {{ $paie->mois }}/{{ $paie->annee }}
                    </p>
                </div>
                <a href="{{ route('rh.paie.index', ['mois' => $paie->annee . '-' . str_pad($paie->mois, 2, '0', STR_PAD_LEFT)]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour aux paies
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Modification Manuelle des Montants
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.paie.update', $paie) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Salaire de base (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg"
                                           name="salaire_base" value="{{ old('salaire_base', $paie->salaire_base) }}"
                                           min="0" step="1000" required>
                                    @error('salaire_base')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Total Brut (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg"
                                           name="brut" value="{{ old('brut', $paie->brut) }}"
                                           min="0" step="1000" required>
                                    @error('brut')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">CNPS Salariale (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg"
                                           name="cnps_salariale" value="{{ old('cnps_salariale', $paie->cnps_salariale) }}"
                                           min="0" step="100" required>
                                    @error('cnps_salariale')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Autres retenues (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg"
                                           name="autres_retenues" value="{{ old('autres_retenues', $paie->autres_retenues) }}"
                                           min="0" step="100" required>
                                    @error('autres_retenues')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success">Net à payer (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg border-success"
                                           name="net_a_payer" value="{{ old('net_a_payer', $paie->net_a_payer) }}"
                                           min="0" step="100" required>
                                    @error('net_a_payer')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Informations</h6>
                                    <ul class="mb-0 small">
                                        <li>Modifiez manuellement les montants selon les besoins spécifiques</li>
                                        <li>Le net à payer est le montant final versé à l'agent</li>
                                        <li>Tous les champs sont obligatoires</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('rh.paie.index', ['mois' => $paie->annee . '-' . str_pad($paie->mois, 2, '0', STR_PAD_LEFT)]) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations de l'agent -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informations de l'Agent
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h6 class="mb-1">{{ $paie->user->name ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $paie->user->email ?? '' }}</small>
                    </div>

                    <hr>

                    <dl class="row small">
                        <dt class="col-sm-5">Service:</dt>
                        <dd class="col-sm-7">{{ $paie->user->service->nom ?? 'Non assigné' }}</dd>

                        <dt class="col-sm-5">Statut:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-{{ $paie->user->actif ? 'success' : 'secondary' }}">
                                {{ $paie->user->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </dd>

                        <dt class="col-sm-5">Rôle:</dt>
                        <dd class="col-sm-7">{{ ucfirst($paie->user->role ?? 'N/A') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Résumé des montants actuels -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Résumé des Montants
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 mb-0 text-primary">{{ number_format($paie->brut, 0, ',', ' ') }}</div>
                            <small class="text-muted">Brut</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 text-success">{{ number_format($paie->net_a_payer, 0, ',', ' ') }}</div>
                            <small class="text-muted">Net</small>
                        </div>
                    </div>

                    <hr>

                    <div class="small">
                        <div class="d-flex justify-content-between">
                            <span>Charges sociales:</span>
                            <span>{{ number_format($paie->cnps_salariale, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Autres retenues:</span>
                            <span>{{ number_format($paie->autres_retenues, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Net à payer:</span>
                            <span class="text-success">{{ number_format($paie->net_a_payer, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calcul automatique du net à payer
document.addEventListener('DOMContentLoaded', function() {
    const brutInput = document.querySelector('[name="brut"]');
    const cnpsInput = document.querySelector('[name="cnps_salariale"]');
    const autresInput = document.querySelector('[name="autres_retenues"]');
    const netInput = document.querySelector('[name="net_a_payer"]');

    function calculateNet() {
        const brut = parseFloat(brutInput.value) || 0;
        const cnps = parseFloat(cnpsInput.value) || 0;
        const autres = parseFloat(autresInput.value) || 0;
        const net = Math.max(0, brut - cnps - autres);
        netInput.value = net;
    }

    brutInput.addEventListener('input', calculateNet);
    cnpsInput.addEventListener('input', calculateNet);
    autresInput.addEventListener('input', calculateNet);
});
</script>
@endsection
