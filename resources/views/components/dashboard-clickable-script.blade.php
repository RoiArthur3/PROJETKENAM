<!-- Script pour rendre cliquables les cartes et titres du dashboard -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mapping des titres de cartes vers leurs routes
    const routeMap = {
        'Trésorerie': '/tresorerie',
        'Comptabilité': '/comptabilite',
        'Chiffre d\'Affaires': '/comptabilite',
        'Opérations': '/operations',
        'Missions': '/operations',
        'Stock': '/entrepots',
        'Magasin': '/magasin',
        'Factures': '/comptabilite/factures',
        'Validations': '/validations',
        'Parc': '/parc',
        'Véhicules': '/parc',
        'Audit': '/audit',
        'RH': '/rh',
        'Contrats': '/rh/contrats',
        'Assurances': '/parc',
        'Flux': '/tresorerie/flux',
        'Résultat': '/comptabilite',
        'Visites': '/parc/visites'
    };

    // Fonction pour trouver la route basée sur le texte du titre
    function getRoute(text) {
        for (const [key, route] of Object.entries(routeMap)) {
            if (text.toLowerCase().includes(key.toLowerCase())) {
                return route;
            }
        }
        return null;
    }

    // Traiter TOUTES les cartes
    document.querySelectorAll('.card').forEach(card => {
        // Chercher le titre
        const titleElement = card.querySelector('h6, h5, h4, .card-title');
        
        if (titleElement) {
            const titleText = titleElement.innerText.trim();
            const route = getRoute(titleText);

            // Rendre la carte cliquable
            card.style.cursor = 'pointer';
            card.style.transition = 'all 0.3s ease';

            // Ajouter les événements hover
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });

            // Ajouter l'événement clic
            if (route) {
                card.addEventListener('click', function(e) {
                    const target = e.target;
                    // Ne pas naviguer si on clique sur un lien ou un bouton
                    if (!target.closest('a, button, input')) {
                        window.location.href = route;
                    }
                });
            }

            // Rendre aussi le titre cliquable
            if (titleElement && route) {
                titleElement.style.cursor = 'pointer';
                titleElement.style.transition = 'color 0.2s ease';
                titleElement.addEventListener('click', function(e) {
                    e.stopPropagation();
                    window.location.href = route;
                });
                titleElement.addEventListener('mouseenter', function() {
                    this.style.color = '#0056b3';
                });
                titleElement.addEventListener('mouseleave', function() {
                    this.style.color = '';
                });
            }
        }
    });

    console.log('Dashboard cartes rendues cliquables');
});
</script>
