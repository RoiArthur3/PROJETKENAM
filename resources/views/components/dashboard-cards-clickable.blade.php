<!-- Dashboard Clickable Cards Component -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendre que le DOM soit complètement chargé
    setTimeout(function() {
        // Configuration simple des routes
        var routes = {
            'tresorerie': '/tresorerie',
            'comptabilite': '/comptabilite',
            'comptabilité': '/comptabilite',
            'operations': '/operations',
            'opération': '/operations',
            'magasin': '/magasin',
            'stock': '/entrepots',
            'factures': '/comptabilite/factures',
            'facture': '/comptabilite/factures',
            'validations': '/validations',
            'parc': '/parc',
            'audit': '/audit',
            'rh': '/rh',
            'contrats': '/rh/contrats',
            'flux': '/tresorerie/flux'
        };

        // Sélectionner TOUTES les cartes
        var cards = document.querySelectorAll('.card');
        
        cards.forEach(function(card) {
            // Ajouter le curseur pointeur
            card.style.cursor = 'pointer';
            card.style.transition = 'all 0.3s ease';

            // Ajouter les événements souris
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });

            // Chercher le titre
            var titleEl = card.querySelector('h6');
            if (titleEl) {
                var titleText = titleEl.innerText.toLowerCase();
                var route = '/dashboard'; // route par défaut

                // Chercher la route correspondante
                for (var key in routes) {
                    if (titleText.indexOf(key) !== -1) {
                        route = routes[key];
                        break;
                    }
                }

                // Ajouter l'événement clic
                card.addEventListener('click', function(e) {
                    // Ne pas naviguer si on clique sur un lien ou un bouton
                    var target = e.target;
                    if (!target.closest('a, button, input, textarea')) {
                        window.location.href = route;
                    }
                });

                // Rendre le titre aussi cliquable
                titleEl.style.cursor = 'pointer';
                titleEl.addEventListener('click', function(e) {
                    e.stopPropagation();
                    window.location.href = route;
                });
            }
        });

        console.log('Cartes du dashboard rendues cliquables');
    }, 300);
});
</script>
