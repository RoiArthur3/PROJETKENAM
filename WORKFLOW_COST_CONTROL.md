# Workflow Cible Mission Engin / Cost Control

## Flux métier attendu

1. Commercial ouvre une prospection.
2. Le besoin client est formalisé dans une demande de recherche d'engin.
3. Un bon de commande client fixe le cadre d'exécution: chantier, durée, prix client, contraintes.
4. Le fournisseur met l'engin à disposition en location avec un coût fournisseur.
5. La logistique contrôle la conformité de l'engin, confirme sa disponibilité et le met en mission.
6. La logistique assigne un chauffeur à la mission.
7. Le Cost Control saisit les jours ou heures réellement travaillés par l'engin selon le bon de commande.
8. Les charges réelles et le chiffre d'affaires sont rattachés à la mission pour mesurer la marge réelle.

## Traduction applicative cible

### 1. Entrée commerciale
- Objet source attendu: prospection ou demande de recherche d'engin.
- Données minimales: client, chantier, type d'engin, période demandée, volume prévu, contraintes.
- Statut cible: validé par le commercial avant sourcing fournisseur.

### 2. Cadre contractuel
- Objet source attendu: bon de commande client.
- Données minimales: numéro, client, dates prévues, durée mission, prix client, référence commerciale.
- Rôle: document de référence pour la mission et pour le pointage Cost Control.

### 3. Sourcing fournisseur
- Objet source attendu: fournisseur + coût de location de l'engin.
- Données minimales: fournisseur, engin retenu, coût journalier ou horaire, conditions de mise à disposition.
- Rôle: alimenter le coût fournisseur initial de la mission.

### 4. Exécution logistique
- Objet applicatif actuel: vehicle_missions.
- Données minimales: véhicule/engin, chauffeur, client, fournisseur, chantier, dates, coût fournisseur, prix client.
- Rôle: pivot opérationnel entre commercial, logistique, Cost Control, Trésorerie et Facturation.

### 5. Réel d'exécution
- Objet applicatif actuel: vehicle_pointages.
- Données minimales: mission, date de pointage, unité heure/jour, quantité, chauffeur, coût unitaire, prix unitaire.
- Rôle: suivre le temps réellement consommé par l'engin sur chantier.

### 6. Réel financier
- Objet applicatif actuel: vehicle_financial_entries.
- Catégories couvertes: carburant, salaire chauffeur, maintenance, transport engin, frais divers chantier, facture client.
- Sources couvertes: saisie manuelle, décaissement Trésorerie, avance Trésorerie, facture.
- Rôle: calculer le coût réel total, le CA reconnu et la marge réelle.

## Couverture déjà présente dans le code

### Déjà en place
- Création et gestion des missions engins via `VehicleMissionController`.
- Affectation du véhicule, du fournisseur, du client et du chauffeur au niveau mission.
- Pointage des jours ou heures via `VehicleCostControlController` et `vehicle_pointages`.
- Saisie de charges et de chiffre d'affaires via `vehicle_financial_entries`.
- Liaison possible avec Trésorerie via `depense_caisse_id`.
- Liaison possible avec Facture via `facture_id`.

### Manques identifiés
- Pas de flux actif de prospection relié au module matériel.
- Pas de modèle explicite de demande de recherche d'engin servant de source de mission.
- Pas de transformation contrôlée `demande/BC -> mission`.
- La numérotation métier est fragmentée entre bon de commande et mission.
- Le lien entre bon de commande client et mission n'est pas encore matérialisé dans `vehicle_missions`.

## Pivot de conception recommandé

### Entité pivot
- La mission engin doit rester le pivot opérationnel unique.

### Chaînage cible
- `Prospection / Demande de recherche` -> `Bon de commande client` -> `Mission engin` -> `Pointages` + `Écritures financières`

### Création recommandée
- Introduire un service applicatif dédié à la création de mission.
- Ce service devra construire une mission depuis une source amont validée au lieu de laisser plusieurs contrôleurs fabriquer la mission chacun à leur manière.

## Données minimales à fiabiliser dans la mission

- Référence unique mission.
- Référence du bon de commande client.
- Client.
- Fournisseur.
- Engin.
- Chauffeur.
- Chantier / destination.
- Date de début.
- Date de fin.
- Durée prévue.
- Coût fournisseur prévu.
- Prix client prévu.
- Statut logistique.

## Étapes techniques suivantes

1. Créer le service de création de mission depuis une source commerciale validée.
2. Ajouter à `vehicle_missions` la référence de l'objet source amont, au minimum le bon de commande client.
3. Brancher les contrôleurs actifs pour utiliser ce service au lieu d'une création directe dispersée.
4. Stabiliser la numérotation métier entre bon de commande, mission et pièces financières.
5. Valider le flux complet par routes et tests de saisie.