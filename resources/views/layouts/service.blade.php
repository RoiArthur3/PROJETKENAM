@extends('layouts.app')

@push('styles')
<style>
    /* Adapter le contenu pour les services */
    .service-content {
        margin-left: 280px;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .service-content {
            margin-left: 0;
            padding: 10px;
        }
    }

    /* Header noir pour services - même style que le dashboard principal */
    .navbar-custom {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
        border-bottom: 4px solid #000000 !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
    }

    .nav-button {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 2px solid rgba(255, 255, 255, 0.25) !important;
        color: white !important;
        padding: 10px 18px !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        margin: 0 6px !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .nav-button:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6) !important;
    }
</style>
@endpush

@section('content')
<div class="service-content">
    @yield('service_content')
</div>

@push('scripts')
<script>
    // Adapter le sidebar pour les services
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter le menu service dans le sidebar
        const sidebar = document.querySelector('#app-sidebar');
        if (sidebar && !document.querySelector('.service-menu-section')) {
            const serviceMenu = document.createElement('div');
            serviceMenu.className = 'menu-section service-menu-section';
            serviceMenu.innerHTML = `
                <button class="menu-parent toggle" data-target="#sm-service" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:transparent;border:none;color:#166534;font-weight:600;border-bottom:1px solid #dcfce7;cursor:pointer;">
                    <div style="display:flex;align-items:center;">
                        <i class="fas fa-building" style="width:20px;margin-right:12px;color:#16a34a;"></i>
                        <span>Service</span>
                    </div>
                    <i class="fas fa-chevron-right" style="transition:transform .3s;"></i>
                </button>
                <div id="sm-service" class="submenu" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">
                    <a href="{{ route('services.dashboard') }}" class="submenu-link">Dashboard</a>
                    <a href="{{ route('services.profile') }}" class="submenu-link">Profil</a>
                </div>
            `;

            // Insérer avant le dernier menu-section
            const lastSection = sidebar.querySelector('.menu-section:last-child');
            if (lastSection) {
                sidebar.insertBefore(serviceMenu, lastSection);
            } else {
                sidebar.appendChild(serviceMenu);
            }

            // Activer le menu service
            const serviceToggle = serviceMenu.querySelector('.toggle');
            const serviceSubmenu = document.getElementById('sm-service');
            serviceToggle.classList.add('menu-active');
            serviceSubmenu.classList.add('open');
            serviceToggle.querySelector('.fa-chevron-right').style.transform = 'rotate(90deg)';
        }
    });
</script>
@endpush
