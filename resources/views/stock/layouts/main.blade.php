@extends('layouts.app')

@section('title', 'Gestion Stock | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-boxes mr-2"></i>@yield('module-title')
            </h1>
            <p class="text-muted">@yield('module-description')</p>
        </div>
        <div class="col-auto">
            @yield('module-actions')
        </div>
    </div>

    @yield('module-content')
</div>
@endsection
