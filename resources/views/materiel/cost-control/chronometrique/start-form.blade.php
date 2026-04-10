@extends('layouts.app')

@section('title', 'Démarrer un Pointage | KENAM SERVICES')

@section('content')
<div class="container mt-4">
    {{-- ── En-tête ────────────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-play-circle me-2 text-success"></i>Démarrer un Pointage
            </h1>
            <p class="text-muted mb-0">Saisissez les informations et cliquez sur "DÉMARRER"</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('materiel.cost-control.chrono.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="startPointageForm" class="card shadow-sm">
                @csrf

                {{-- Véhicule & Chauffeur --}}
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Informations du pointage
                    </h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="vehicle_id" class="form-label">🚗 Véhicule <span class="text-danger">*</span></label>
                            <select id="vehicle_id" name="vehicle_id" class="form-select form-select-lg" required>
                                <option value="">-- Sélectionner un véhicule --</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un véhicule</div>
                        </div>
                        <div class="col-md-6">
                            <label for="driver_id" class="form-label">👤 Chauffeur <span class="text-danger">*</span></label>
                            <select id="driver_id" name="driver_id" class="form-select form-select-lg" required>
                                <option value="">-- Sélectionner un chauffeur --</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">
                                    {{ $driver->nom }} {{ $driver->prenoms }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un chauffeur</div>
                        </div>
                    </div>

                    {{-- Tâche & Localisation --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="task_label" class="form-label">📝 Description de la tâche <span class="text-danger">*</span></label>
                            <input type="text" id="task_label" name="task_label" class="form-control form-control-lg"
                                   placeholder="Ex: Transport de marchandises, Livraison, Chantier..." required>
                            <small class="text-muted">Décrivez le travail à effectuer</small>
                            <div class="invalid-feedback">Veuillez entrer une description</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="departure_location" class="form-label">📍 Lieu de départ</label>
                            <input type="text" id="departure_location" name="departure_location" class="form-control"
                                   placeholder="Ex: Agence centrale, Chantier A...">
                        </div>
                        <div class="col-md-6">
                            <label for="submodule" class="form-label">🏢 Module <span class="text-danger">*</span></label>
                            <select id="submodule" name="submodule" class="form-select" required>
                                <option value="camion_plateau">Camion Plateau</option>
                                <option value="engin">Engin Standard</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Tarification --}}
                <div class="card-body border-top">
                    <h5 class="card-title">
                        <i class="fas fa-dollar-sign me-2 text-success"></i>Coûts horaires
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="supplier_unit_cost" class="form-label">💰 Coût Fournisseur/heure <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="number" id="supplier_unit_cost" name="supplier_unit_cost"
                                       class="form-control" placeholder="0,00" step="0.01" min="0" required>
                                <span class="input-group-text">FCFA/h</span>
                            </div>
                            <small class="text-muted">Ce que vous payez au fournisseur/chauffeur</small>
                            <div class="invalid-feedback">Veuillez entrer le coût</div>
                        </div>
                        <div class="col-md-6">
                            <label for="client_unit_price" class="form-label">🎯 Prix Client/heure <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="number" id="client_unit_price" name="client_unit_price"
                                       class="form-control" placeholder="0,00" step="0.01" min="0" required>
                                <span class="input-group-text">FCFA/h</span>
                            </div>
                            <small class="text-muted">Ce que vous facturez au client</small>
                            <div class="invalid-feedback">Veuillez entrer le prix</div>
                        </div>
                    </div>

                    {{-- Affichage de la marge prévue --}}
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">Marge estimée/heure :</small>
                        <h5 class="text-success mb-0" id="estimated_margin">0 FCFA</h5>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="card-footer bg-light d-flex gap-2 justify-content-between">
                    <a href="{{ route('materiel.cost-control.plateau.chrono.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" id="startBtn" class="btn btn-success btn-lg">
                        <i class="fas fa-play-circle me-2"></i>DÉMARRER LE CHRONO
                    </button>
                </div>
            </form>
        </div>

        {{-- Aide utilisation --}}
        <div class="col-lg-4">
            <div class="card border-info shadow-sm">
                <div class="card-header bg-info bg-gradient text-white">
                    <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Comment ça marche ?</h6>
                </div>
                <div class="card-body">
                    <ol class="small">
                        <li class="mb-2">
                            <strong>Remplissez le formulaire</strong> avec les informations du pointage
                        </li>
                        <li class="mb-2">
                            <strong>Cliquez sur "DÉMARRER"</strong> pour lancer le chronomètre
                        </li>
                        <li class="mb-2">
                            <strong>Le pointage démarre immédiatement</strong> et vous serez redirigé au dashboard
                        </li>
                        <li class="mb-2">
                            <strong>Au Dashboard</strong>, vous verrez le pointage en cours avec le chrono en temps réel
                        </li>
                        <li class="mb-2">
                            <strong>Cliquez sur "STOP"</strong> pour arrêter et calculer les heures
                        </li>
                        <li>
                            <strong>Les heures sont calculées automatiquement</strong> et arrondies
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card border-success shadow-sm mt-3">
                <div class="card-header bg-success bg-gradient text-white">
                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Calcul automatique</h6>
                </div>
                <div class="card-body text-small">
                    <p class="mb-2">
                        <strong>Prix/heure Client</strong> - <strong>Coût/heure Fournisseur</strong> = <strong>Marge/heure</strong>
                    </p>
                    <p class="text-muted mb-0">
                        Cette marge est multiplée par le nombre d'heures écoulées pour obtenir la marge totale du pointage.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('startPointageForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('startBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Démarrage...';

    try {
        const response = await fetch('{{ route("materiel.cost-control.plateau.chrono.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                vehicle_id: document.getElementById('vehicle_id').value,
                driver_id: document.getElementById('driver_id').value,
                vehicle_mission_id: null,
                task_label: document.getElementById('task_label').value,
                departure_location: document.getElementById('departure_location').value,
                supplier_unit_cost: document.getElementById('supplier_unit_cost').value,
                client_unit_price: document.getElementById('client_unit_price').value,
                submodule: document.getElementById('submodule').value,
            })
        });

        const data = await response.json();

        if (data.success) {
            // Toast notification
            showToast('✓ Pointage démarré avec succès !', 'success');
            
            // Redirection au dashboard après 1.5s
            setTimeout(() => {
                window.location.href = '{{ route("materiel.cost-control.plateau.chrono.dashboard") }}';
            }, 1500);
        } else {
            showToast('✗ Erreur: ' + (data.error || 'Impossible de démarrer'), 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('✗ Erreur réseau', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Calcul de la marge estimée
document.getElementById('supplier_unit_cost').addEventListener('change', updateMargin);
document.getElementById('client_unit_price').addEventListener('change', updateMargin);

function updateMargin() {
    const supplier = parseFloat(document.getElementById('supplier_unit_cost').value) || 0;
    const client = parseFloat(document.getElementById('client_unit_price').value) || 0;
    const margin = client - supplier;
    
    const marginEl = document.getElementById('estimated_margin');
    marginEl.textContent = margin.toLocaleString('fr-FR', { style: 'currency', currency: 'XOF' });
    marginEl.className = margin >= 0 ? 'text-success' : 'text-danger';
    marginEl.classList.add('mb-0');
}

function showToast(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>
@endsection
