<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Nouvelle Opération - Kenam OPS</title>
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #dc2626;
        }

        body {
            background: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding-bottom: 80px;
        }

        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }

        .mobile-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .form-section {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-section h5 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .photo-upload {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .photo-upload:hover {
            border-color: var(--primary);
            background: #f0f9ff;
        }

        .photo-preview {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .photo-item {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-item .remove-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            cursor: pointer;
        }

        .gps-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #0369a1;
        }

        .urgent-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 8px;
        }

        .submit-btn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 40px);
            max-width: 440px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }

        .submit-btn:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateX(-50%) translateY(-2px);
            box-shadow: 0 6px 30px rgba(37, 99, 235, 0.4);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .quick-select {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .quick-btn {
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 0.9rem;
        }

        .quick-btn:hover, .quick-btn.selected {
            border-color: var(--primary);
            background: #f0f9ff;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <div class="mobile-header">
            <div class="d-flex align-items-center">
                <a href="{{ route('mobile.terrain.index') }}" class="text-white me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h5 mb-0">Nouvelle Opération</h1>
                    <small>Création rapide terrain</small>
                </div>
            </div>
        </div>

        <form id="operationForm" enctype="multipart/form-data">
            @csrf

            <!-- Section Informations de base -->
            <div class="form-section">
                <h5><i class="fas fa-info-circle me-2"></i>Informations</h5>

                <div class="mb-3">
                    <label class="form-label">Titre de l'opération *</label>
                    <input type="text" name="titre" class="form-control" required
                           placeholder="Ex: Livraison matériel chantier">
                </div>

                <div class="mb-3">
                    <label class="form-label">Type d'opération *</label>
                    <select name="type_operation_id" class="form-select" required>
                        <option value="">Sélectionner...</option>
                        @foreach($typesOperations as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Client *</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Sélectionner...</option>
                        @foreach($recentClients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Service *</label>
                    <select name="service_operationnel_id" class="form-select" required>
                        <option value="">Sélectionner...</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant estimé (FCFA)</label>
                    <input type="number" name="montant_estime" class="form-control"
                           placeholder="0" step="1000">
                </div>
            </div>

            <!-- Section Photos - SUPPRIMÉE -->
            <!-- Pas de photos ni pièces jointes pour la version terrain simplifiée -->

            <!-- Section Localisation - SIMPLIFIÉE -->
            <div class="form-section">
                <h5><i class="fas fa-map-marker-alt me-2"></i>Localisation</h5>

                <div class="mb-3">
                    <label class="form-label">Lieu d'intervention</label>
                    <input type="text" name="lieu_intervention" class="form-control"
                           placeholder="Adresse ou description du lieu">
                </div>

                <!-- GPS Optionnel - masqué pour simplifier -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>

            <!-- Section Options -->
            <div class="form-section">
                <h5><i class="fas fa-cog me-2"></i>Options</h5>

                <div class="urgent-toggle">
                    <input type="checkbox" name="urgence" id="urgence" class="form-check-input">
                    <label for="urgence" class="form-check-label mb-0">
                        <strong>Marquer comme URGENT</strong>
                        <br><small>Traitement prioritaire</small>
                    </label>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Notes terrain</label>
                    <textarea name="notes_terrain" class="form-control" rows="3"
                              placeholder="Informations complémentaires..."></textarea>
                </div>
            </div>

            <!-- Espacement pour le bouton -->
            <div style="height: 100px;"></div>
        </form>

        <!-- Bouton Submit -->
        <button type="submit" form="operationForm" class="submit-btn" id="submitBtn">
            <span id="submitText">Créer l'opération</span>
            <div class="loading-spinner" id="loadingSpinner"></div>
        </button>
    </div>

    <script>
        let photos = []; // SUPPRIMÉ - Pas de photos

        // GPS - SIMPLIFIÉ (optionnel)
        function getCurrentPosition() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                    },
                    function(error) {
                        // GPS optionnel - pas d'erreur affichée
                        console.log('GPS non disponible');
                    }
                );
            }
        }

        // Soumission du formulaire
        document.getElementById('operationForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const spinner = document.getElementById('loadingSpinner');

            // Désactiver le bouton
            submitBtn.disabled = true;
            submitText.textContent = 'Création en cours...';
            spinner.style.display = 'inline-block';

            try {
                const formData = new FormData(this);

                // PAS de photos - formulaire simplifié

                const response = await fetch('{{ route("mobile.terrain.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Opération créée avec succès !', 'success');

                    // Redirection après 2 secondes
                    setTimeout(() => {
                        window.location.href = result.redirect_url || '{{ route("mobile.terrain.operations") }}';
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Erreur lors de la création');
                }

            } catch (error) {
                showNotification('Erreur: ' + error.message, 'error');

                // Réactiver le bouton
                submitBtn.disabled = false;
                submitText.textContent = 'Créer l\'opération';
                spinner.style.display = 'none';
            }
        });

        // Notifications
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
            notification.style.zIndex = '9999';
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Initialisation - SIMPLIFIÉE
        document.addEventListener('DOMContentLoaded', function() {
            // GPS optionnel seulement
            getCurrentPosition();
        });
    </script>
</body>
</html>
