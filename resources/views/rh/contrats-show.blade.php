@extends('layouts.app')

@section('title', 'Détails du Contrat - ' . $personnel->nom . ' ' . $personnel->prenoms)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dossier de Contrat : {{ $personnel->nom }} {{ $personnel->prenoms }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.contrats.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('personnel.contrat-pdf', $personnel->id) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Imprimer le Contrat (PDF)
            </a>
            <a href="{{ route('personnel.edit', $personnel->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <!-- État Civil -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">État Civil & Contact</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center border" style="width: 100px; height: 100px;">
                            @if($personnel->photo_profil)
                                <img src="{{ asset('storage/' . $personnel->photo_profil) }}" alt="Photo" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <i class="fas fa-user fa-3x text-muted"></i>
                            @endif
                        </div>
                        <h5 class="mt-3 mb-0">{{ $personnel->nom }} {{ $personnel->prenoms }}</h5>
                        <p class="text-muted">{{ $personnel->poste }}</p>
                    </div>
                    <hr>
                    <p><strong>Matricule :</strong> {{ $personnel->matricule }}</p>
                    <p><strong>Sexe :</strong> {{ $personnel->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                    <p><strong>Né(e) le :</strong> {{ \Carbon\Carbon::parse($personnel->date_naissance)->format('d/m/Y') }} à {{ $personnel->lieu_naissance }}</p>
                    <p><strong>Nationalité :</strong> {{ $personnel->nationalite }}</p>
                    <p><strong>Situation :</strong> {{ $personnel->situation_matrimoniale }}</p>
                    <p><strong>Enfants :</strong> {{ $personnel->nb_enfants_charge }}</p>
                    <hr>
                    <p><strong>Email :</strong> {{ $personnel->email_personnel ?? 'Non renseigné' }}</p>
                    <p><strong>Téléphone :</strong> {{ $personnel->telephone_principal }}</p>
                    <p><strong>Adresse :</strong> {{ $personnel->adresse_residence }}</p>
                </div>
            </div>
        </div>

        <!-- Détails du Contrat -->
        <div class="col-md-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Conditions du Contrat (Code Travail CI)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type de Contrat :</strong> <span class="badge bg-info px-3">{{ $personnel->type_contrat }}</span></p>
                            <p><strong>Date d'Embauche :</strong> {{ \Carbon\Carbon::parse($personnel->date_embauche)->format('d/m/Y') }}</p>
                            <p><strong>Service :</strong> {{ $personnel->service }}</p>
                            <p><strong>Catégorie :</strong> {{ $personnel->categorie }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Période d'essai :</strong> {{ $personnel->duree_essai_jours }} jours</p>
                            <p><strong>Date Fin Prévue :</strong> {{ $personnel->date_fin_contrat ? \Carbon\Carbon::parse($personnel->date_fin_contrat)->format('d/m/Y') : 'Contrat Permanent' }}</p>
                            <p><strong>ID CNPS :</strong> {{ $personnel->numero_cnps ?? 'EN ATTENTE' }}</p>
                            <p><strong>N° Contribuable :</strong> {{ $personnel->numero_contribuable ?? 'EN ATTENTE' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h6 class="m-0 font-weight-bold">Rémunération Mensuelle Globale</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Rubrique</th>
                                <th class="text-end">Montant ({{ $personnel->devise ?? 'FCFA' }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Salaire de Base</td>
                                <td class="text-end">{{ number_format($personnel->salaire_base, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td>Salaire Horaire</td>
                                <td class="text-end">
                                    <span class="badge bg-info">
                                        {{ number_format($personnel->salaire_horaire ?? ($personnel->salaire_base / 173.33), 2, ',', ' ') }} {{ $personnel->devise ?? 'FCFA' }}/h
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Indemnité de Transport (Exonéré)</td>
                                <td class="text-end">{{ number_format($personnel->salaire_base * 0.1, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td>Indemnité de Logement</td>
                                <td class="text-end">{{ number_format($personnel->salaire_base * 0.15, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td>Autres Primes / Gratifications</td>
                                <td class="text-end">0</td>
                            </tr>
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-info">
                                <td>SALAIRE BRUT TOTAL</td>
                                <td class="text-end">{{ number_format($personnel->salaire_base * 1.25, 0, ',', ' ') }} {{ $personnel->devise ?? 'FCFA' }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="alert alert-light border small mt-3">
                        <i class="fas fa-info-circle me-1"></i> Ce montant correspond au salaire brut total avant déductions sociales (CNPS, IS, IGR, CMU). Les charges sociales et fiscales seront calculées lors de l'édition du bulletin de paie.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
