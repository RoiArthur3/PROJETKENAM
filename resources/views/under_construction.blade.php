@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body" style="padding:2rem;">
            <h2 style="color:#166534; font-weight:800; margin-bottom:0.5rem;">@yield('page_title', $title ?? 'En construction')</h2>
            <p style="color:#475569; margin-bottom:1rem;">Cette page est en cours de réalisation.</p>
            <div style="display:flex; align-items:center; gap:.75rem; color:#16a34a;">
                <i class="fas fa-tools"></i>
                <span>Merci de revenir bientôt. Toutes les fonctionnalités seront disponibles progressivement.</span>
            </div>
        </div>
    </div>
</div>
@endsection
