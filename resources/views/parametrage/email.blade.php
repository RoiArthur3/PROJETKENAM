@extends('layouts.app')

@section('title', 'Configuration Email - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="{{ route('parametrage.index') }}" class="text-gray-800 text-decoration-none">
                <i class="fas fa-arrow-left"></i> Paramétrage
            </a>
            <span class="mx-2">/</span>
            Configuration Email
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Paramètres SMTP</h6>
                </div>
                <div class="card-body">
                    <form id="emailConfigForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Méthode d'envoi</label>
                                    <select name="mail_mailer" class="form-control">
                                        <option value="smtp" {{ ($config['mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mail" {{ ($config['mailer'] ?? '') == 'mail' ? 'selected' : '' }}>PHP Mail</option>
                                        <option value="sendmail" {{ ($config['mailer'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hôte SMTP</label>
                                    <input type="text" name="mail_host" class="form-control" value="{{ $config['host'] ?? '' }}" placeholder="smtp.example.com">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Port</label>
                                    <input type="number" name="mail_port" class="form-control" value="{{ $config['port'] ?? 587 }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Chiffrement</label>
                                    <select name="mail_encryption" class="form-control">
                                        <option value="tls" {{ ($config['encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($config['encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="">Aucun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Utilisateur SMTP</label>
                                    <input type="text" name="mail_username" class="form-control" value="{{ $config['username'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mot de passe SMTP</label>
                                    <input type="password" name="mail_password" class="form-control" value="{{ $config['password'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                        <hr>
                        <h6 class="font-weight-bold text-gray-800 mb-3">Expéditeur par défaut</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Expéditeur</label>
                                    <input type="email" name="mail_from_address" class="form-control" value="{{ $config['from_address'] ?? '' }}" placeholder="no-reply@kenam.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nom Expéditeur</label>
                                    <input type="text" name="mail_from_name" class="form-control" value="{{ $config['from_name'] ?? 'KENAM Services' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" onclick="saveEmailConfig()">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                            <button type="button" class="btn btn-info ml-2" onclick="testEmailConfig()">
                                <i class="fas fa-paper-plane me-2"></i> Tester la connexion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Aide</h6>
                </div>
                <div class="card-body">
                    <p>Configurez ici le serveur de messagerie pour l'envoi des notifications, factures et rapports.</p>
                    <p>Pour Gmail, utilisez :</p>
                    <ul>
                        <li>Hôte: smtp.gmail.com</li>
                        <li>Port: 587 (TLS)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function saveEmailConfig() {
    const form = document.getElementById('emailConfigForm');
    const formData = new FormData(form);

    fetch('{{ route("parametrage.email.save") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Erreur: ' + JSON.stringify(data.errors || data.message));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur technique lors de la sauvegarde.');
    });
}

function testEmailConfig() {
    const testEmail = prompt('Entrez une adresse email pour le test:');
    if(!testEmail) return;
    
    // Logique de test à implémenter si route dispo
    alert('Test envoyé à ' + testEmail + ' (Simulation)');
}
</script>
@endpush
@endsection
