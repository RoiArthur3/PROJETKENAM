// Service Worker pour KENAM Hikvision PWA
const CACHE_NAME = 'kenam-hikvision-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/styles.css',
  '/app.js',
  '/manifest.json'
];

// Installation du service worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Cache ouvert');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activation du service worker
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('Service Worker: Ancien cache supprimé');
            return caches.delete(cache);
          }
        })
      );
    })
  );
});

// Interception des requêtes
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return response
        if (response) {
          return response;
        }

        // Clone request
        const fetchRequest = event.request.clone();

        return fetch(fetchRequest).then(
          response => {
            // Check if valid response
            if(!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone response
            const responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        );
      })
  );
});

// Background sync pour les photos hors ligne
self.addEventListener('sync', event => {
  if (event.tag === 'sync-photos') {
    event.waitUntil(syncPhotos());
  }
});

// Synchronisation des photos
async function syncPhotos() {
  try {
    const photos = await getStoredPhotos();
    
    for (const photo of photos) {
      try {
        await uploadPhotoToHikvision(photo);
        await removeStoredPhoto(photo.id);
      } catch (error) {
        console.error('Erreur upload photo:', error);
      }
    }
  } catch (error) {
    console.error('Erreur sync photos:', error);
  }
}

// Récupérer les photos stockées
async function getStoredPhotos() {
  return new Promise((resolve) => {
    const request = indexedDB.open('KenamHikvisionDB', 1);
    
    request.onsuccess = (event) => {
      const db = event.target.result;
      const transaction = db.transaction(['photos'], 'readonly');
      const store = transaction.objectStore('photos');
      const getAllRequest = store.getAll();
      
      getAllRequest.onsuccess = () => resolve(getAllRequest.result);
      getAllRequest.onerror = () => resolve([]);
    };
    
    request.onerror = () => resolve([]);
  });
}

// Upload photo vers Hikvision
async function uploadPhotoToHikvision(photo) {
  const formData = new FormData();
  formData.append('photo', photo.data);
  formData.append('employeeId', photo.employeeId);
  formData.append('timestamp', photo.timestamp);
  
  const response = await fetch('/api/hikvision/upload-photo', {
    method: 'POST',
    body: formData
  });
  
  if (!response.ok) {
    throw new Error('Upload failed');
  }
  
  return response.json();
}

// Supprimer photo stockée
async function removeStoredPhoto(photoId) {
  return new Promise((resolve) => {
    const request = indexedDB.open('KenamHikvisionDB', 1);
    
    request.onsuccess = (event) => {
      const db = event.target.result;
      const transaction = db.transaction(['photos'], 'readwrite');
      const store = transaction.objectStore('photos');
      const deleteRequest = store.delete(photoId);
      
      deleteRequest.onsuccess = () => resolve();
      deleteRequest.onerror = () => resolve();
    };
    
    request.onerror = () => resolve();
  });
}
