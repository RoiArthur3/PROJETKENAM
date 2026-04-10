// Sidebar Overlay Mode - Version Améliorée
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('app-sidebar');
    const mainContent = document.querySelector('.main-content');

    // Créer l'overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Trouver ou créer le header
    const header = document.querySelector('.header') || document.querySelector('.navbar-custom');

    // Créer le bouton burger dans le header
    const burgerBtn = document.createElement('button');
    burgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
    burgerBtn.className = 'header-burger-btn';
    burgerBtn.setAttribute('aria-label', 'Toggle menu');
    burgerBtn.setAttribute('title', 'Ouvrir le menu');

    // Insérer le burger au début du header
    if (header) {
        header.insertBefore(burgerBtn, header.firstChild);
    }

    // État de la sidebar
    let isOpen = false;

    // Fonction toggle
    function toggleSidebar() {
        isOpen = !isOpen;

        if (isOpen) {
            // Ouvrir la sidebar
            sidebar.classList.add('active');
            overlay.classList.add('active');
            burgerBtn.setAttribute('title', 'Fermer le menu');

            // Empêcher le scroll du body
            document.body.style.overflow = 'hidden';

            // Focus sur le premier élément du menu
            setTimeout(() => {
                const firstMenuItem = sidebar.querySelector('.menu-item, .menu-parent');
                if (firstMenuItem) {
                    firstMenuItem.focus();
                }
            }, 300);
        } else {
            // Fermer la sidebar
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            burgerBtn.setAttribute('title', 'Ouvrir le menu');

            // Réactiver le scroll du body
            document.body.style.overflow = '';
        }
    }

    // Événements
    burgerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
    });

    overlay.addEventListener('click', function() {
        if (isOpen) {
            toggleSidebar();
        }
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            toggleSidebar();
        }

        // Raccourci Ctrl/Cmd + B
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
    });

    // Fermer quand on clique sur un lien dans la sidebar (mobile)
    const menuLinks = sidebar.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768 && isOpen) {
                setTimeout(() => {
                    toggleSidebar();
                }, 300);
            }
        });
    });

    // Gestion du redimensionnement
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (window.innerWidth > 768 && isOpen) {
                // Garder la sidebar ouverte sur desktop
                // ou la fermer selon vos préférences
                // toggleSidebar();
            }
        }, 250);
    });

    // Animation d'entrée
    setTimeout(function() {
        burgerBtn.style.opacity = '1';
        burgerBtn.style.transform = 'scale(1)';
    }, 500);

    // Accessibilité : gérer le focus trap
    function handleFocusTrap(e) {
        if (!isOpen) return;

        const focusableElements = sidebar.querySelectorAll(
            'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (e.key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }
    }

    document.addEventListener('keydown', handleFocusTrap);

    // Export pour utilisation globale
    window.toggleSidebar = toggleSidebar;
    window.closeSidebar = function() {
        if (isOpen) {
            toggleSidebar();
        }
    };

    // Initialiser l'état
    burgerBtn.style.opacity = '0';
    burgerBtn.style.transform = 'scale(0.8)';
    burgerBtn.style.transition = 'all 0.3s ease';
});
