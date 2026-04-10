@extends('layouts.app')

@section('title', 'Configuration Pointage Hikvision - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="{{ route('parametrage.index') }}" class="text-gray-800 text-decoration-none">
                <i class="fas fa-arrow-left"></i> Paramétrage
            </a>
            <span class="mx-2">/</span>
            Configuration Pointage Hikvision
        </h1>
    </div>

    <!-- Activation simple -->
    <div class="card shadow mb-4">
        <div class="card-header bg-purple text-white">
            <h6 class="mb-0">
                <i class="fas fa-power-off me-2"></i>Activation
            </h6>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="enabled" id="enabled" {{ ($hikvisionConfig['enabled'] ?? config('hikvision.enabled', false)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="enabled">
                            <h5 class="mb-0">Activer le pointage Hikvision</h5>
                            <p class="text-muted mb-0">Permet d'utiliser le dispositif de reconnaissance faciale pour le pointage des employés</p>
                        </label>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if(!($hikvisionConfig['enabled'] ?? config('hikvision.enabled', false)))
                        <button class="btn btn-success btn-lg" onclick="enableHikvision()">
                            <i class="fas fa-power-off me-2"></i> Activer
                        </button>
                    @else
                        <button class="btn btn-warning btn-lg" onclick="disableHikvision()">
                            <i class="fas fa-power-off me-2"></i> Désactiver
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Hikvision -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-cog me-2"></i>Configuration du Dispositif
            </h6>
        </div>
        <div class="card-body">
            <form id="hikvisionForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Adresse IP</label>
                        <input type="text" id="hikvisionIp" class="form-control" placeholder="192.168.1.70" value="{{ $hikvisionConfig['default_ip'] ?? config('hikvision.default_ip', '192.168.1.70') }}">
                        <small class="text-muted">IP du terminal Hikvision</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Port</label>
                        <input type="number" id="hikvisionPort" class="form-control" placeholder="80" value="{{ $hikvisionConfig['default_port'] ?? config('hikvision.default_port', 80) }}">
                        <small class="text-muted">Port HTTP</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Protocole</label>
                        <select id="hikvisionProtocol" class="form-select">
                            <option value="http" {{ config('hikvision.default_protocol', 'http') === 'http' ? 'selected' : '' }}>HTTP</option>
                            <option value="https" {{ config('hikvision.default_protocol', 'http') === 'https' ? 'selected' : '' }}>HTTPS</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Utilisateur</label>
                        <input type="text" id="hikvisionUser" class="form-control" value="{{ $hikvisionConfig['default_user'] ?? config('hikvision.default_user', 'admin') }}">
                        <small class="text-muted">Nom d'utilisateur</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" id="hikvisionPassword" class="form-control" value="{{ $hikvisionConfig['default_password'] ?? config('hikvision.default_password', '') }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('hikvisionPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Mot de passe du terminal</small>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Endpoint ISAPI</label>
                        <input type="text" id="hikvisionEndpoint" class="form-control" value="{{ $hikvisionConfig['default_endpoint'] ?? config('hikvision.default_endpoint', '/ISAPI/AccessControl/AcsEvent') }}">
                        <small class="text-muted">Endpoint ISAPI à utiliser</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Timeout (sec)</label>
                        <input type="number" id="hikvisionTimeout" class="form-control" value="{{ $hikvisionConfig['default_timeout'] ?? config('hikvision.default_timeout', 30) }}">
                        <small class="text-muted">Timeout de connexion</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Max résultats</label>
                        <input type="number" id="hikvisionMaxResults" class="form-control" value="{{ $hikvisionConfig['default_max_results'] ?? config('hikvision.default_max_results', 100) }}">
                        <small class="text-muted">Nombre max d'événements</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mode de test</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="testMode" id="testModeDirect" value="direct" {{ ($hikvisionConfig['test_mode'] ?? config('hikvision.test_mode', 'direct')) === 'direct' ? 'checked' : '' }}>
                            <input class="form-check-input" type="radio" name="testMode" id="testModeDirect" value="direct" checked>
                            <label class="form-check-label" for="testModeDirect">Direct</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="testMode" id="testModeEnv" value="env">
                            <label class="form-check-label" for="testModeEnv">Variables .env</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="testHikvisionConnection()">
                        <i class="fas fa-plug me-1"></i>Tester la connexion
                    </button>
                    <button type="button" class="btn btn-success" onclick="fetchHikvisionEvents()">
                        <i class="fas fa-download me-1"></i>Récupérer les événements
                    </button>
                    <button type="button" class="btn btn-warning" onclick="saveHikvisionConfig()">
                        <i class="fas fa-save me-1"></i>Enregistrer comme défaut
                    </button>
                    <button type="button" class="btn btn-info" onclick="resetHikvisionConfig()">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </button>
                </div>
                <div id="hikvisionResults" class="mt-3"></div>
            </form>
        </div>
    </div>

    <!-- Synchronisation des employés -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">
                <i class="fas fa-users-cog me-2"></i>Synchronisation des Employés
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Photo employé</label>
                    <div class="card border">
                        <div class="card-body text-center">
                            <!-- Zone de preview avec cadre style Canada/Visa -->
                            <div id="photoPreview" class="mb-3 position-relative d-inline-block" style="min-width: 300px; min-height: 400px; max-width: 350px; max-height: 450px;">
                                <!-- Cadre photo style Carte d'Identité -->
                                <div class="photo-frame" style="width: 300px; height: 400px; margin: 0 auto; position: relative; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; border: 2px solid #dee2e6;">
                                    <!-- Lignes guides de positionnement CI -->
                                    <div class="guidelines" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none;">
                                        <!-- Zone visage (rectangle central) -->
                                        <div style="position: absolute; top: 25%; left: 15%; right: 15%; height: 45%; border: 2px solid rgba(220, 53, 69, 0.4); border-radius: 8px; background: rgba(220, 53, 69, 0.05);">
                                            <!-- Indicateur centre visage -->
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 12px; height: 12px; border: 2px solid rgba(220, 53, 69, 0.6); border-radius: 50%; background: rgba(220, 53, 69, 0.1);"></div>
                                        </div>

                                        <!-- Ligne yeux (35% du haut) -->
                                        <div style="position: absolute; top: 35%; left: 8%; right: 8%; height: 1px; background: rgba(25, 135, 84, 0.6); border-top: 1px dashed rgba(25, 135, 84, 0.8);">
                                            <!-- Marqueurs yeux -->
                                            <div style="position: absolute; left: 30%; top: -4px; width: 8px; height: 8px; background: rgba(25, 135, 84, 0.8); border-radius: 50%;"></div>
                                            <div style="position: absolute; right: 30%; top: -4px; width: 8px; height: 8px; background: rgba(25, 135, 84, 0.8); border-radius: 50%;"></div>
                                        </div>

                                        <!-- Ligne menton (75% du haut) -->
                                        <div style="position: absolute; top: 75%; left: 8%; right: 8%; height: 1px; background: rgba(25, 135, 84, 0.6); border-top: 1px dashed rgba(25, 135, 84, 0.8);"></div>

                                        <!-- Ligne verticale centrale -->
                                        <div style="position: absolute; top: 15%; bottom: 15%; left: 50%; width: 1px; background: rgba(25, 135, 84, 0.6); border-left: 1px dashed rgba(25, 135, 84, 0.8);"></div>

                                        <!-- Coins de cadre CI -->
                                        <div style="position: absolute; top: 15%; left: 10%; width: 30px; height: 30px; border-top: 3px solid rgba(13, 110, 253, 0.8); border-left: 3px solid rgba(13, 110, 253, 0.8); border-radius: 4px 0 0 0;"></div>
                                        <div style="position: absolute; top: 15%; right: 10%; width: 30px; height: 30px; border-top: 3px solid rgba(13, 110, 253, 0.8); border-right: 3px solid rgba(13, 110, 253, 0.8); border-radius: 0 4px 0 0;"></div>
                                        <div style="position: absolute; bottom: 15%; left: 10%; width: 30px; height: 30px; border-bottom: 3px solid rgba(13, 110, 253, 0.8); border-left: 3px solid rgba(13, 110, 253, 0.8); border-radius: 0 0 0 4px;"></div>
                                        <div style="position: absolute; bottom: 15%; right: 10%; width: 30px; height: 30px; border-bottom: 3px solid rgba(13, 110, 253, 0.8); border-right: 3px solid rgba(13, 110, 253, 0.8); border-radius: 0 0 4px 0;"></div>

                                        <!-- Guides d'épaules -->
                                        <div style="position: absolute; top: 85%; left: 20%; right: 20%; height: 1px; background: rgba(255, 193, 7, 0.5); border-top: 1px dotted rgba(255, 193, 7, 0.7);"></div>

                                        <!-- Texte guide -->
                                        <div style="position: absolute; top: 8%; left: 0; right: 0; text-align: center;">
                                            <span style="background: rgba(13, 110, 253, 0.95); color: white; padding: 6px 16px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);">
                                                CARTE D'IDENTITÉ
                                            </span>
                                        </div>

                                        <!-- Dimensions -->
                                        <div style="position: absolute; bottom: 5%; left: 0; right: 0; text-align: center;">
                                            <span style="background: rgba(33, 37, 41, 0.8); color: white; padding: 3px 10px; border-radius: 12px; font-size: 9px; font-weight: 500;">
                                                35mm × 45mm
                                            </span>
                                        </div>

                                        <!-- Mesures guide -->
                                        <div style="position: absolute; top: 25%; left: -25px; bottom: 25%; width: 20px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <span style="writing-mode: vertical-rl; text-orientation: mixed; font-size: 8px; color: rgba(108, 117, 125, 0.8); font-weight: 500;">45mm</span>
                                        </div>
                                        <div style="position: absolute; left: 15%; right: 15%; bottom: -20px; height: 15px; display: flex; justify-content: center; align-items: center;">
                                            <span style="font-size: 8px; color: rgba(108, 117, 125, 0.8); font-weight: 500;">35mm</span>
                                        </div>
                                    </div>

                                    <!-- Image preview -->
                                    <img id="previewImage" src="" alt="Aperçu de la photo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover; display: none;">

                                    <!-- Placeholder -->
                                    <div id="photoPlaceholder" class="d-flex flex-column align-items-center justify-content-center h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                        <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-1" style="font-size: 14px;">Photo d'identité</p>
                                        <p class="text-muted small mb-0" style="font-size: 12px;">Format: 3x4 - Visage centré</p>
                                    </div>
                                </div>

                                <!-- Instructions -->
                                <div class="mt-3 text-start">
                                    <h6 class="text-muted mb-2"><i class="fas fa-id-card me-1"></i>Norme Carte d'Identité:</h6>
                                    <ul class="small text-muted mb-0" style="font-size: 12px;">
                                        <li><strong>Format:</strong> 35mm × 45mm (ISO/IEC 19794-5)</li>
                                        <li><strong>Visage:</strong> Centré dans le cadre rouge (70% de la hauteur)</li>
                                        <li><strong>Yeux:</strong> Sur la ligne verte à 35% du haut</li>
                                        <li><strong>Menton:</strong> Sur la ligne verte à 75% du haut</li>
                                        <li><strong>Épaules:</strong> Visibles jusqu'à la ligne jaune</li>
                                        <li><strong>Fond:</strong> Clair et uni (blanc ou gris clair)</li>
                                        <li><strong>Expression:</strong> Neutre, regard vers l'objectif</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Options de chargement -->
                            <div class="d-flex gap-2 justify-content-center mb-3">
                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('photoFile').click()">
                                    <i class="fas fa-upload me-1"></i> Charger une photo
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="startCamera()">
                                    <i class="fas fa-camera me-1"></i> Prendre une photo
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="clearPhoto()" id="clearBtn" style="display: none;">
                                    <i class="fas fa-trash me-1"></i> Supprimer
                                </button>
                            </div>

                            <!-- Input file caché -->
                            <input type="file" id="photoFile" accept="image/*" style="display: none;" onchange="handleFileSelect(event)">

                            <!-- Champ base64 caché -->
                            <textarea id="photoBase64" rows="1" style="display: none;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Matricule employé</label>
                    <input type="text" id="employeeMatricule" class="form-control" placeholder="EMP001">
                    <small class="text-muted">Matricule correspondant à la photo</small>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nom complet</label>
                    <input type="text" id="employeeName" class="form-control" placeholder="Jean Dupont">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Terminal cible</label>
                    <select id="syncDeviceId" class="form-select">
                        <option value="">Tous les terminaux</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="syncSingleEmployee()">
                    <i class="fas fa-user-plus me-1"></i>Synchroniser cet employé
                </button>
                <button type="button" class="btn btn-success" onclick="syncAllEmployees()">
                    <i class="fas fa-users me-1"></i>Synchroniser tous les employés
                </button>
                <button type="button" class="btn btn-info" onclick="testEmployeePhoto()">
                    <i class="fas fa-image me-1"></i>Tester la photo
                </button>
            </div>
            <div id="syncResults" class="mt-3"></div>
        </div>
    </div>

    <!-- Lien vers diagnostic -->
    <div class="card shadow">
        <div class="card-body text-center">
            <h6 class="mb-3">Diagnostic complet du système</h6>
            <p class="text-muted mb-3">Pour un diagnostic détaillé du dispositif Hikvision et des tests avancés</p>
            <a href="{{ route('hikvision.diagnostic') }}" class="btn btn-outline-primary">
                <i class="fas fa-stethoscope me-1"></i> Accéder au diagnostic
            </a>
        </div>
    </div>
</div>

<!-- Modal Webcam professionnel -->
<div class="modal fade" id="cameraModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Studio Photo - Capture d'identité
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Colonne gauche : Preview webcam avec cadre -->
                    <div class="col-md-8">
                        <div class="text-center">
                            <h6 class="mb-3">Aperçu en direct</h6>

                            <!-- Conteneur webcam avec cadre intégré -->
                            <div class="webcam-container position-relative d-inline-block" style="width: 480px; height: 640px;">
                                <!-- Cadre Carte d'Identité par-dessus la vidéo -->
                                <div class="photo-frame-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 360px; height: 480px; z-index: 10; pointer-events: none;">
                                    <!-- Cadre CI -->
                                    <div style="width: 100%; height: 100%; position: relative; border: 3px solid #0d6efd; border-radius: 12px; background: rgba(255,255,255,0.08); box-shadow: 0 0 0 1px rgba(13, 110, 253, 0.4), 0 8px 32px rgba(0,0,0,0.15);">
                                        <!-- Lignes guides CI -->
                                        <div class="guidelines" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                                            <!-- Zone visage -->
                                            <div style="position: absolute; top: 25%; left: 15%; right: 15%; height: 45%; border: 2px solid rgba(220, 53, 69, 0.6); border-radius: 8px; background: rgba(220, 53, 69, 0.08);">
                                                <!-- Centre visage -->
                                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 14px; height: 14px; border: 2px solid rgba(220, 53, 69, 0.8); border-radius: 50%; background: rgba(220, 53, 69, 0.15);"></div>
                                            </div>

                                            <!-- Ligne yeux -->
                                            <div style="position: absolute; top: 35%; left: 8%; right: 8%; height: 2px; background: rgba(25, 135, 84, 0.8); box-shadow: 0 0 6px rgba(25, 135, 84, 0.9);">
                                                <!-- Marqueurs yeux -->
                                                <div style="position: absolute; left: 30%; top: -5px; width: 10px; height: 10px; background: rgba(25, 135, 84, 0.9); border-radius: 50%; box-shadow: 0 0 4px rgba(25, 135, 84, 0.8);"></div>
                                                <div style="position: absolute; right: 30%; top: -5px; width: 10px; height: 10px; background: rgba(25, 135, 84, 0.9); border-radius: 50%; box-shadow: 0 0 4px rgba(25, 135, 84, 0.8);"></div>
                                            </div>

                                            <!-- Ligne menton -->
                                            <div style="position: absolute; top: 75%; left: 8%; right: 8%; height: 2px; background: rgba(25, 135, 84, 0.8); box-shadow: 0 0 6px rgba(25, 135, 84, 0.9);"></div>

                                            <!-- Ligne centre -->
                                            <div style="position: absolute; top: 15%; bottom: 15%; left: 50%; width: 2px; background: rgba(25, 135, 84, 0.8); box-shadow: 0 0 6px rgba(25, 135, 84, 0.9);"></div>

                                            <!-- Coins CI -->
                                            <div style="position: absolute; top: 15%; left: 10%; width: 35px; height: 35px; border-top: 4px solid #fff; border-left: 4px solid #fff; border-radius: 6px 0 0 0; box-shadow: -3px -3px 8px rgba(13, 110, 253, 0.7);"></div>
                                            <div style="position: absolute; top: 15%; right: 10%; width: 35px; height: 35px; border-top: 4px solid #fff; border-right: 4px solid #fff; border-radius: 0 6px 0 0; box-shadow: 3px -3px 8px rgba(13, 110, 253, 0.7);"></div>
                                            <div style="position: absolute; bottom: 15%; left: 10%; width: 35px; height: 35px; border-bottom: 4px solid #fff; border-left: 4px solid #fff; border-radius: 0 0 0 6px; box-shadow: -3px 3px 8px rgba(13, 110, 253, 0.7);"></div>
                                            <div style="position: absolute; bottom: 15%; right: 10%; width: 35px; height: 35px; border-bottom: 4px solid #fff; border-right: 4px solid #fff; border-radius: 0 0 6px 0; box-shadow: 3px 3px 8px rgba(13, 110, 253, 0.7);"></div>

                                            <!-- Guide épaules -->
                                            <div style="position: absolute; top: 85%; left: 20%; right: 20%; height: 2px; background: rgba(255, 193, 7, 0.7); box-shadow: 0 0 4px rgba(255, 193, 7, 0.8);"></div>

                                            <!-- Texte guide CI -->
                                            <div style="position: absolute; top: 5%; left: 0; right: 0; text-align: center;">
                                                <span style="background: rgba(13, 110, 253, 0.95); color: white; padding: 8px 20px; border-radius: 25px; font-size: 12px; font-weight: 700; letter-spacing: 1px; box-shadow: 0 3px 12px rgba(13, 110, 253, 0.4); text-transform: uppercase;">
                                                    Carte d'Identité
                                                </span>
                                            </div>

                                            <!-- Dimensions -->
                                            <div style="position: absolute; bottom: 4%; left: 0; right: 0; text-align: center;">
                                                <span style="background: rgba(33, 37, 41, 0.9); color: white; padding: 4px 12px; border-radius: 15px; font-size: 10px; font-weight: 600; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);">
                                                    35mm × 45mm • ISO/IEC 19794-5
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vidéo webcam -->
                                <video id="cameraVideo" width="480" height="640" autoplay style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; background: #000;"></video>

                                <!-- Canvas caché pour capture -->
                                <canvas id="cameraCanvas" width="480" height="640" style="display: none;"></canvas>

                                <!-- Placeholder pendant chargement -->
                                <div id="cameraPlaceholder" class="d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border-radius: 8px;">
                                    <div class="text-center text-white">
                                        <i class="fas fa-camera fa-5x mb-4 opacity-75"></i>
                                        <h5 class="mb-3">Activation de la webcam professionnelle...</h5>
                                        <div class="spinner-border text-light mb-3" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <p class="small opacity-75">Veuillez autoriser l'accès à votre caméra</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne droite : Contrôles et instructions -->
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-id-card me-2"></i>Norme Carte d'Identité
                                </h6>
                                <ul class="small mb-4">
                                    <li><strong>Position:</strong> Face à la caméra, regard droit</li>
                                    <li><strong>Visage:</strong> Dans le cadre rouge (70% hauteur)</li>
                                    <li><strong>Yeux:</strong> Sur la ligne verte (35% du haut)</li>
                                    <li><strong>Menton:</strong> Sur la ligne verte (75% du haut)</li>
                                    <li><strong>Épaules:</strong> Jusqu'à la ligne jaune</li>
                                    <li><strong>Éclairage:</strong> Uniforme, sans ombres</li>
                                    <li><strong>Fond:</strong> Clair et uni (blanc/gris)</li>
                                    <li><strong>Expression:</strong> Neutre, bouche fermée</li>
                                </ul>

                                <h6 class="card-title">
                                    <i class="fas fa-sliders-h me-2"></i>Réglages
                                </h6>

                                <!-- Contrôle de luminosité -->
                                <div class="mb-3">
                                    <label class="form-label small">Luminosité</label>
                                    <input type="range" class="form-range" id="brightnessSlider" min="0" max="200" value="100">
                                    <div class="d-flex justify-content-between">
                                        <small>Sombre</small>
                                        <small>Normal</small>
                                        <small>Clair</small>
                                    </div>
                                </div>

                                <!-- Contrôle de contraste -->
                                <div class="mb-3">
                                    <label class="form-label small">Contraste</label>
                                    <input type="range" class="form-range" id="contrastSlider" min="50" max="150" value="100">
                                    <div class="d-flex justify-content-between">
                                        <small>Bas</small>
                                        <small>Normal</small>
                                        <small>Élevé</small>
                                    </div>
                                </div>

                                <!-- Qualité -->
                                <div class="mb-4">
                                    <label class="form-label small">Qualité photo</label>
                                    <select class="form-select form-select-sm" id="qualitySelect">
                                        <option value="0.6">Standard (rapide)</option>
                                        <option value="0.8" selected>Haute qualité</option>
                                        <option value="0.95">Maximum (lent)</option>
                                    </select>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-lg" id="captureBtn" onclick="capturePhoto()" style="display: none;" disabled>
                                        <i class="fas fa-camera me-2"></i> Capturer la photo
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="switchCamera()" id="switchBtn" style="display: none;">
                                        <i class="fas fa-sync-alt me-1"></i> Changer de caméra
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <div class="ms-auto text-muted small" id="cameraStatus">
                    <i class="fas fa-circle text-danger me-1"></i> Caméra inactive
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fonctions pour la configuration Hikvision
function enableHikvision() {
    if (confirm('Activer le pointage Hikvision ?')) {
        console.log('Activation de Hikvision...');
        // Logique d'activation
        location.reload();
    }
}

function disableHikvision() {
    if (confirm('Désactiver le pointage Hikvision ?')) {
        console.log('Désactivation de Hikvision...');
        // Logique de désactivation
        location.reload();
    }
}

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Fonctions de test Hikvision
async function testHikvisionConnection() {
    const resultsDiv = document.getElementById('hikvisionResults');
    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Test de connexion en cours...</div>';

    let url, data;

    if (document.querySelector('input[name="testMode"]:checked').value === 'env') {
        url = '{{ route("hikvision.test-env") }}';
        data = {};
    } else {
        url = '{{ route("hikvision.test-connection") }}';
        data = {
            ip: document.getElementById('hikvisionIp').value,
            port: document.getElementById('hikvisionPort').value,
            username: document.getElementById('hikvisionUser').value,
            password: document.getElementById('hikvisionPassword').value,
            protocol: document.getElementById('hikvisionProtocol').value,
            timeout: document.getElementById('hikvisionTimeout').value
        };
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Connexion réussie!</strong><br>
                    IP: ${result.ip || data.ip}<br>
                    Port: ${result.port || data.port}<br>
                    Code HTTP: ${result.http_code}
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Échec de connexion:</strong> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur:</strong> ${error.message}
            </div>
        `;
    }
}

async function fetchHikvisionEvents() {
    const resultsDiv = document.getElementById('hikvisionResults');
    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Récupération des événements...</div>';

    let url, data;

    if (document.querySelector('input[name="testMode"]:checked').value === 'env') {
        url = '{{ route("hikvision.fetch-events-env") }}';
        data = {
            max_results: document.getElementById('hikvisionMaxResults').value
        };
    } else {
        url = '{{ route("hikvision.fetch-events") }}';
        data = {};
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Événements récupérés!</strong><br>
                    Nombre d'événements: ${result.events_count}<br>
                    ${result.max_results ? `Max résultats: ${result.max_results}` : ''}
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Échec de récupération:</strong> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur:</strong> ${error.message}
            </div>
        `;
    }
}

async function saveHikvisionConfig() {
    const resultsDiv = document.getElementById('hikvisionResults');
    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Enregistrement de la configuration...</div>';

    const config = {
        enabled: document.getElementById('enabled').checked,
        ip: document.getElementById('hikvisionIp').value,
        port: document.getElementById('hikvisionPort').value,
        protocol: document.getElementById('hikvisionProtocol').value,
        username: document.getElementById('hikvisionUser').value,
        password: document.getElementById('hikvisionPassword').value,
        endpoint: document.getElementById('hikvisionEndpoint').value,
        timeout: document.getElementById('hikvisionTimeout').value,
        max_results: document.getElementById('hikvisionMaxResults').value
    };

    try {
        const response = await fetch('{{ route("hikvision.save-config") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(config)
        });

        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Configuration enregistrée!</strong><br>
                    Les nouvelles valeurs par défaut ont été sauvegardées.
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erreur:</strong> ${result.message || 'Erreur inconnue'}
                </div>
            `;
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur:</strong> ${error.message}
            </div>
        `;
    }
}

function resetHikvisionConfig() {
    if (confirm('Réinitialiser la configuration aux valeurs par défaut ?')) {
        document.getElementById('hikvisionIp').value = '192.168.1.70';
        document.getElementById('hikvisionPort').value = '80';
        document.getElementById('hikvisionProtocol').value = 'http';
        document.getElementById('hikvisionUser').value = 'admin';
        document.getElementById('hikvisionPassword').value = '';
        document.getElementById('hikvisionEndpoint').value = '/ISAPI/AccessControl/AcsEvent';
        document.getElementById('hikvisionTimeout').value = '30';
        document.getElementById('hikvisionMaxResults').value = '100';
    }
}

// Fonctions pour la gestion des photos
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            displayPhoto(e.target.result);
            document.getElementById('photoBase64').value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function displayPhoto(dataUrl) {
    const previewImage = document.getElementById('previewImage');
    const placeholder = document.getElementById('photoPlaceholder');
    const clearBtn = document.getElementById('clearBtn');

    previewImage.src = dataUrl;
    previewImage.style.display = 'block';
    placeholder.style.display = 'none';
    clearBtn.style.display = 'inline-block';
}

function clearPhoto() {
    const previewImage = document.getElementById('previewImage');
    const placeholder = document.getElementById('photoPlaceholder');
    const clearBtn = document.getElementById('clearBtn');
    const photoBase64 = document.getElementById('photoBase64');
    const photoFile = document.getElementById('photoFile');

    previewImage.style.display = 'none';
    previewImage.src = '';
    placeholder.style.display = 'flex';
    clearBtn.style.display = 'none';
    photoBase64.value = '';
    photoFile.value = '';
}

let cameraStream = null;
let currentCameraIndex = 0;
let availableCameras = [];

async function startCamera() {
    try {
        // Afficher le modal
        const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
        modal.show();

        // Récupérer les caméras disponibles
        const devices = await navigator.mediaDevices.enumerateDevices();
        availableCameras = devices.filter(device => device.kind === 'videoinput');

        if (availableCameras.length === 0) {
            throw new Error('Aucune caméra détectée');
        }

        // Demander l'accès à la webcam
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            }
        });

        cameraStream = stream;

        // Afficher la vidéo
        const video = document.getElementById('cameraVideo');
        const placeholder = document.getElementById('cameraPlaceholder');
        const captureBtn = document.getElementById('captureBtn');
        const switchBtn = document.getElementById('switchBtn');
        const status = document.getElementById('cameraStatus');

        video.srcObject = stream;
        video.style.display = 'block';
        placeholder.style.display = 'none';
        captureBtn.style.display = 'block';
        captureBtn.disabled = false;

        if (availableCameras.length > 1) {
            switchBtn.style.display = 'block';
        }

        status.innerHTML = '<i class="fas fa-circle text-success me-1"></i> Caméra active';

        // Appliquer les filtres
        applyFilters();

    } catch (error) {
        console.error('Erreur webcam:', error);
        alert('Impossible d\'accéder à la webcam. Vérifiez que vous avez bien autorisé l\'accès et qu\'aucune autre application ne l\'utilise.');

        // Fermer le modal en cas d'erreur
        const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
        if (modal) modal.hide();
    }
}

