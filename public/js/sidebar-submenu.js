// Sidebar avec Sous-Modules Flottants
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
    updateLayout();

    // Gestion du survol de la sidebar
    sidebar.addEventListener('mouseenter', function() {
        clearTimeout(hoverTimeout);
        if (!isExpanded) {
            expandSidebar();
        }
    });

    sidebar.addEventListener('mouseleave', function() {
        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
            if (isExpanded) {
                collapseSidebar();
            }
        }, 300);
    });

    // Gestion des sous-modules
    const menuItems = document.querySelectorAll('.menu-item.has-submenu');

    menuItems.forEach(item => {
        const moduleName = item.dataset.module;
        const submenu = document.getElementById(`submenu-${moduleName}`);

        if (!submenu) return;

        // Ouvrir/fixer au clic et conserver jusqu'au clic sur un autre module
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (currentSubmenu === submenu && submenu.classList.contains('show')) {
                return;
            }

            showSubmenu(submenu, item);
        });

        // Clic sur les liens du sous-menu
        const submenuLinks = submenu.querySelectorAll('.submenu-link');
        submenuLinks.forEach(link => {
            // Toujours ouvrir les sous-modules dans l'onglet courant.
            link.setAttribute('target', '_self');

            link.addEventListener('click', function(e) {
                // Empêcher l'ouverture dans un nouvel onglet via Ctrl/Cmd/Shift/middle-click.
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) {
                    e.preventDefault();
                    window.location.assign(link.href);
                    return;
                }

                // Ne pas fermer automatiquement ici : la navigation va se faire
                // et le sous-menu doit rester fixe jusqu'au clic d'un autre module.
            });
        });
    });

    function showSubmenu(submenu, menuItem) {
        // Cacher le sous-menu précédent
        if (currentSubmenu && currentSubmenu !== submenu) {
            hideSubmenu(currentSubmenu);
        }

        currentSubmenu = submenu;

        // Rendre visible pour mesurer la taille réelle (utile pour les grilles larges)
        submenu.classList.add('show');

        // Calculer la position
        const sidebarRect = sidebar.getBoundingClientRect();
        const menuItemRect = menuItem.getBoundingClientRect();

        const measuredRect = submenu.getBoundingClientRect();
        const submenuWidth = Math.max(220, measuredRect.width || submenu.offsetWidth || 220);
        const submenuHeight = Math.max(100, measuredRect.height || submenu.offsetHeight || 100);

        // Positionner le sous-menu à droite de la sidebar
        const submenuX = sidebarRect.right + 10;
        const submenuY = menuItemRect.top;

        // Vérifier si le sous-menu dépasse de l'écran
        const maxX = window.innerWidth - submenuWidth - 20;
        const finalX = Math.min(submenuX, maxX);

        const maxY = window.innerHeight - submenuHeight - 20;
        const finalY = Math.max(10, Math.min(submenuY, maxY));

        // Positionner le sous-menu
        submenu.style.left = finalX + 'px';
        submenu.style.top = finalY + 'px';
    }

    function hideSubmenu(submenu) {
        submenu.classList.remove('show');
        if (currentSubmenu === submenu) {
            currentSubmenu = null;
        }
    }

    function expandSidebar() {
        isExpanded = true;
        sidebar.classList.add('expanded');
        updateLayout();
    }

    function collapseSidebar() {
        isExpanded = false;
        sidebar.classList.remove('expanded');
        updateLayout();
    }

    function hideAllSubmenus() {
        const submenus = document.querySelectorAll('.floating-submenu');
        submenus.forEach(submenu => {
            hideSubmenu(submenu);
        });
    }

    function updateLayout() {
        const sidebarWidth = isExpanded ? 300 : 90;

        if (mainContent) {
            mainContent.style.marginLeft = sidebarWidth + 'px';
            mainContent.style.width = `calc(100% - ${sidebarWidth}px)`;
        }

        if (header) {
            header.style.left = sidebarWidth + 'px';
            header.style.width = `calc(100% - ${sidebarWidth}px)`;
        }
    }

    // Gestion du redimensionnement
    window.addEventListener('resize', function() {
        updateLayout();
        // Repositionner le sous-menu courant sans le fermer.
        if (currentSubmenu && currentSubmenu.classList.contains('show')) {
            const activeMenuItem = document.querySelector('.menu-item.has-submenu[data-module="' + currentSubmenu.id.replace('submenu-', '') + '"]');
            if (activeMenuItem) {
                showSubmenu(currentSubmenu, activeMenuItem);
            }
        }
    });

    // Support du clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAllSubmenus();
        }
    });

    // Comportement voulu : les sous-modules restent affichés jusqu'au clic d'un autre module.

    console.log('Sidebar avec sous-modules flottants initialisé');
});
