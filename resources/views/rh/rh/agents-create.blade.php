@extends('layouts.app')

@section('title', 'RH - Ajouter un Agent | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus mr-2 text-primary"></i>Ajouter un Nouvel Agent
            </h1>
            <p class="text-muted">Création d'une nouvelle fiche employé dans le système RH</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire d'ajout d'agent -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('rh.agents.store') }}" method="POST">
                @csrf
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-card mr-2"></i>Informations Personnelles
                    </h6>
                </div>
                <div class="card-body">
                    <div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" id="date_naissance">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
                                <input type="text" class="form-control" id="lieu_naissance">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" placeholder="123 Rue de la Paix">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="code_postal" class="form-label">Code postal</label>
                                <input type="text" class="form-control" id="code_postal">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <select class="form-select" id="pays">
                                    <option value="FR">France</option>
                                    <option value="BE">Belgique</option>
                                    <option value="CH">Suisse</option>
                                    <option value="LU">Luxembourg</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_securite_sociale" class="form-label">Numéro de sécurité sociale</label>
                                <input type="text" class="form-control" id="numero_securite_sociale" placeholder="1 23 45 67 890 123 45">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_identite" class="form-label">Numéro d'identité</label>
                                <input type="text" class="form-control" id="numero_identite">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-briefcase mr-2"></i>Informations Professionnelles
                    </h6>
                </div>
                <div class="card-body">
                    <div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service" class="form-label">Service *</label>
                                <select class="form-select" id="service" name="service" required>
                                    <option value="">Sélectionner un service</option>
                                    <option value="Logistique">Logistique</option>
                                    <option value="Entretien">Entretien</option>
                                    <option value="Administration">Administration</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="RH">Ressources Humaines</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="poste" class="form-label">Poste *</label>
                                <input type="text" class="form-control" id="poste" name="poste" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contrat" class="form-label">Type de contrat *</label>
                                <select class="form-select" id="contrat" name="contrat" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="CDI">CDI</option>
                                    <option value="CDD">CDD</option>
                                    <option value="Intérim">Intérim</option>
                                    <option value="Stage">Stage</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_embauche" class="form-label">Date d'embauche *</label>
                                <input type="date" class="form-control" id="date_embauche" name="date_embauche" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salaire" class="form-label">Salaire de base (FCFA) *</label>
                                <input type="number" class="form-control" id="salaire" name="salaire" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select" id="statut" required>
                                    <option value="">Sélectionner un statut</option>
                                    <option value="actif">Actif</option>
                                    <option value="en_essai">En période d'essai</option>
                                    <option value="suspendu">Suspendu</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="manager" class="form-label">Manager / Responsable</label>
                                <select class="form-select" id="manager">
                                    <option value="">Sélectionner un manager</option>
                                    <option value="1">Marie Curie</option>
                                    <option value="2">Jean Dupont</option>
                                    <option value="3">Pierre Martin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="classification" class="form-label">Classification</label>
                                <select class="form-select" id="classification">
                                    <option value="">Sélectionner une classification</option>
                                    <option value="agent">Agent de maîtrise</option>
                                    <option value="cadre">Cadre</option>
                                    <option value="technicien">Technicien</option>
                                    <option value="ouvrier">Ouvrier</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="commentaires" class="form-label">Commentaires</label>
                            <textarea class="form-control" id="commentaires" rows="3" placeholder="Informations complémentaires..."></textarea>
                        </div>
                </div>
            </div>
            <!-- Photo de profil -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-camera mr-2"></i>Photo de profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-upload mb-3">
                        <div class="avatar-preview">
                            <div id="imagePreview" class="avatar-circle bg-light text-muted" style="width: 120px; height: 120px; margin: 0 auto; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="file" id="avatarInput" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-upload mr-1"></i>Choisir une photo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents à fournir -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i>Documents à fournir
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-id-card text-muted mr-2"></i>
                                <span class="small">Carte d'identité</span>
                            </div>
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-file-contract text-muted mr-2"></i>
                                <span class="small">Contrat de travail</span>
                            </div>
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-user-md text-muted mr-2"></i>
                                <span class="small">Visite médicale</span>
                            </div>
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-graduation-cap text-muted mr-2"></i>
                                <span class="small">Diplômes</span>
                            </div>
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-euro-sign text-muted mr-2"></i>
                                <span class="small">RIB</span>
                            </div>
                            <input type="checkbox" class="form-check-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs mr-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>Enregistrer l'agent
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>Enregistrer et continuer
                        </button>
                        <button type="button" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                    </div>

                    <hr class="my-3">

                    <div class="small text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Les champs marqués d'un * sont obligatoires
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles personnalisés -->
<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.form-label {
    font-weight: 600;
    color: #495057;
}
</style>

<!-- Scripts -->
<script>
// Gestionnaire pour l'upload d'avatar
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.style.backgroundImage = `url(${e.target.result})`;
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
            preview.innerHTML = '';
        };
        reader.readAsDataURL(file);
    }
});

// JS spécifique supprimé: la soumission est gérée par Laravel
</script>
@endsection