function applyFilters() {
    const video = document.getElementById('cameraVideo');
    const brightness = document.getElementById('brightnessSlider').value;
    const contrast = document.getElementById('contrastSlider').value;

    video.style.filter = `brightness(${brightness}%) contrast(${contrast}%)`;
}

function switchCamera() {
    if (availableCameras.length <= 1) return;

    currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;

    // Arrêter le stream actuel
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }

    // Démarrer avec la nouvelle caméra
    startCameraWithIndex(currentCameraIndex);
}

async function startCameraWithIndex(index) {
    try {
        const constraints = {
            video: {
                deviceId: availableCameras[index].deviceId,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        };

        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        cameraStream = stream;

        const video = document.getElementById('cameraVideo');
        video.srcObject = stream;

        applyFilters();

    } catch (error) {
        console.error('Erreur changement caméra:', error);
    }
}

function capturePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const context = canvas.getContext('2d');
    const quality = parseFloat(document.getElementById('qualitySelect').value);

    // Dessiner l'image de la vidéo sur le canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Appliquer les filtres sur le canvas
    const brightness = document.getElementById('brightnessSlider').value / 100;
    const contrast = document.getElementById('contrastSlider').value / 100;

    context.filter = `brightness(${brightness}) contrast(${contrast})`;
    context.drawImage(canvas, 0, 0);

    // Convertir en base64
    const dataUrl = canvas.toDataURL('image/jpeg', quality);

    // Afficher la photo
    displayPhoto(dataUrl);
    document.getElementById('photoBase64').value = dataUrl;

    // Fermer le modal et arrêter la webcam
    stopCamera();
    const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
    if (modal) modal.hide();
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }

    // Réinitialiser le modal
    const video = document.getElementById('cameraVideo');
    const placeholder = document.getElementById('cameraPlaceholder');
    const captureBtn = document.getElementById('captureBtn');
    const switchBtn = document.getElementById('switchBtn');
    const status = document.getElementById('cameraStatus');

    video.style.display = 'none';
    video.srcObject = null;
    placeholder.style.display = 'flex';
    captureBtn.style.display = 'none';
    switchBtn.style.display = 'none';
    status.innerHTML = '<i class="fas fa-circle text-danger me-1"></i> Caméra inactive';

    // Réinitialiser les sliders
    document.getElementById('brightnessSlider').value = 100;
    document.getElementById('contrastSlider').value = 100;
}

