@extends('layouts.app')

@section('title', 'Structure de Connexion KENAM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search me-2"></i>
                        Structure de Connexion - Comment le Système Reconnaît les Utilisateurs
                    </h3>
                </div>
                <div class="card-body">
                    
                    <!-- Étape 1: Formulaire -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i>Étape 1: Formulaire de Connexion</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Champ du formulaire:</strong> <code>name="telephone"</code>
                                        <br><strong>Type:</strong> <code>type="tel"</code>
                                        <br><strong>Pattern:</strong> <code>[0-9]{10}</code>
                                        <br><strong>Placeholder:</strong> <code>0554419341</code>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>JavaScript:</strong> Nettoyage automatique
                                        <br>- Supprime les caractères non numériques
                                        <br>- Limite à 10 chiffres
                                        <br>- Envoie le format nettoyé
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2: Controller -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-cogs me-2"></i>Étape 2: LoginController</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Recherche dans:</strong> <code>table.users.champ.phone</code>
                                        <br><strong>Méthode:</strong> <code>User::whereIn('phone', $variations)</code>
                                        <br><strong>Validation:</strong> Hash::check($password, $user->password)
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Variations testées:</strong>
                                        <ul class="mb-0">
                                            <li><code>0100000000</code> (format direct)</li>
                                            <li><code>225100000000</code> (avec 225)</li>
                                            <li><code>+225100000000</code> (avec +225)</li>
                                            <li><code>00225100000000</code> (avec 00225)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3: Base de Données -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h5><i class="fas fa-database me-2"></i>Étape 3: Base de Données</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Table:</strong> <code>users</code>
                                        <br><strong>Champ clé:</strong> <code>phone</code>
                                        <br><strong>Format stocké:</strong> 10 chiffres (ex: 0100000000)
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Utilisateurs disponibles:</strong>
                                        <ul class="mb-0">
                                            <li><code>0100000000</code> - Administrateur KENAM (superadmin)</li>
                                            <li><code>0100000001</code> - Super Admin Test (superadmin)</li>
                                            <li><code>0100000002</code> - Admin Test (admin)</li>
                                            <li><code>0100000003</code> - Moderator Test (moderator)</li>
                                            <li><code>0100000004</code> - Agent Test (agent)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test de Connexion -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-play-circle me-2"></i>Test de Connexion en Temps Réel</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="testPhone" class="form-label">Téléphone (10 chiffres)</label>
                                            <input type="tel" class="form-control" id="testPhone" placeholder="0100000000" maxlength="10">
                                            <small class="form-text text-muted">Ex: 0100000000</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="testPassword" class="form-label">Mot de passe</label>
                                            <input type="password" class="form-control" id="testPassword" placeholder="admin123">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button onclick="testLogin()" class="btn btn-primary w-100">
                                                <i class="fas fa-sign-in-alt me-2"></i>Tester la Connexion
                                            </button>
                                        </div>
                                    </div>
                                    <div id="testResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-dark">
                                <h5><i class="fas fa-check-circle me-2"></i>Résumé du Processus</h5>
                                <ol>
                                    <li><strong>Formulaire:</strong> Utilisateur saisit 10 chiffres dans le champ "telephone"</li>
                                    <li><strong>JavaScript:</strong> Nettoie et formatte le numéro avant envoi</li>
                                    <li><strong>Controller:</strong> Crée des variations et cherche dans le champ "phone"</li>
                                    <li><strong>Base de Données:</strong> Retourne l'utilisateur si trouvé</li>
                                    <li><strong>Validation:</strong> Vérifie le mot de passe hashé et le statut actif</li>
                                    <li><strong>Connexion:</strong> Authentifie l'utilisateur et redirige selon le rôle</li>
                                </ol>
                                
                                <div class="mt-3">
                                    <strong>🎯 IDENTIFIANTS DE TEST:</strong><br>
                                    <code>Téléphone: 0100000000 | Mot de passe: admin123</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testLogin() {
    const phone = document.getElementById('testPhone').value;
    const password = document.getElementById('testPassword').value;
    const resultDiv = document.getElementById('testResult');
    
    if (!phone || !password) {
        resultDiv.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Veuillez remplir tous les champs
            </div>
        `;
        return;
    }
    
    // Nettoyer le téléphone (simuler le JavaScript du formulaire)
    const cleanPhone = phone.replace(/\D/g, '').slice(0, 10);
    
    resultDiv.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin me-2"></i>
            Test de connexion en cours...
        </div>
    `;
    
    // Envoyer la requête de test
    fetch('/test/check-permissions')
        .then(response => response.json())
        .then(data => {
            const userFound = data.phone === cleanPhone;
            
            if (userFound) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle me-2"></i>Connexion Simulée Réussie !</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Utilisateur:</strong> ${data.name}<br>
                                <strong>Rôle:</strong> ${data.role}<br>
                                <strong>Téléphone:</strong> ${data.phone}<br>
                                <strong>Actif:</strong> ${data.is_active ? 'OUI' : 'NON'}
                            </div>
                            <div class="col-md-6">
                                <strong>Accès Paramétrage:</strong> ${data.can_access_parametrage ? '✅ OUI' : '❌ NON'}<br>
                                <strong>Admin/Superadmin:</strong> ${data.is_admin ? '✅ OUI' : '❌ NON'}<br>
                                <strong>Redirection:</strong> ${data.role === 'superadmin' ? '/dashboard' : '/operations/dashboard'}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        Aucun utilisateur trouvé avec le téléphone: ${cleanPhone}
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erreur: ${error.message}
                </div>
            `;
        });
}

// Limiter l'entrée à 10 chiffres
document.getElementById('testPhone').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
});
</script>
@endpush
