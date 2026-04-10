// KENAM Hikvision PWA Application
class HikvisionPWA {
    constructor() {
        this.stream = null;
        this.capturedPhoto = null;
        this.employees = [];
        this.photos = [];
        this.db = null;
        this.isOnline = navigator.onLine;

        this.init();
    }

    async init() {
        await this.initServiceWorker();
        await this.initDatabase();
        await this.loadEmployees();
        this.setupEventListeners();
        this.updateConnectionStatus();
        this.loadStoredPhotos();
        this.setupInstallPrompt();
    }

    // Service Worker Initialization
    async initServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/hikvision-pwa/sw.js');
                console.log('Service Worker registered:', registration);

                // Background sync
                if ('sync' in registration) {
                    this.registration = registration;
                }
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }

    // IndexedDB Initialization
    async initDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('KenamHikvisionDB', 1);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve();
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                if (!db.objectStoreNames.contains('photos')) {
                    const photoStore = db.createObjectStore('photos', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    photoStore.createIndex('employeeId', 'employeeId', { unique: false });
                    photoStore.createIndex('timestamp', 'timestamp', { unique: false });
                    photoStore.createIndex('synced', 'synced', { unique: false });
                    photoStore.createIndex('isPreRegistration', 'isPreRegistration', { unique: false });
                }

                if (!db.objectStoreNames.contains('employees')) {
                    const employeeStore = db.createObjectStore('employees', {
                        keyPath: 'id'
                    });
                    employeeStore.createIndex('name', 'name', { unique: false });
                }
            };
        });
    }

    // Load Employees
    async loadEmployees() {
        try {
            const response = await fetch('/api/employees');
            if (response.ok) {
                this.employees = await response.json();
                this.populateEmployeeSelect();
                this.cacheEmployees();
            }
        } catch (error) {
            console.error('Failed to load employees:', error);
            this.loadCachedEmployees();
        }
    }

    // Cache Employees
    async cacheEmployees() {
        if (!this.db) return;

        const transaction = this.db.transaction(['employees'], 'readwrite');
        const store = transaction.objectStore('employees');

        // Clear existing
        await store.clear();

        // Add current employees
        for (const employee of this.employees) {
            await store.add(employee);
        }
    }

    // Load Cached Employees
    async loadCachedEmployees() {
        if (!this.db) return;

        const transaction = this.db.transaction(['employees'], 'readonly');
        const store = transaction.objectStore('employees');
        const request = store.getAll();

        request.onsuccess = () => {
            this.employees = request.result;
            this.populateEmployeeSelect();
        };
    }

    // Populate Employee Select
    populateEmployeeSelect() {
        const select = document.getElementById('employee-select');
        select.innerHTML = '<option value="">Choisir un employé...</option>';

        this.employees.forEach(employee => {
            const option = document.createElement('option');
            option.value = employee.id;
            option.textContent = `${employee.name} - ${employee.employee_id || employee.id}`;
            select.appendChild(option);
        });
    }

    // Setup Event Listeners
    setupEventListeners() {
        // Camera controls
        document.getElementById('start-camera').addEventListener('click', () => this.startCamera());
        document.getElementById('capture-photo').addEventListener('click', () => this.capturePhoto());
        document.getElementById('retake-photo').addEventListener('click', () => this.retakePhoto());
        document.getElementById('sync-photo').addEventListener('click', () => this.syncPhoto());

        // Employee selection
        document.getElementById('employee-select').addEventListener('change', (e) => {
            const value = e.target.value;
            const preRegInfo = document.getElementById('pre-registration-info');

            if (value === '') {
                // Pré-enregistrement mode
                preRegInfo.style.display = 'block';
                document.getElementById('capture-photo').disabled = false;
                this.showToast('Mode pré-enregistrement activé', 'info');
            } else {
                // Employee selected
                preRegInfo.style.display = 'none';
                document.getElementById('capture-photo').disabled = false;
            }
        });

        // Connection status
        window.addEventListener('online', () => this.updateConnectionStatus());
        window.addEventListener('offline', () => this.updateConnectionStatus());
    }

    // Start Camera
    async startCamera() {
        try {
            const video = document.getElementById('video-preview');
            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = this.stream;
            video.style.display = 'block';

            document.getElementById('start-camera').disabled = true;
            // Toujours autoriser la capture (pré-enregistrement possible)
            document.getElementById('capture-photo').disabled = false;

            this.showToast('Caméra démarrée', 'success');
        } catch (error) {
            console.error('Camera error:', error);
            this.showToast('Erreur d\'accès à la caméra', 'error');
        }
    }

    // Capture Photo
    capturePhoto() {
        const video = document.getElementById('video-preview');
        const canvas = document.getElementById('photo-canvas');
        const context = canvas.getContext('2d');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);

        // Convert to blob
        canvas.toBlob((blob) => {
            this.capturedPhoto = blob;

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById('captured-photo');
                img.src = e.target.result;

                video.style.display = 'none';
                document.getElementById('photo-preview').style.display = 'block';
                document.getElementById('capture-photo').style.display = 'none';
                document.getElementById('retake-photo').style.display = 'inline-block';
                document.getElementById('sync-photo').disabled = false;
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.8);

        this.showToast('Photo capturée', 'success');
    }

    // Retake Photo
    retakePhoto() {
        document.getElementById('video-preview').style.display = 'block';
        document.getElementById('photo-preview').style.display = 'none';
        document.getElementById('capture-photo').style.display = 'inline-block';
        document.getElementById('retake-photo').style.display = 'none';
        document.getElementById('sync-photo').disabled = true;

        this.capturedPhoto = null;
    }

    // Sync Photo
    async syncPhoto() {
        if (!this.capturedPhoto) return;

        const employeeId = document.getElementById('employee-select').value;
        const tempName = document.getElementById('temp-name').value;
        const tempNotes = document.getElementById('temp-notes').value;

        // Validation pour pré-enregistrement
        if (!employeeId && !tempName) {
            this.showToast('Veuillez entrer un nom temporaire pour le pré-enregistrement', 'warning');
            return;
        }

        const photoData = {
            employeeId: employeeId || null,
            tempName: tempName || 'Pré-enregistrement',
            tempNotes: tempNotes || '',
            timestamp: new Date().toISOString(),
            data: this.capturedPhoto,
            synced: false,
            isPreRegistration: !employeeId
        };

        // Store photo locally first
        await this.storePhoto(photoData);

        // Try to sync immediately if online
        if (this.isOnline) {
            await this.uploadPhoto(photoData);
        } else {
            const message = photoData.isPreRegistration
                ? 'Pré-enregistrement enregistré localement. Synchronisation lors de la reconnexion.'
                : 'Photo enregistrée localement. Synchronisation lors de la reconnexion.';
            this.showToast(message, 'info');
            this.registerBackgroundSync();
        }

        // Update UI
        this.updatePhotoStats();
        this.addPhotoToGallery(photoData);
        this.resetCamera();
    }

    // Store Photo in IndexedDB
    async storePhoto(photoData) {
        if (!this.db) return;

        const transaction = this.db.transaction(['photos'], 'readwrite');
        const store = transaction.objectStore('photos');

        return new Promise((resolve, reject) => {
            const request = store.add(photoData);
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    // Upload Photo to Server
    async uploadPhoto(photoData) {
        const formData = new FormData();
        formData.append('photo', photoData.data);
        formData.append('employeeId', photoData.employeeId);
        formData.append('timestamp', photoData.timestamp);

        try {
            const response = await fetch('/api/hikvision/upload-photo', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                await this.markPhotoAsSynced(photoData.id);
                this.showToast('Photo synchronisée avec succès', 'success');
            } else {
                throw new Error('Upload failed');
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showToast('Erreur de synchronisation', 'error');
        }
    }

    // Mark Photo as Synced
    async markPhotoAsSynced(photoId) {
        if (!this.db) return;

        const transaction = this.db.transaction(['photos'], 'readwrite');
        const store = transaction.objectStore('photos');

        return new Promise((resolve, reject) => {
            const request = store.get(photoId);
            request.onsuccess = () => {
                const photo = request.result;
                photo.synced = true;
                const updateRequest = store.put(photo);
                updateRequest.onsuccess = () => resolve();
                updateRequest.onerror = () => reject(updateRequest.error);
            };
            request.onerror = () => reject(request.error);
        });
    }

    // Load Stored Photos
    async loadStoredPhotos() {
        if (!this.db) return;

        const transaction = this.db.transaction(['photos'], 'readonly');
        const store = transaction.objectStore('photos');
        const request = store.getAll();

        request.onsuccess = () => {
            this.photos = request.result;
            this.updatePhotoStats();
            this.displayRecentPhotos();
        };
    }

    // Update Photo Statistics
    updatePhotoStats() {
        const total = this.photos.length;
        const synced = this.photos.filter(p => p.synced).length;
        const pending = total - synced;

        document.getElementById('total-photos').textContent = total;
        document.getElementById('synced-photos').textContent = synced;
        document.getElementById('pending-photos').textContent = pending;

        const percentage = total > 0 ? Math.round((synced / total) * 100) : 0;
        const progressBar = document.getElementById('sync-progress');
        progressBar.style.width = `${percentage}%`;
        progressBar.textContent = `${percentage}%`;
    }

    // Display Recent Photos
    displayRecentPhotos() {
        const container = document.getElementById('recent-photos');
        container.innerHTML = '';

        const recentPhotos = this.photos
            .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
            .slice(0, 6);

        recentPhotos.forEach(photo => {
            this.addPhotoToGallery(photo);
        });
    }

    // Add Photo to Gallery
    addPhotoToGallery(photoData) {
        const container = document.getElementById('recent-photos');
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4';

        let displayName = 'Inconnu';
        if (photoData.isPreRegistration) {
            displayName = photoData.tempName || 'Pré-enregistrement';
        } else {
            const employee = this.employees.find(e => e.id == photoData.employeeId);
            displayName = employee ? employee.name : 'Inconnu';
        }

        col.innerHTML = `
            <div class="photo-item fade-in">
                <img src="${this.getPhotoUrl(photoData)}" alt="Photo de ${displayName}">
                <div class="photo-overlay">
                    <div>${displayName}</div>
                    <small>${new Date(photoData.timestamp).toLocaleString()}</small>
                    ${photoData.tempNotes ? `<div class="text-warning"><small>${photoData.tempNotes}</small></div>` : ''}
                </div>
                <div class="photo-status ${photoData.synced ? 'synced' : 'pending'}">
                    ${photoData.isPreRegistration ? '⏸' : (photoData.synced ? '✓' : '⏳')}
                </div>
            </div>
        `;

        container.insertBefore(col, container.firstChild);

        // Limit to 6 photos
        while (container.children.length > 6) {
            container.removeChild(container.lastChild);
        }
    }

    // Get Photo URL
    getPhotoUrl(photoData) {
        if (photoData.data instanceof Blob) {
            return URL.createObjectURL(photoData.data);
        }
        return '/placeholder-photo.jpg';
    }

    // Reset Camera
    resetCamera() {
        document.getElementById('video-preview').style.display = 'none';
        document.getElementById('photo-preview').style.display = 'none';
        document.getElementById('capture-photo').style.display = 'inline-block';
        document.getElementById('retake-photo').style.display = 'none';
        document.getElementById('sync-photo').disabled = true;
        document.getElementById('employee-select').value = '';
        document.getElementById('pre-registration-info').style.display = 'none';
        document.getElementById('temp-name').value = '';
        document.getElementById('temp-notes').value = '';

        this.capturedPhoto = null;
    }

    // Update Connection Status
    updateConnectionStatus() {
        this.isOnline = navigator.onLine;
        const statusElement = document.getElementById('connection-status');

        if (this.isOnline) {
            statusElement.className = 'badge bg-success me-2';
            statusElement.innerHTML = '<i class="fas fa-wifi me-1"></i>En ligne';
            this.syncPendingPhotos();
        } else {
            statusElement.className = 'badge bg-danger me-2';
            statusElement.innerHTML = '<i class="fas fa-wifi-slash me-1"></i>Hors ligne';
        }
    }

    // Sync Pending Photos
    async syncPendingPhotos() {
        const pendingPhotos = this.photos.filter(p => !p.synced);

        for (const photo of pendingPhotos) {
            await this.uploadPhoto(photo);
        }

        this.updatePhotoStats();
        this.displayRecentPhotos();
    }

    // Register Background Sync
    registerBackgroundSync() {
        if (this.registration && 'sync' in this.registration) {
            this.registration.sync.register('sync-photos');
        }
    }

    // Setup Install Prompt
    setupInstallPrompt() {
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            const installBtn = document.getElementById('install-btn');
            installBtn.classList.remove('d-none');

            installBtn.addEventListener('click', () => {
                installBtn.style.display = 'none';
                deferredPrompt.prompt();

                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        this.showToast('Application installée avec succès', 'success');
                    }
                    deferredPrompt = null;
                });
            });
        });
    }

    // Show Toast
    showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        const toastHeader = toast.querySelector('.toast-header i');

        toastMessage.textContent = message;

        // Update icon based on type
        toastHeader.className = `fas me-2 text-${type === 'error' ? 'danger' : type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'info'}`;

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
}

// Initialize Application
document.addEventListener('DOMContentLoaded', () => {
    new HikvisionPWA();
});

// Handle visibility change
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        // App became visible, check for updates
        if (navigator.serviceWorker) {
            navigator.serviceWorker.getRegistration().then(registration => {
                if (registration) {
                    registration.update();
                }
            });
        }
    }
});
