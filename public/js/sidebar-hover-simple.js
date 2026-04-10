// Sidebar Compact avec Extension au Survol - Version Simplifiée
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('app-sidebar');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('.header');

    if (!sidebar) {
        console.error('Sidebar non trouvée');
        return;
    }

    // État initial : compact
    let isExpanded = false;
    let hoverTimeout;
    let currentSubmenu = null;

    // Forcer le mode compact au chargement
    sidebar.classList.add('compact');
    updateLayout();

    // Gestion du survol de la sidebar
    sidebar.addEventListener('mouseenter', function() {
        clearTimeout(hoverTimeout);
        if (!isExpanded) {
            expandSidebar();
        }
    });

    sidebar.addEventListener('mouseleave', function(e) {
        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
            if (isExpanded && !isMouseOverSubmenu()) {
                collapseSidebar();
            }
        }, 300);
    });

    // Gestion du survol des menus parents
    const menuParents = sidebar.querySelectorAll('.menu-parent');
    menuParents.forEach(menuParent => {
        menuParent.addEventListener('mouseenter', function() {
            if (isExpanded) {
                const submenu = menuParent.querySelector('.submenu');
                if (submenu) {
                    showSubmenu(menuParent);
                }
            }
        });

        menuParent.addEventListener('mouseleave', function() {
            if (!isMouseOverSubmenu()) {
                hideCurrentSubmenu();
            }
        });
    });

    function isMouseOverSubmenu() {
        return currentSubmenu && currentSubmenu.matches(':hover');
    }

    function expandSidebar() {
        isExpanded = true;
        sidebar.classList.remove('compact');
        sidebar.classList.add('expanded');
        updateLayout();

        // Afficher les textes
        const spans = sidebar.querySelectorAll('.menu-parent span');
        spans.forEach(span => {
            span.style.display = 'inline';
        });

        // Réaligner les icônes
        const icons = sidebar.querySelectorAll('.menu-parent i');
        icons.forEach(icon => {
            icon.style.marginRight = '12px';
        });
    }

    function collapseSidebar() {
        isExpanded = false;
        sidebar.classList.add('compact');
        sidebar.classList.remove('expanded');
        updateLayout();
        hideCurrentSubmenu();

        // Cacher les textes
        const spans = sidebar.querySelectorAll('.menu-parent span');
        spans.forEach(span => {
            span.style.display = 'none';
        });

        // Centrer les icônes
        const icons = sidebar.querySelectorAll('.menu-parent i');
        icons.forEach(icon => {
            icon.style.marginRight = '0';
        });
    }

    function updateLayout() {
        const sidebarWidth = isExpanded ? 280 : 72;

        if (mainContent) {
            mainContent.style.marginLeft = sidebarWidth + 'px';
            mainContent.style.width = `calc(100% - ${sidebarWidth}px)`;
        }

        if (header) {
            header.style.left = sidebarWidth + 'px';
            header.style.width = `calc(100% - ${sidebarWidth}px)`;
        }

        // Mettre à jour les variables CSS
        document.documentElement.style.setProperty('--sidebar-width', sidebarWidth + 'px');
        document.documentElement.style.setProperty('--content-width', `calc(100% - ${sidebarWidth}px)`);
    }

    function showSubmenu(menuParent) {
        hideCurrentSubmenu();

        const submenu = menuParent.querySelector('.submenu');
        if (!submenu) return;

        const menuItemRect = menuParent.getBoundingClientRect();
        const sidebarWidth = sidebar.offsetWidth;

        // Créer un sous-menu flottant
        const floatingSubmenu = document.createElement('div');
        floatingSubmenu.className = 'floating-submenu';
        floatingSubmenu.innerHTML = submenu.innerHTML;

        // Positionner le sous-menu
        floatingSubmenu.style.cssText = `
            position: fixed;
            left: ${sidebarWidth + 10}px;
            top: ${menuItemRect.top}px;
            width: 200px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.2s ease;
            max-height: 300px;
            overflow-y: auto;
        `;

        document.body.appendChild(floatingSubmenu);
        currentSubmenu = floatingSubmenu;

        // Animation d'entrée
        setTimeout(() => {
            floatingSubmenu.style.opacity = '1';
            floatingSubmenu.style.transform = 'translateX(0)';
        }, 10);

        // Gestion du survol du sous-menu
        floatingSubmenu.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
        });

        floatingSubmenu.addEventListener('mouseleave', function() {
            clearTimeout(hoverTimeout);
            hoverTimeout = setTimeout(() => {
                hideCurrentSubmenu();
            }, 100);
        });
    }

    function hideCurrentSubmenu() {
        if (currentSubmenu) {
            currentSubmenu.style.opacity = '0';
            currentSubmenu.style.transform = 'translateX(-10px)';
            setTimeout(() => {
                if (currentSubmenu && currentSubmenu.parentNode) {
                    currentSubmenu.parentNode.removeChild(currentSubmenu);
                }
            }, 200);
            currentSubmenu = null;
        }
    }

    // Gestion du redimensionnement
    window.addEventListener('resize', function() {
        updateLayout();
        hideCurrentSubmenu();
    });

    // Support du clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCurrentSubmenu();
            if (isExpanded) {
                collapseSidebar();
            }
        }
    });

    console.log('Sidebar hover expand initialisé');
});
