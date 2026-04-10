// Dashboard Clickable Cards Script
// Rend toutes les cartes du dashboard principales cliquables

'use strict';

document.addEventListener('DOMContentLoaded', function() {
    // Configuration des routes pour chaque type de carte
    const cardRouteMap = {
        'Trésorerie': '/tresorerie',
        'Comptabilité': '/comptabilite',
        'Opérations': '/operations',
        'Stock': '/entrepots',
        'Magasin': '/magasin',
        'Validations': '/validations',
        'Factures': '/comptabilite/factures',
        'Parc': '/parc',
        'Véhicules': '/vehicules',
        'Audit': '/audit',
        'RH': '/rh',
        'Contrats': '/rh/contrats',
        'Assurances': '/parc',
        'Flux': '/tresorerie/flux',
        'SMS': '/'
    };

    // Sélectionner toutes les cartes
    const cards = document.querySelectorAll('.dashboard-container .card');
    
    cards.forEach(card => {
        // Trouver le titre de la carte
        const title = card.querySelector('h6, .card-title');
        if (title) {
            const titleText = title.textContent.trim();
            
            // Chercher une route correspondante
            let targetRoute = null;
            for (const [keyword, route] of Object.entries(cardRouteMap)) {
                if (titleText.toLowerCase().includes(keyword.toLowerCase())) {
                    targetRoute = route;
                    break;
                }
            }
            
            // Si une route est trouvée, rendre la carte cliquable
            if (targetRoute) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function(e) {
                    // Ne pas rediriger si on clique sur un lien ou un bouton
                    const clickedElement = e.target;
                    if (!clickedElement.closest('a, button, input, .prevent-click')) {
                        window.location.href = targetRoute;
                    }
                });
                
                // Ajouter les effets de survol
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                });
            }
        }
    });
});
