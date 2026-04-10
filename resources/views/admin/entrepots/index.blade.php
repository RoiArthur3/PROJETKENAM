@extends('layouts.app')
@section('title', 'Gestion des Entrepôts')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Entrepôts</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.stock.entrepots.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nouvel Entrepôt
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Module Entrepôts - Interface d'administration</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
