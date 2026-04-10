<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Connexion - KENAM SERVICES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg mt-5">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Test Connexion
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-info">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </label>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ old('email', 'test@test.com') }}"
                                       required
                                       autocomplete="email"
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Mot de passe
                                </label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       value="{{ old('password', '12345678') }}"
                                       required
                                       autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Se souvenir de moi
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Se connecter
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <h6 class="text-muted mb-3">Comptes de test</h6>
                            <div class="row">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100 mb-2" onclick="fillCredentials('test@test.com', '12345678')">
                                        <i class="fas fa-user me-1"></i>Agent Test
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-info btn-sm w-100 mb-2" onclick="fillCredentials('agent@gmail.com', 'password123')">
                                        <i class="fas fa-user me-1"></i>Agent Gmail
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-warning btn-sm w-100" onclick="fillCredentials('manager.rh@kenam.com', 'password123')">
                                        <i class="fas fa-user-tie me-1"></i>Manager RH
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="fillCredentials('admin@kenamservices.com', 'admin123')">
                                        <i class="fas fa-user-shield me-1"></i>Admin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug Panel -->
                <div class="card mt-3">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-bug me-2"></i>Debug Info
                        </h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <div><strong>URL:</strong> {{ url()->current() }}</div>
                            <div><strong>Method:</strong> {{ request()->method() }}</div>
                            <div><strong>CSRF Token:</strong> {{ substr(csrf_token(), 0, 10) }}...</div>
                            <div><strong>Session ID:</strong> {{ session()->getId() }}</div>
                            <div><strong>Time:</strong> {{ now()->format('H:i:s') }}</div>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;

            // Visual feedback
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Rempli!';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-success', 'btn-outline-info', 'btn-outline-warning', 'btn-outline-danger');

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-success', 'btn-outline-info', 'btn-outline-warning', 'btn-outline-danger');
            }, 1000);
        }

        // Auto-submit for testing
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');

            form.addEventListener('submit', function(e) {
                console.log('Form submission started...');
                console.log('Email:', document.getElementById('email').value);
                console.log('Password:', document.getElementById('password').value);
                console.log('CSRF Token:', document.querySelector('input[name="_token"]').value);
            });

            // Add console log for debugging
            console.log('Login test page loaded');
            console.log('Current URL:', window.location.href);
        });
    </script>
</body>
</html>
