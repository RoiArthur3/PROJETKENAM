// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('app-sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('.header');

    // État initial depuis le cookie
    let isCompact = getCookie('sidebar_state') === 'compact';

    // Appliquer l'état initial
    if (isCompact) {
        sidebar.classList.add('compact');
        updateMainContentMargin();
    }

    // Gestion du click sur le burger
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }

    // Fonction toggle
    function toggleSidebar() {
        isCompact = !isCompact;

        if (isCompact) {
            sidebar.classList.add('compact');
            setCookie('sidebar_state', 'compact', 30);
        } else {
            sidebar.classList.remove('compact');
            setCookie('sidebar_state', 'expanded', 30);
        }

        updateMainContentMargin();

        // Animation de l'icône burger
        const icon = toggleBtn.querySelector('i');
        if (icon) {
            icon.style.transform = isCompact ? 'rotate(90deg)' : 'rotate(0deg)';
        }
    }

    // Mettre à jour les marges du contenu principal
    function updateMainContentMargin() {
        const sidebarWidth = isCompact ? 72 : 280;

        if (mainContent) {
            mainContent.style.marginLeft = sidebarWidth + 'px';
        }

        if (header) {
            header.style.left = sidebarWidth + 'px';
        }

        // Mettre à jour les variables CSS
        document.documentElement.style.setProperty('--sidebar-width', sidebarWidth + 'px');
    }

    // Gestion des tooltips en mode compact
    function addTooltips() {
        if (!isCompact) return;

        const menuItems = sidebar.querySelectorAll('.menu-item, .menu-parent');
        menuItems.forEach(item => {
            const text = item.querySelector('span')?.textContent || item.textContent.trim();
            if (text && text !== '') {
                item.setAttribute('data-tooltip', text);
            }
        });
    }

    // Initialiser les tooltips
    addTooltips();

    // Mettre à jour les tooltips quand on change de mode
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                addTooltips();
            }
        });
    });

    observer.observe(sidebar, { attributes: true });

    // Gestion du redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        updateMainContentMargin();
    });

    // Fonctions utilitaires pour les cookies
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Support des raccourcis clavier
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + B pour toggle la sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
    });

    // Animation au chargement
    setTimeout(function() {
        sidebar.style.opacity = '1';
    }, 100);
});

// Export pour utilisation globale
window.toggleSidebar = function() {
    const event = new Event('click');
    document.getElementById('sidebar-toggle')?.dispatchEvent(event);
};
