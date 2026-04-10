@extends('layouts.app')

@section('title', 'Test Sync Caméra')

@section('content')
<div class="container-fluid py-4">
    <h1>Test - Synchronisation Caméra</h1>
    <p>Si vous voyez cette page, la route et le contrôleur fonctionnent.</p>
    
    <div class="alert alert-info">
        <strong>Debug info:</strong><br>
        Route: {{ route('rh.camera-sync.index') }}<br>
        Controller: RH\CameraSyncController<br>
        Method: index
    </div>
    
    <a href="{{ route('rh.camera-sync.index') }}" class="btn btn-primary">Retour à l'index</a>
</div>
@endsection
