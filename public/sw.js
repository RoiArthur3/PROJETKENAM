const CACHE_NAME = 'kenam-ops-v1';
const urlsToCache = [
    '/',
    '/mobile/terrain',
    '/mobile/terrain/create',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// Installation du Service Worker
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Service Worker: Mise en cache des fichiers');
                return cache.addAll(urlsToCache);
            })
    );
});

// Activation du Service Worker
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Suppression de l\'ancien cache');
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Interception des requêtes
self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Si la requête est dans le cache, la retourner
                if (response) {
                    return response;
                }

                // Sinon, faire la requête réseau
                return fetch(event.request).then(function(response) {
                    // Vérifier si la réponse est valide
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }

                    // Cloner la réponse pour la mettre en cache
                    var responseToCache = response.clone();

                    caches.open(CACHE_NAME)
                        .then(function(cache) {
                            cache.put(event.request, responseToCache);
                        });

                    return response;
                });
            })
            .catch(function(error) {
                // Gestion des erreurs hors-ligne
                console.log('Service Worker: Erreur de fetch', error);
                
                // Retourner une page hors-ligne pour les requêtes de page
                if (event.request.destination === 'document') {
                    return caches.match('/mobile/terrain');
                }
            })
    );
});

// Gestion des notifications push
self.addEventListener('push', function(event) {
    const options = {
        body: event.data ? event.data.text() : 'Nouvelle notification Kenam OPS',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Voir',
                icon: '/icons/icon-96x96.png'
            },
            {
                action: 'close',
                title: 'Fermer',
                icon: '/icons/icon-96x96.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('Kenam OPS', options)
    );
});

// Gestion des clics sur notifications
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/mobile/terrain')
        );
    } else if (event.action === 'close') {
        // Fermer la notification
    } else {
        // Comportement par défaut
        event.waitUntil(
            clients.openWindow('/mobile/terrain')
        );
    }
});

// Synchronisation en arrière-plan
self.addEventListener('sync', function(event) {
    if (event.tag === 'sync-operations') {
        event.waitUntil(syncOperations());
    }
});

// Fonction de synchronisation
function syncOperations() {
    return fetch('/api/mobile/sync')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            // Traiter les données synchronisées
            console.log('Données synchronisées:', data);
            
            // Envoyer une notification de synchronisation réussie
            self.registration.showNotification('Synchronisation', {
                body: 'Vos données ont été synchronisées',
                icon: '/icons/icon-192x192.png'
            });
        })
        .catch(function(error) {
            console.error('Erreur de synchronisation:', error);
        });
}
