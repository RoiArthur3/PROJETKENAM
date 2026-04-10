@extends('layouts.app')

@section('title', 'Connexion | KENAM SERVICES')

@section('content')
<div class="container-fluid vh-100">
    <div class="row h-100">
        <div class="col-lg-6 d-none d-lg-block">
            <div class="h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, var(--kenam-green) 0%, var(--kenam-green-dark) 100%);">
                <div class="text-center text-white p-5">
                    <div class="mb-4">
                        <i class="fas fa-building fa-4x"></i>
                    </div>
                    <h2 class="mb-3">KENAM SERVICES</h2>
                    <p class="lead mb-4">Plateforme de gestion logistique</p>
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-truck fa-2x mb-2"></i>
                            <p class="small mb-0">Logistique</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p class="small mb-0">Analyse</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-cogs fa-2x mb-2"></i>
                            <p class="small mb-0">Opérations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="h-100 d-flex align-items-center justify-content-center">
                <div class="w-100" style="max-width: 400px;">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h3 class="mb-2">Connexion</h3>
                                <p class="text-muted">Accédez à votre espace de travail</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Toggle Email/Téléphone -->
                                <div class="mb-3">
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="loginType" id="loginTypePhone" value="phone" checked>
                                        <label class="btn btn-outline-primary" for="loginTypePhone">
                                            <i class="fas fa-phone me-1"></i>Téléphone
                                        </label>

                                        <input type="radio" class="btn-check" name="loginType" id="loginTypeEmail" value="email">
                                        <label class="btn btn-outline-primary" for="loginTypeEmail">
                                            <i class="fas fa-envelope me-1"></i>Email
                                        </label>
                                    </div>
                                </div>

                                <!-- Champ Email/Téléphone -->
                                <div class="mb-3">
                                    <label for="login" class="form-label">
                                        <span id="loginLabel">Téléphone</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="loginIcon">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control"
                                               id="login"
                                               name="telephone"
                                               value="{{ old('telephone') }}"
                                               placeholder="Entrez votre numéro de téléphone"
                                               required>
                                    </div>
                                </div>

                                <!-- Champ Mot de passe -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password"
                                               class="form-control"
                                               id="password"
                                               name="password"
                                               placeholder="Entrez votre mot de passe"
                                               required>
                                    </div>
                                </div>

                                <!-- Se souvenir de moi -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                </div>

                                <!-- Bouton de connexion -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Se connecter
                                    </button>
                                </div>
                            </form>

                            <!-- Informations de connexion -->
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <strong>Test Modérateur:</strong><br>
                                    Email: moderateur@demo.com<br>
                                    Téléphone: 0755551133<br>
                                    Mot de passe: password123
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.h-100 {
    height: 100vh;
}

.btn-check:checked + .btn {
    background-color: var(--kenam-green);
    border-color: var(--kenam-green);
    color: white;
}

.btn-check:checked + .btn:hover {
    background-color: var(--kenam-green-dark);
    border-color: var(--kenam-green-dark);
}

.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.input-group-text {
    background-color: var(--gray-100);
    border: 1px solid var(--gray-300);
    color: var(--gray-600);
}

.form-control:focus {
    border-color: var(--kenam-green);
    box-shadow: 0 0 0 0.2rem rgba(22, 163, 74, 0.25);
}

.btn-primary {
    background-color: var(--kenam-green);
    border-color: var(--kenam-green);
}

.btn-primary:hover {
    background-color: var(--kenam-green-dark);
    border-color: var(--kenam-green-dark);
}

.btn-outline-primary {
    color: var(--kenam-green);
    border-color: var(--kenam-green);
}

.btn-outline-primary:hover {
    background-color: var(--kenam-green);
    border-color: var(--kenam-green);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginTypeEmail = document.getElementById('loginTypeEmail');
    const loginTypePhone = document.getElementById('loginTypePhone');
    const loginInput = document.getElementById('login');
    const loginLabel = document.getElementById('loginLabel');
    const loginIcon = document.getElementById('loginIcon');

    function updateLoginField(type) {
        if (type === 'email') {
            loginInput.type = 'email';
            loginInput.placeholder = 'Entrez votre email';
            loginLabel.textContent = 'Email';
            loginIcon.innerHTML = '<i class="fas fa-envelope"></i>';
        } else {
            loginInput.type = 'tel';
            loginInput.placeholder = 'Entrez votre numéro de téléphone';
            loginLabel.textContent = 'Téléphone';
            loginIcon.innerHTML = '<i class="fas fa-phone"></i>';
        }
    }

    loginTypeEmail.addEventListener('change', function() {
        if (this.checked) {
            updateLoginField('email');
        }
    });

    loginTypePhone.addEventListener('change', function() {
        if (this.checked) {
            updateLoginField('phone');
        }
    });

    // Formatage du numéro de téléphone
    loginInput.addEventListener('input', function(e) {
        if (loginTypePhone.checked) {
            // Supprimer tous les caractères non numériques
            let value = e.target.value.replace(/[^0-9]/g, '');

            // Limiter à 10 caractères
            if (value.length > 10) {
                value = value.substring(0, 10);
            }

            e.target.value = value;
        }
    });
});
</script>
@endsection
