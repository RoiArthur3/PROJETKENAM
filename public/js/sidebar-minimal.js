// Sidebar Minimalist Mode
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('app-sidebar');
    const menuItems = sidebar.querySelectorAll('.menu-item, .menu-parent');

    // Toujours en mode compact
    sidebar.classList.add('compact');

    // Afficher les labels au survol prolongé
    let hoverTimeout;

    menuItems.forEach(item => {
        const text = item.querySelector('span')?.textContent || item.textContent.trim();
        if (text && text !== '') {
            item.setAttribute('data-tooltip', text);
        }

        item.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
            hoverTimeout = setTimeout(() => {
                showExpandedTooltip(item, text);
            }, 800);
        });

        item.addEventListener('mouseleave', function() {
            clearTimeout(hoverTimeout);
            hideExpandedTooltip();
        });
    });

    function showExpandedTooltip(item, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'expanded-tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: fixed;
            left: 80px;
            top: ${item.getBoundingClientRect().top}px;
            background: linear-gradient(135deg, #1e7e34, #28a745);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        `;

        document.body.appendChild(tooltip);

        setTimeout(() => {
            tooltip.style.opacity = '1';
            tooltip.style.transform = 'translateX(0)';
        }, 10);
    }

    function hideExpandedTooltip() {
        const tooltip = document.querySelector('.expanded-tooltip');
        if (tooltip) {
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translateX(-10px)';
            setTimeout(() => tooltip.remove(), 300);
        }
    }
});
