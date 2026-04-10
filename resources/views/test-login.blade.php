<!DOCTYPE html>
<html>
<head>
    <title>Test Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Test de Connexion</h3>
                    </div>
                    <div class="card-body">
                        <form id="loginForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="admin@kenamservices.net" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-control" value="admin123" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                        </form>
                        <div id="result" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/test-login-submit', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                }
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('result');
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <h5>✅ Connexion réussie!</h5>
                            <p><strong>Utilisateur:</strong> ${data.user.name}</p>
                            <p><strong>Email:</strong> ${data.user.email}</p>
                            <p><strong>Rôle:</strong> ${data.user.role}</p>
                            <p><strong>Redirection vers:</strong> ${data.redirect}</p>
                            <a href="${data.redirect}" class="btn btn-success">Aller au dashboard</a>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>❌ Échec de connexion</h5>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('result').innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Erreur technique</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>
