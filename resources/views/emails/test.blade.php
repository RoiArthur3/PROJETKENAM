@extends('layouts.app')

@section('title', 'Test Email Design | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope me-2 text-primary"></i>Test Email Design KENAM
        </h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Test Manuel
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('email.test.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="to_email" class="form-label">Email destinataire *</label>
                            <input type="email" class="form-control" id="to_email" name="to_email" 
                                   value="technolabpro@gmail.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet *</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="Test Design Email KENAM SERVICES" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required>Ceci est un test du nouveau design premium des emails KENAM SERVICES avec les couleurs orange, blanc et vert. Le logo est maintenant plus grand et le design est complètement refait avec des animations modernes.</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le test
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket me-2"></i>Test Automatique
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Envoyer un email de test aux deux adresses spécifiées avec le nouveau design KENAM.
                    </p>
                    
                    <div class="mb-3">
                        <h6>Adresses de test :</h6>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                technolabpro@gmail.com
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                pmo.arthur@webpluriel.com
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-success" onclick="sendTestEmails()">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer aux deux adresses
                    </button>

                    <div id="testResults" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations sur le Design
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">🎨 Palette de couleurs KENAM</h6>
                            <ul>
                                <li><span style="display: inline-block; width: 20px; height: 20px; background: #ff6b35; border-radius: 3px;"></span> Orange primaire</li>
                                <li><span style="display: inline-block; width: 20px; height: 20px; background: #16a34a; border-radius: 3px;"></span> Vert succès</li>
                                <li><span style="display: inline-block; width: 20px; height: 20px; background: #ffffff; border: 1px solid #ddd; border-radius: 3px;"></span> Blanc propreté</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">✨ Nouveautés du design</h6>
                            <ul>
                                <li>Logo XXL (180px × 90px)</li>
                                <li>Animations shine et rotate</li>
                                <li>Gradients modernes</li>
                                <li>Effets 3D et hover</li>
                                <li>Typography premium</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestEmails() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    fetch('{{ route('email.test.send') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const resultsDiv = document.getElementById('testResults');
        let html = '<div class="alert alert-info"><h6>Résultats:</h6><ul>';
        
        for (const [email, result] of Object.entries(data.results)) {
            html += `<li>${result} - ${email}</li>`;
        }
        
        html += '</ul></div>';
        resultsDiv.innerHTML = html;
        
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check me-2"></i>Test terminé';
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 3000);
    })
    .catch(error => {
        console.error('Error:', error);
        const resultsDiv = document.getElementById('testResults');
        resultsDiv.innerHTML = '<div class="alert alert-danger">Erreur: ' + error.message + '</div>';
        
        button.disabled = false;
        button.innerHTML = originalText;
    });
}
</script>
@endsection
