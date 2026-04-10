@extends('layouts.app')

@section('title', 'Test Permissions - {{ $user->name }}')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>
                        Test des Permissions - {{ $user->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informations Utilisateur</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nom:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rôle:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'info' }}">
                                            {{ $user->getRoleDisplayName() }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Module Principal:</strong></td>
                                    <td>
                                        @if($primaryModule)
                                            <span class="badge bg-primary">{{ $primaryModule }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Permissions de Modules</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>Accès</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Dashboard</td>
                                            <td>
                                                @if($user->canAccessModule('dashboard'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Opérations</td>
                                            <td>
                                                @if($user->canAccessModule('operations'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Parc Auto</td>
                                            <td>
                                                @if($user->canAccessModule('fleet'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>RH</td>
                                            <td>
                                                @if($user->canAccessModule('hr'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fournisseurs</td>
                                            <td>
                                                @if($user->canAccessModule('suppliers'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Stock</td>
                                            <td>
                                                @if($user->canAccessModule('warehouse'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Comptabilité</td>
                                            <td>
                                                @if($user->canAccessModule('accounting'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Facturation</td>
                                            <td>
                                                @if($user->canAccessModule('invoicing'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reporting</td>
                                            <td>
                                                @if($user->canAccessModule('reporting'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Commercial</td>
                                            <td>
                                                @if($user->canAccessModule('commercial'))
                                                    <span class="badge bg-success">✓ Oui</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Modules Accessibles</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($accessibleModules as $module => $name)
                                    <a href="{{ $this->getModuleRoute($module) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-{{ $this->getModuleIcon($module) }} me-1"></i>
                                        {{ $name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Test de Navigation</h6>
                                <p class="mb-2">
                                    Pour tester les permissions de cet utilisateur:
                                </p>
                                <ol class="mb-0">
                                    <li>Utilisez le bouton "Connexion" pour vous connecter avec ce compte</li>
                                    <li>Vérifiez que seul les modules autorisés apparaissent dans le menu</li>
                                    <li>Essayez d'accéder directement à des URLs non autorisées</li>
                                    <li>Confirmez que vous recevez une erreur 403 pour les accès non autorisés</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('test.permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                            <a href="{{ route('login') }}?test_user={{ $user->id }}" class="btn btn-success ms-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Tester la connexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
private function getModuleRoute($module)
{
    $routes = [
        'dashboard' => route('dashboard'),
        'operations' => route('operations.index'),
        'fleet' => route('parc.dashboard'),
        'hr' => route('rh.dashboard'),
        'suppliers' => route('fournisseurs.dashboard'),
        'warehouse' => route('stock.dashboard'),
        'reporting' => route('reporting.dashboard'),
        'commercial' => route('commercial.dashboard'),
    ];

    return $routes[$module] ?? '#';
}

private function getModuleIcon($module)
{
    $icons = [
        'dashboard' => 'tachometer-alt',
        'operations' => 'cogs',
        'fleet' => 'truck',
        'hr' => 'users',
        'suppliers' => 'handshake',
        'warehouse' => 'warehouse',
        'reporting' => 'chart-bar',
        'commercial' => 'briefcase',
    ];

    return $icons[$module] ?? 'circle';
}
@endphp