// Nettoyer la webcam quand le modal se ferme
document.addEventListener('DOMContentLoaded', function() {
    const cameraModal = document.getElementById('cameraModal');
    if (cameraModal) {
        cameraModal.addEventListener('hidden.bs.modal', function () {
            stopCamera();
        });

        // Écouteurs pour les sliders
        document.getElementById('brightnessSlider').addEventListener('input', applyFilters);
        document.getElementById('contrastSlider').addEventListener('input', applyFilters);
    }
});

// Fonctions de synchronisation
async function syncSingleEmployee() {
    const resultsDiv = document.getElementById('syncResults');
    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Synchronisation de l\'employé...</div>';

    const employeeData = {
        matricule: document.getElementById('employeeMatricule').value,
        name: document.getElementById('employeeName').value,
        photo: document.getElementById('photoBase64').value,
        device_id: document.getElementById('syncDeviceId').value
    };

    try {
        const response = await fetch('{{ route("hikvision.sync-employees") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(employeeData)
        });

        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Synchronisation réussie!</strong><br>
                    Employé: ${employeeData.name}<br>
                    Matricule: ${employeeData.matricule}
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Échec de synchronisation:</strong> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur:</strong> ${error.message}
            </div>
        `;
    }
}

async function syncAllEmployees() {
    const resultsDiv = document.getElementById('syncResults');
    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Synchronisation de tous les employés...</div>';

    try {
        const response = await fetch('{{ route("hikvision.sync-all-employees") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Synchronisation terminée!</strong><br>
                    Total: ${result.total_count}<br>
                    Réussis: ${result.synced_count}<br>
                    Échecs: ${result.failed_count}
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Échec de synchronisation:</strong> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur:</strong> ${error.message}
            </div>
        `;
    }
}

async function testEmployeePhoto() {
    const resultsDiv = document.getElementById('syncResults');
    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Test de la photo...</div>';

    const employeeId = document.getElementById('employeeMatricule').value;

    try {
        const response = await fetch('{{ route("hikvision.test-photo") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ employee_id: employeeId })
        });

        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Photo testée avec succès!</strong><br>
                    Employé ID: ${result.data.employee_id}<br>
                    Photo capturée: ${result.data.photo_captured ? 'Oui' : 'Non'}<br>
                    Heure: ${result.data.capture_time}
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Échec du test:</strong> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur:</strong> ${error.message}
            </div>
        `;
    }
}
</script>
@endpush
