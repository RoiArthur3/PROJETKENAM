@extends('layouts.app')
@section('title', 'Gestion des Fournisseurs')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Fournisseurs</h3>
                    <div class="card-tools">
                        <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nouveau Fournisseur
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Module Fournisseurs - Interface d'administration</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
