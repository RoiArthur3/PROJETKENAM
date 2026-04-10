@extends('layouts.app')

@section('title', 'Gestionnaire de Fichiers - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-folder-open me-2 text-primary"></i>Gestionnaire de Fichiers
            </h1>
            <p class="text-muted mb-0">Téléverser, organiser et gérer les fichiers du système</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-upload me-1"></i>Uploader Fichiers
            </button>
            <button class="btn btn-info" onclick="refreshFileManager()">
                <i class="fas fa-sync me-1"></i>Rafraîchir
            </button>
        </div>
    </div>

    <!-- Alertes -->
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

    <!-- Interface principale -->
    <div class="row">
        <!-- Zone principale -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-folder me-2"></i>
                        Fichiers et Documents
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Zone de glisser-déposer -->
                    <div id="dropZone" class="border-2 border-dashed rounded p-4 text-center mb-3" 
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)" 
                         ondragleave="handleDragLeave(event)">
                        <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Glissez-déposez des fichiers ici</h5>
                        <p class="text-muted">ou cliquez sur le bouton "Uploader Fichiers" pour sélectionner</p>
                        <input type="file" id="fileInput" multiple class="d-none" onchange="handleFileSelect(event)">
                    </div>

                    <!-- Barre de recherche -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Rechercher des fichiers..." onkeyup="searchFiles()">
                                <button class="btn btn-outline-secondary" onclick="clearSearch()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary" onclick="filterFiles('all')">Tous</button>
                                <button class="btn btn-outline-secondary" onclick="filterFiles('images')">Images</button>
                                <button class="btn btn-outline-secondary" onclick="filterFiles('documents')">Documents</button>
                                <button class="btn btn-outline-secondary" onclick="filterFiles('videos')">Vidéos</button>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des fichiers -->
                    <div id="fileList" class="row">
                        <!-- Les fichiers seront ajoutés ici par JavaScript -->
                    </div>

                    <!-- Message si aucun fichier -->
                    <div id="noFilesMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5>Aucun fichier trouvé</h5>
                        <p class="text-muted">Commencez par uploader des fichiers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#dropZone {
    transition: all 0.3s ease;
    cursor: pointer;
}

#dropZone:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

#dropZone.dragover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.file-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.file-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.file-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.file-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.file-item:hover .file-actions {
    opacity: 1;
}

.upload-progress {
    margin-top: 10px;
}
</style>

<script>
let uploadedFiles = [];

// Fonction principale pour gérer l'upload
function handleFileSelect(event) {
    const files = event.target.files;
    uploadFiles(files);
}

function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('dragover');
    
    const files = event.dataTransfer.files;
    uploadFiles(files);
}

function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.add('dragover');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('dragover');
}

function uploadFiles(files) {
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        uploadFile(file);
    }
}

function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    // Créer un élément pour afficher la progression
    const fileId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const fileElement = createFileElement(file, fileId);
    document.getElementById('fileList').appendChild(fileElement);

    // Envoyer le fichier
    fetch('{{ route("settings.file.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFileElement(fileId, data.file);
            uploadedFiles.push(data.file);
        } else {
            showError(fileId, data.message || 'Erreur lors de l\'upload');
        }
    })
    .catch(error => {
        showError(fileId, 'Erreur réseau: ' + error.message);
    });
}

function createFileElement(file, fileId) {
    const div = document.createElement('div');
    div.className = 'col-md-4 col-sm-6';
    div.id = 'file_' + fileId;
    
    const fileIcon = getFileIcon(file.type);
    const fileSize = formatFileSize(file.size);
    
    div.innerHTML = `
        <div class="file-item">
            <div class="text-center">
                <div class="file-icon ${fileIcon.class}">
                    <i class="${fileIcon.icon}"></i>
                </div>
                <h6 class="mb-1">${file.name}</h6>
                <p class="text-muted mb-2">${fileSize}</p>
                <div class="upload-progress">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 100%">
                        </div>
                    </div>
                    <small class="text-muted">Upload en cours...</small>
                </div>
            </div>
        </div>
    `;
    
    return div;
}

function updateFileElement(fileId, fileData) {
    const element = document.getElementById('file_' + fileId);
    if (element) {
        const fileIcon = getFileIcon(fileData.mime_type);
        element.innerHTML = `
            <div class="file-item">
                <div class="text-center">
                    <div class="file-icon ${fileIcon.class}">
                        <i class="${fileIcon.icon}"></i>
                    </div>
                    <h6 class="mb-1">${fileData.name}</h6>
                    <p class="text-muted mb-2">${formatFileSize(fileData.size)}</p>
                    <div class="file-actions">
                        <button class="btn btn-sm btn-primary me-1" onclick="downloadFile('${fileData.id}')">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteFile('${fileData.id}', '${fileId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
}

function showError(fileId, message) {
    const element = document.getElementById('file_' + fileId);
    if (element) {
        element.innerHTML = `
            <div class="file-item border-danger">
                <div class="text-center">
                    <div class="file-icon text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h6 class="mb-1">Erreur</h6>
                    <p class="text-danger mb-2">${message}</p>
                    <button class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i> Supprimer
                    </button>
                </div>
            </div>
        `;
    }
}

function getFileIcon(mimeType) {
    if (mimeType.startsWith('image/')) {
        return { icon: 'fas fa-image', class: 'text-success' };
    } else if (mimeType.startsWith('video/')) {
        return { icon: 'fas fa-video', class: 'text-danger' };
    } else if (mimeType.includes('pdf')) {
        return { icon: 'fas fa-file-pdf', class: 'text-danger' };
    } else if (mimeType.includes('word') || mimeType.includes('document')) {
        return { icon: 'fas fa-file-word', class: 'text-primary' };
    } else if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) {
        return { icon: 'fas fa-file-excel', class: 'text-success' };
    } else if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) {
        return { icon: 'fas fa-file-powerpoint', class: 'text-warning' };
    } else if (mimeType.includes('zip') || mimeType.includes('rar') || mimeType.includes('tar')) {
        return { icon: 'fas fa-file-archive', class: 'text-info' };
    } else {
        return { icon: 'fas fa-file', class: 'text-secondary' };
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function downloadFile(fileId) {
    window.open('{{ route("settings.file.download") }}/' + fileId, '_blank');
}

function deleteFile(fileId, elementId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')) {
        fetch('{{ route("settings.file.delete") }}/' + fileId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('file_' + elementId).remove();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            alert('Erreur réseau: ' + error.message);
        });
    }
}

function searchFiles() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const fileItems = document.querySelectorAll('.file-item');
    
    fileItems.forEach(item => {
        const fileName = item.querySelector('h6').textContent.toLowerCase();
        if (fileName.includes(searchTerm)) {
            item.parentElement.style.display = 'block';
        } else {
            item.parentElement.style.display = 'none';
        }
    });
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    searchFiles();
}

function filterFiles(type) {
    const fileItems = document.querySelectorAll('.file-item');
    
    fileItems.forEach(item => {
        if (type === 'all') {
            item.parentElement.style.display = 'block';
        } else {
            // Logique de filtrage selon le type
            item.parentElement.style.display = 'block';
        }
    });
}

function refreshFileManager() {
    location.reload();
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Rendre la zone de drop cliquable
    const dropZone = document.getElementById('dropZone');
    dropZone.addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });
});
</script>
@endsection
