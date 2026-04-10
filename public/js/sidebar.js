document.addEventListener('DOMContentLoaded', function() {
    // Tooltips (safe if Bootstrap unavailable)
    try {
        if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
            const tooltipTriggerList = Array.prototype.slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
        }
    } catch (_) { /* ignore tooltip init errors */ }

    const sidebar = document.querySelector('.sidebar');
    const sidebarMenu = document.getElementById('sidebarMenu');

    // Compact toggle support
    const compactToggle = document.getElementById('compactToggle');
    const applyCompact = (on) => {
        if (!sidebar) return;
        sidebar.classList.toggle('compact', !!on);
        localStorage.setItem('sidebarCompact', !!on);
    };
    if (compactToggle) {
        compactToggle.addEventListener('click', (e) => {
            e.preventDefault();
            applyCompact(!sidebar.classList.contains('compact'));
        });
        // Restore compact state
        if (localStorage.getItem('sidebarCompact') === 'true') {
            applyCompact(true);
        }
    }

    // Sections open/close handling
    const restoreOpenSections = () => {
        try {
            const stored = JSON.parse(localStorage.getItem('openSections') || '[]');
            document.querySelectorAll('.menu-section').forEach((section, idx) => {
                const submenu = section.querySelector('.submenu');
                if (!submenu) return;
                if (stored.includes(idx)) {
                    submenu.classList.add('show');
                    const header = section.querySelector('.menu-section-header[data-toggle="submenu"]');
                    if (header) header.classList.add('open');
                    if (header) header.setAttribute('aria-expanded', 'true');
                }
            });
        } catch (_) {}
    };
    const saveOpenSections = () => {
        const openIdx = [];
        document.querySelectorAll('.menu-section').forEach((section, idx) => {
            const submenu = section.querySelector('.submenu');
            if (submenu && submenu.classList.contains('show')) openIdx.push(idx);
        });
        localStorage.setItem('openSections', JSON.stringify(openIdx));
    };

    // Delegated click to ensure it works regardless of dynamic content
    if (sidebarMenu) {
        sidebarMenu.addEventListener('click', (e) => {
            const hdr = e.target.closest('.menu-section-header[data-toggle="submenu"]');
            if (!hdr) return;
            e.preventDefault();
            e.stopPropagation();
            const section = hdr.closest('.menu-section');
            const submenu = section ? section.querySelector('.submenu') : null;
            if (!submenu) return;

            const isOpen = submenu.classList.contains('show');
            // Close all other sections
            document.querySelectorAll('.menu-section .submenu.show').forEach(sm => {
                if (sm !== submenu) {
                    sm.classList.remove('show');
                    const h = sm.closest('.menu-section').querySelector('.menu-section-header[data-toggle="submenu"]');
                    if (h) {
                        h.classList.remove('open');
                        h.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            // Toggle current
            if (isOpen) {
                submenu.classList.remove('show');
                submenu.style.display = 'none';
                hdr.classList.remove('open');
                hdr.setAttribute('aria-expanded', 'false');
            } else {
                submenu.classList.add('show');
                submenu.style.display = 'block';
                hdr.classList.add('open');
                hdr.setAttribute('aria-expanded', 'true');
            }

            saveOpenSections();
        });
    }

    // Hover-to-open for desktop screens - Modified for current menu structure
    const isDesktop = () => window.innerWidth >= 992;
    const openSection = (section) => {
        const submenu = section.querySelector('.submenu');
        const toggle = section.querySelector('.toggle');
        if (!submenu || !toggle) return;

        // Close others first
        document.querySelectorAll('.menu-section .submenu.open').forEach(sm => {
            if (sm !== submenu) {
                sm.classList.remove('open');
                sm.style.maxHeight = '0';
                const t = sm.closest('.menu-section').querySelector('.toggle');
                if (t) {
                    t.classList.remove('menu-active');
                    const chevron = t.querySelector('.fa-chevron-right, .fa-chevron-down');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }
            }
        });

        submenu.classList.add('open');
        submenu.style.maxHeight = '1000px';
        toggle.classList.add('menu-active');
        const chevron = toggle.querySelector('.fa-chevron-right, .fa-chevron-down');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    };

    const closeSection = (section) => {
        const submenu = section.querySelector('.submenu');
        const toggle = section.querySelector('.toggle');
        if (!submenu || !toggle) return;

        submenu.classList.remove('open');
        submenu.style.maxHeight = '0';
        toggle.classList.remove('menu-active');
        const chevron = toggle.querySelector('.fa-chevron-right, .fa-chevron-down');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    };

    let hoverCloseTimer = null;

    // Add hover functionality to menu sections
    document.querySelectorAll('.menu-section').forEach(section => {
        const toggle = section.querySelector('.toggle');
        if (!toggle) return;

        // Mouse enter - open submenu
        section.addEventListener('mouseenter', () => {
            if (!isDesktop()) return;
            if (hoverCloseTimer) {
                clearTimeout(hoverCloseTimer);
                hoverCloseTimer = null;
            }
            openSection(section);
        });

        // Mouse leave - close submenu with delay
        section.addEventListener('mouseleave', () => {
            if (!isDesktop()) return;
            hoverCloseTimer = setTimeout(() => {
                closeSection(section);
            }, 300); // 300ms delay before closing
        });

        // Click functionality for mobile
        toggle.addEventListener('click', (e) => {
            if (isDesktop()) return; // Don't interfere with hover on desktop
            e.preventDefault();
            e.stopPropagation();

            const submenu = section.querySelector('.submenu');
            const isOpen = submenu.classList.contains('open');

            if (isOpen) {
                closeSection(section);
            } else {
                openSection(section);
            }
        });
    });

    // Active link + ensure its section is open
    const links = document.querySelectorAll('.sidebar a.menu-item');
    let activeLink = null;
    const current = window.location.origin + window.location.pathname;
    links.forEach(link => {
        // Match by pathname, ignoring query/hash
        const href = (new URL(link.href)).origin + (new URL(link.href)).pathname;
        if (href === current || link.href === window.location.href) {
            link.classList.add('active');
            activeLink = link;
            const section = link.closest('.menu-section');
            const submenu = section ? section.querySelector('.submenu') : null;
            if (submenu) submenu.classList.add('show');
        }
    });

    // Restore previously open sections and then ensure the active section remains open
    restoreOpenSections();
    if (activeLink) {
        const section = activeLink.closest('.menu-section');
        const submenu = section ? section.querySelector('.submenu') : null;
        if (submenu) {
            submenu.classList.add('show');
            const header = section.querySelector('.menu-section-header[data-toggle="submenu"]');
            if (header) {
                header.classList.add('open');
                header.setAttribute('aria-expanded', 'true');
            }
        }
    }
    saveOpenSections();

    // Auto-scroll to keep active link visible
    if (activeLink && sidebarMenu) {
        const linkTop = activeLink.getBoundingClientRect().top;
        const menuTop = sidebarMenu.getBoundingClientRect().top;
        const delta = linkTop - menuTop - 80; // offset under header
        sidebarMenu.scrollTop += delta;
    }

    // Light icon animation
    document.querySelectorAll('.sidebar a.menu-item i').forEach((icon, idx) => {
        setTimeout(() => {
            icon.style.opacity = '1';
            icon.style.transform = 'scale(1)';
        }, 100 + idx * 40);
    });
});
