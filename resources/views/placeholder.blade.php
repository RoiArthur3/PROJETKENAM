@extends('layouts.app')

@section('title', $title ?? 'Page en construction')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title ?? 'Page en construction' }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <i class="fas fa-person-digging text-muted me-3" style="font-size: 2rem"></i>
                <p class="text-muted mb-0">Cette page n'est pas encore implémentée. Elle s'affichera prochainement.
                    Vous pouvez néanmoins naviguer dans les autres sections déjà disponibles.</p>
            </div>
        </div>
    </div>
</div>
@endsection
