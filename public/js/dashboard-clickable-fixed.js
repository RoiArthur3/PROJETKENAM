// Dashboard Clickable Cards - Version Améliorée
// Script pour rendre tous les titre et cartes du dashboard cliquables

(function() {
    // Routes complètes pour chaque type de carte
    const cardRoutes = {
        'Trésorerie': '/tresorerie',
        'Comptabilité': '/comptabilite',
        'Chiffre': '/comptabilite',
        'Affaires': '/comptabilite',
        'Opérations': '/operations',
        'Missions': '/operations',
        'Stock': '/entrepots',
        'Entrepôts': '/entrepots',
        'Magasin': '/magasin',
        'Alertes': '/entrepots',
        'Validations': '/validations',
        'Factures': '/comptabilite/factures',
        'Parc': '/parc',
        'Disponibilité': '/parc',
        'Véhicules': '/parc',
        'Audit': '/audit',
        'RH': '/rh',
        'Contrats': '/rh/contrats',
        'Personnel': '/rh',
        'Assurances': '/parc',
        'Flux': '/tresorerie/flux',
        'Prévisionnel': '/tresorerie',
        'SMS': '/dashboard',
        'Résultat Net': '/comptabilite',
        'Performance': '/comptabilite',
        'Revenus': '/comptabilite/factures',
        'Dépenses': '/tresorerie',
        'Visites': '/parc',
        'Techniques': '/parc'
    };

    function findRoute(text) {
        const lowerText = text.toLowerCase();
        for (const [keyword, route] of Object.entries(cardRoutes)) {
            if (lowerText.includes(keyword.toLowerCase())) {
                return route;
            }
        }
        return null;
    }

    function makeCardsClickable() {
        // Sélectionner toutes les cartes
        const cards = document.querySelectorAll('.card');
        let clickableCount = 0;

        cards.forEach((card) => {
            try {
                // Chercher le titre (h6, h5, h4 ou .card-title)
                let titleEl = card.querySelector('h6, h5, h4, .card-title');
                
                if (titleEl) {
                    const titleText = titleEl.textContent.trim();
                    const route = findRoute(titleText);

                    if (route) {
                        // Ajouter les styles
                        card.style.cursor = 'pointer';
                        card.style.transition = 'all 0.3s ease';
                        card.setAttribute('data-route', route);
                        
                        // Ajouter les événements souris
                        card.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateY(-5px)';
                            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.15)';
                        });

                        card.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = '';
                        });

                        // Ajouter l'événement clic
                        card.addEventListener('click', function(e) {
                            // Vérifier le cible du clic
                            const target = e.target;
                            if (!target.closest('a, button, input, textarea, [onclick]')) {
                                const destinationRoute = this.getAttribute('data-route');
                                window.location.href = destinationRoute;
                            }
                        });

                        clickableCount++;
                    }
                }
                
                // Rendre aussi les titres cliquables directement
                const titles = card.querySelectorAll('h6, h5, h4, .card-title');
                titles.forEach(title => {
                    const titleText = title.textContent.trim();
                    const route = findRoute(titleText);
                    if (route) {
                        title.style.cursor = 'pointer';
                        title.style.transition = 'color 0.3s ease';
                        title.addEventListener('click', function(e) {
                            e.stopPropagation();
                            window.location.href = route;
                        });
                        title.addEventListener('mouseenter', function() {
                            this.style.color = '#0c63e4';
                        });
                        title.addEventListener('mouseleave', function() {
                            this.style.color = '';
                        });
                    }
                });
            } catch (error) {
                console.error('Erreur avec la carte:', error);
            }
        });

        console.log('Cartes cliquables activées:', clickableCount);
    }

    // Exécuter au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', makeCardsClickable);
    } else {
        makeCardsClickable();
    }

    // Aussi réexécuter après un délai pour les cartes chargées dynamiquement
    setTimeout(makeCardsClickable, 1000);
})();
