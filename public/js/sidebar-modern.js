// Sidebar Moderne KENAM - Version Simplifiée avec Filtre CSS
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
    });

    // Support du clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isExpanded) {
            collapseSidebar();
        }
    });

    console.log('Sidebar moderne avec filtre logo initialisé');
});
