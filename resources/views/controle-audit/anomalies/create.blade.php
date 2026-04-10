@extends('layouts.app')

@section('title', 'Nouvelle Anomalie - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus text-primary me-2"></i>Nouvelle Anomalie
            </h1>
            <p class="text-muted mb-0">Créer une nouvelle anomalie d'audit</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informations de l'Anomalie</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('controle-audit.anomalies.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Décrivez l'anomalie..."></textarea>
                                <div class="form-text">Description détaillée de l'anomalie détectée.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cause" class="form-label">Cause</label>
                                <textarea class="form-control" id="cause" name="cause" rows="3" placeholder="Cause de l'anomalie si connue..."></textarea>
                                <div class="form-text">Cause identifiée de l'anomalie.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_anomalie" class="form-label">Type d'Anomalie *</label>
                                <select class="form-select" id="type_anomalie" name="type_anomalie" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="processus">Processus</option>
                                    <option value="documentation">Documentation</option>
                                    <option value="conformite">Conformité</option>
                                    <option value="securite">Sécurité</option>
                                    <option value="qualite">Qualité</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="severite" class="form-label">Sévérité *</label>
                                <select class="form-select" id="severite" name="severite" required>
                                    <option value="">Sélectionner la sévérité</option>
                                    <option value="mineure">Mineure</option>
                                    <option value="majeure">Majeure</option>
                                    <option value="critique">Critique</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_decouverte" class="form-label">Date de Découverte *</label>
                                <input type="date" class="form-control" id="date_decouverte" name="date_decouverte" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsable_id" class="form-label">Responsable</label>
                                <select class="form-select" id="responsable_id" name="responsable_id">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Personne responsable du traitement de l'anomalie.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en_attente">En attente</option>
                                    <option value="en_cours">En cours</option>
                                    <option value="resolue">Résolue</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_resolution" class="form-label">Date de Résolution</label>
                                <input type="date" class="form-control" id="date_resolution" name="date_resolution">
                                <div class="form-text">À remplir si l'anomalie est résolue.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="actions_correctives" class="form-label">Actions Correctives</label>
                            <textarea class="form-control" id="actions_correctives" name="actions_correctives" rows="3" placeholder="Actions mises en place pour corriger l'anomalie..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('controle-audit.anomalies') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer l'Anomalie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
