@extends('layouts.app')
@section('title', 'Contrats - Juridique')
@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Contrats</h1>
        <a href="{{ route('juridique.contrats.create') }}" class="btn btn-primary">Nouveau contrat</a>
    </div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Référence</th><th>Titre</th><th>Type</th><th>Partie</th><th>Montant</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($contrats as $contrat)
                    <tr>
                        <td>{{ $contrat->reference }}</td>
                        <td>{{ $contrat->titre }}</td>
                        <td>{{ $contrat->type_contrat }}</td>
                        <td>{{ $contrat->partie_contractante }}</td>
                        <td>{{ number_format($contrat->montant ?? 0, 0, ',', ' ') }} {{ $contrat->devise }}</td>
                        <td>{{ $contrat->statut }}</td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('juridique.contrats.show', $contrat) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                            <a href="{{ route('juridique.contrats.edit', $contrat) }}" class="btn btn-sm btn-outline-warning">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Aucun contrat disponible</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $contrats->links() }}
    </div></div>
</div>
@endsection
