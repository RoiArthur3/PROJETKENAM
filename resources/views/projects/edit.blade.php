@extends('layouts.app')

@section('title', 'Modifier Estimation de Cout')

@section('content')
<div class="project-edit-page">
    <div class="card border-0 shadow-sm mb-4 hero-edit">
        <div class="card-body p-4 p-lg-5 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="text-uppercase small fw-bold text-primary mb-2">Edition Estimation de Cout</p>
                <h1 class="h3 mb-1">Modifier l'estimation</h1>
                <p class="text-muted mb-0">{{ $project->nom }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('projets.show', $project->id) }}" class="btn btn-outline-secondary">Retour au detail</a>
            </div>
        </div>
    </div>

    <form action="{{ route('projets.update', $project->id) }}" method="POST" class="row g-4">
        @csrf
        @method('PUT')

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">Informations generales</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'estimation <span class="text-danger">*</span></label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom', $project->nom) }}" class="form-control @error('nom') is-invalid @enderror" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Type d'estimation <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @selected(old('type', $project->type) === $type)>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="budget_estime" class="form-label">Cout estimé prévisionnel (FCFA)</label>
                            <input type="number" step="0.01" id="budget_estime" name="budget_estime" value="{{ old('budget_estime', $project->budget_estime) }}" class="form-control @error('budget_estime') is-invalid @enderror">
                            @error('budget_estime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $project->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">Planification</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label">Date de debut <span class="text-danger">*</span></label>
                            <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', $project->date_debut ? $project->date_debut->format('Y-m-d') : '') }}" class="form-control @error('date_debut') is-invalid @enderror" required>
                            @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_fin_prevue" class="form-label">Date de fin prevue <span class="text-danger">*</span></label>
                            <input type="date" id="date_fin_prevue" name="date_fin_prevue" value="{{ old('date_fin_prevue', $project->date_fin_prevue ? $project->date_fin_prevue->format('Y-m-d') : '') }}" class="form-control @error('date_fin_prevue') is-invalid @enderror" required>
                            @error('date_fin_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">Notes</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <label for="notes" class="form-label">Observations complementaires</label>
                    <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $project->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-panel">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">Affectations</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select id="client_id" name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                            <option value="">-- Selectionner un client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected((string) old('client_id', $project->client_id) === (string) $client->id)>{{ $client->nom }}</option>
                            @endforeach
                        </select>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="responsable_search" class="form-label">Responsable (Employé RH)</label>
                        <div class="position-relative">
                            <input 
                                type="text" 
                                id="responsable_search" 
                                class="form-control @error('responsable_id') is-invalid @enderror"
                                placeholder="Taper le nom de l'employé..."
                                autocomplete="off">
                            <input type="hidden" id="responsable_id" name="responsable_id" value="{{ old('responsable_id', $project->responsable_id) }}">
                            
                            <!-- Liste des suggestions -->
                            <ul id="responsable_suggestions" class="list-group position-absolute w-100 mt-1" style="display: none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                            </ul>
                        </div>
                        @error('responsable_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="project-summary mb-3">
                        <div class="summary-item">
                            <span class="text-muted">Statut actuel</span>
                            <strong>{{ ucfirst($project->statut) }}</strong>
                        </div>
                        <div class="summary-item">
                            <span class="text-muted">Avancement</span>
                            <strong>{{ $project->pourcentage_avancement }}%</strong>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Enregistrer les modifications</button>
                        <a href="{{ route('projets.show', $project->id) }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.project-edit-page {
    padding: 1rem;
}
.hero-edit {
    background: linear-gradient(135deg, #f7fbff 0%, #f2f9f4 100%);
}
.sticky-panel {
    position: sticky;
    top: 1rem;
}
.project-summary {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 0.8rem;
    background: #fbfdff;
}
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.35rem 0;
}
.summary-item + .summary-item {
    border-top: 1px dashed #e4e7eb;
}

/* Autocomplete suggestions */
#responsable_suggestions .list-group-item {
    padding: 0.6rem 0.8rem;
    border: none;
    border-bottom: 1px solid #e9ecef;
}
#responsable_suggestions .list-group-item:last-child {
    border-bottom: none;
}
#responsable_suggestions .list-group-item:hover {
    background-color: #f8f9fa;
}

@media (max-width: 991.98px) {
    .sticky-panel {
        position: static;
        top: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('responsable_search');
    const hiddenInput = document.getElementById('responsable_id');
    const suggestionsList = document.getElementById('responsable_suggestions');
    let selectedName = '';

    // If there's a pre-selected value, fetch and display the name
    if (hiddenInput.value) {
        fetch(`/api/employees/${hiddenInput.value}`)
            .then(r => r.json())
            .then(data => {
                if (data.name) {
                    searchInput.value = data.name;
                    selectedName = data.name;
                }
            })
            .catch(err => console.error('Error fetching employee:', err));
    }

    // Debounce function for search
    let debounceTimer;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();

        if (query.length < 2) {
            suggestionsList.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/employees/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = '';

                    if (data.length === 0) {
                        suggestionsList.innerHTML = '<li class="list-group-item text-muted small">Aucun employé trouvé</li>';
                    } else {
                        data.forEach(employee => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item cursor-pointer';
                            li.style.cursor = 'pointer';
                            li.innerHTML = `<strong>${employee.name || employee.nom}</strong><br><small class="text-muted">${employee.role || 'RH'}</small>`;
                            
                            li.addEventListener('click', () => {
                                searchInput.value = employee.name || employee.nom;
                                hiddenInput.value = employee.id;
                                selectedName = employee.name || employee.nom;
                                suggestionsList.style.display = 'none';
                            });

                            suggestionsList.appendChild(li);
                        });
                    }

                    suggestionsList.style.display = 'block';
                })
                .catch(err => {
                    console.error('Search error:', err);
                    suggestionsList.innerHTML = '<li class="list-group-item text-danger small">Erreur de recherche</li>';
                    suggestionsList.style.display = 'block';
                });
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput && e.target !== suggestionsList) {
            suggestionsList.style.display = 'none';
        }
    });

    // Show suggestions on focus
    searchInput.addEventListener('focus', function() {
        if (suggestionsList.innerHTML && this.value.length >= 2) {
            suggestionsList.style.display = 'block';
        }
    });
});
</script>
@endsection