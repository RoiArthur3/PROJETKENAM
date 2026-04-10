@extends('layouts.app')
@section('title', 'Comptabilité - KENAM SERVICES')

@section('content')
<style>
    .compta-sidebar { min-height: calc(100vh - 120px); background: #f8f9fc; border-right: 1px solid #e3e6f0; }
    .compta-sidebar .nav-link { color: #5a5c69; padding: .65rem 1rem; font-size: .85rem; border-radius: 0; border-left: 3px solid transparent; transition: all .15s; }
    .compta-sidebar .nav-link:hover { background: #eaecf4; color: #4e73df; }
    .compta-sidebar .nav-link.active { background: #fff; color: #4e73df; border-left-color: #4e73df; font-weight: 600; }
    .compta-sidebar .nav-link i { width: 22px; text-align: center; margin-right: 8px; }
    .compta-sidebar .sidebar-heading { font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: #b7b9cc; padding: .75rem 1rem .4rem; font-weight: 700; }
    .section-content { min-height: 400px; }
    .stat-card { border-radius: .5rem; border: none; box-shadow: 0 .1rem .25rem rgba(0,0,0,.075); transition: transform .15s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .badge-statut { font-size: .75rem; padding: .3em .6em; }
    .table-compta { font-size: .85rem; }
    .table-compta th { font-weight: 600; background: #f8f9fc; border-top: none; white-space: nowrap; }
    .table-compta td { vertical-align: middle; }
    .empty-state { padding: 3rem 1rem; text-align: center; color: #b7b9cc; }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; }
    .spinner-section { display: flex; justify-content: center; align-items: center; min-height: 300px; }
    .sub-tabs .nav-link { font-size: .8rem; padding: .4rem .8rem; }
    @media(max-width:767px) { .compta-sidebar { min-height: auto; border-right: none; border-bottom: 1px solid #e3e6f0; } }
</style>

<div class="container-fluid px-0">
    <div class="row g-0">
        {{-- Sidebar --}}
        <div class="col-md-2 compta-sidebar py-3">
            <div class="sidebar-heading">Comptabilité</div>
            <nav class="nav flex-column" id="comptaSidebar">
                <a class="nav-link active" href="#" data-section="overview" data-url="{{ route('comptabilite.api.stats') }}">
                    <i class="fas fa-tachometer-alt"></i>Vue d'ensemble
                </a>
                <div class="sidebar-heading mt-2">Gestion</div>
                <a class="nav-link" href="#" data-section="facturation" data-url="{{ route('comptabilite.api.facturation') }}">
                    <i class="fas fa-file-invoice"></i>Facturation
                </a>
                <a class="nav-link" href="#" data-section="encaissements" data-url="{{ route('comptabilite.api.encaissements') }}">
                    <i class="fas fa-hand-holding-usd"></i>Encaissements
                </a>
                <a class="nav-link" href="#" data-section="depenses" data-url="{{ route('comptabilite.api.depenses') }}">
                    <i class="fas fa-receipt"></i>Dépenses
                </a>
                <div class="sidebar-heading mt-2">Trésorerie</div>
                <a class="nav-link" href="#" data-section="caisse" data-url="{{ route('comptabilite.api.caisse') }}">
                    <i class="fas fa-cash-register"></i>Caisse
                </a>
                <a class="nav-link" href="#" data-section="banque" data-url="{{ route('comptabilite.api.banque') }}">
                    <i class="fas fa-university"></i>Banque
                </a>
                <div class="sidebar-heading mt-2">Tiers</div>
                <a class="nav-link" href="#" data-section="clients" data-url="{{ route('comptabilite.api.clients') }}">
                    <i class="fas fa-users"></i>Clients
                </a>
                <a class="nav-link" href="#" data-section="fournisseurs" data-url="{{ route('comptabilite.api.fournisseurs') }}">
                    <i class="fas fa-truck"></i>Fournisseurs
                </a>
                <div class="sidebar-heading mt-2">Analyse</div>
                <a class="nav-link" href="#" data-section="charges" data-url="{{ route('comptabilite.api.charges') }}">
                    <i class="fas fa-tags"></i>Charges &amp; Catégories
                </a>
                <a class="nav-link" href="#" data-section="rapports" data-url="{{ route('comptabilite.api.rapports') }}">
                    <i class="fas fa-chart-bar"></i>Rapports
                </a>
                <a class="nav-link" href="#" data-section="pieces" data-url="{{ route('comptabilite.api.pieces') }}">
                    <i class="fas fa-paperclip"></i>Pièces justificatives
                </a>
                <div class="sidebar-heading mt-2">Référentiel</div>
                <a class="nav-link" href="{{ route('comptabilite.syscohada.index') }}">
                    <i class="fas fa-book"></i>Plan Comptable
                </a>
            </nav>
        </div>

        {{-- Contenu principal --}}
        <div class="col-md-10 p-4">
            <div id="sectionTitle" class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Vue d'ensemble</h4>
                    <small class="text-muted">Tableau de bord comptabilité</small>
                </div>
                <button class="btn btn-sm btn-outline-primary" id="btnRefresh" title="Actualiser">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div id="sectionContent" class="section-content">
                <div class="spinner-section">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('comptaSidebar');
    const content = document.getElementById('sectionContent');
    const titleEl = document.getElementById('sectionTitle');
    let currentChart = null;
    let currentSection = 'overview';

    const sectionMeta = {
        overview:      { icon: 'fa-tachometer-alt', title: "Vue d'ensemble", sub: 'Tableau de bord comptabilité' },
        facturation:   { icon: 'fa-file-invoice', title: 'Facturation', sub: 'Factures, devis, proformas' },
        encaissements: { icon: 'fa-hand-holding-usd', title: 'Encaissements', sub: 'Paiements clients, recettes' },
        depenses:      { icon: 'fa-receipt', title: 'Dépenses', sub: 'Dépenses courantes et justificatifs' },
        caisse:        { icon: 'fa-cash-register', title: 'Caisse', sub: 'Soldes, mouvements, journal' },
        banque:        { icon: 'fa-university', title: 'Banque', sub: 'Comptes, virements, relevés' },
        clients:       { icon: 'fa-users', title: 'Clients', sub: 'Comptes clients, créances' },
        fournisseurs:  { icon: 'fa-truck', title: 'Fournisseurs', sub: 'Comptes fournisseurs, dettes' },
        charges:       { icon: 'fa-tags', title: 'Charges & Catégories', sub: 'Répartition des charges' },
        rapports:      { icon: 'fa-chart-bar', title: 'Rapports', sub: 'États financiers et graphiques' },
        pieces:        { icon: 'fa-paperclip', title: 'Pièces justificatives', sub: 'Reçus et documents' },
    };

    // Format monétaire
    function fmt(n) { return new Intl.NumberFormat('fr-FR', {style:'decimal', maximumFractionDigits:0}).format(n || 0); }
    function fmtCFA(n) { return fmt(n) + ' FCFA'; }

    // Badge statut
    function badge(statut) {
        const colors = {
            'payée':'success','payé':'success','validé':'success','validée':'success','encaissée':'success','actif':'success',
            'impayée':'danger','rejetée':'danger','annulée':'secondary','annulé':'secondary',
            'en_attente':'warning','brouillon':'secondary','effectue':'info','effectué':'info',
            'non justifié':'warning','justifié':'success',
        };
        const c = colors[(statut||'').toLowerCase()] || 'secondary';
        return `<span class="badge bg-${c} badge-statut">${statut || '-'}</span>`;
    }

    // Spinner
    function showLoading() {
        content.innerHTML = '<div class="spinner-section"><div class="spinner-border text-primary" role="status"></div></div>';
    }

    // Mise à jour titre
    function updateTitle(section) {
        const m = sectionMeta[section] || sectionMeta.overview;
        titleEl.querySelector('h4').innerHTML = `<i class="fas ${m.icon} me-2 text-primary"></i>${m.title}`;
        titleEl.querySelector('small').textContent = m.sub;
    }

    // Navigation sidebar
    sidebar.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            currentSection = this.dataset.section;
            updateTitle(currentSection);
            loadSection(currentSection, this.dataset.url);
        });
    });

    // Refresh
    document.getElementById('btnRefresh').addEventListener('click', function() {
        const active = sidebar.querySelector('.nav-link.active');
        if (active) loadSection(active.dataset.section, active.dataset.url);
    });

    // Charger une section
    async function loadSection(section, url) {
        showLoading();
        if (currentChart) { currentChart.destroy(); currentChart = null; }
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Erreur ' + res.status);
            const data = await res.json();
            if (data.error) throw new Error(data.error);
            renderSection(section, data);
        } catch(err) {
            content.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${err.message}</div>`;
        }
    }

    // Dispatch render
    function renderSection(section, data) {
        const renderers = {
            overview: renderOverview,
            facturation: renderFacturation,
            encaissements: renderEncaissements,
            depenses: renderDepenses,
            caisse: renderCaisse,
            banque: renderBanque,
            clients: renderClients,
            fournisseurs: renderFournisseurs,
            charges: renderCharges,
            rapports: renderRapports,
            pieces: renderPieces,
        };
        (renderers[section] || renderOverview)(data);
    }

    // ─── OVERVIEW ───
    function renderOverview(d) {
        content.innerHTML = `
        <div class="row g-3 mb-4">
            ${statCard('Factures', d.factures, 'fa-file-invoice', 'primary', fmtCFA(d.montant_factures))}
            ${statCard('Impayées', d.factures_impayees, 'fa-exclamation-circle', 'danger')}
            ${statCard('Dépenses', d.depenses, 'fa-receipt', 'warning', fmtCFA(d.montant_depenses))}
            ${statCard('Solde Caisses', '', 'fa-cash-register', 'success', fmtCFA(d.solde_caisses))}
            ${statCard('Clients', d.clients, 'fa-users', 'info')}
            ${statCard('Fournisseurs', d.fournisseurs, 'fa-truck', 'secondary')}
            ${statCard('Comptes bancaires', d.comptes_bancaires, 'fa-university', 'dark')}
            ${statCard('Virements', d.virements, 'fa-exchange-alt', 'info')}
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm"><div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle me-1 text-primary"></i>Résumé rapide</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-1 border-bottom"><span>Recettes enregistrées</span><strong>${fmt(d.recettes)}</strong></li>
                        <li class="d-flex justify-content-between py-1 border-bottom"><span>Montant recettes</span><strong>${fmtCFA(d.montant_recettes)}</strong></li>
                        <li class="d-flex justify-content-between py-1 border-bottom"><span>Encaissements ce mois</span><strong>${fmt(d.encaissements_mois)}</strong></li>
                        <li class="d-flex justify-content-between py-1 border-bottom"><span>Paiements clients</span><strong>${fmt(d.paiements)}</strong></li>
                        <li class="d-flex justify-content-between py-1"><span>Caisses actives</span><strong>${fmt(d.nb_caisses)}</strong></li>
                    </ul>
                </div></div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm"><div class="card-body">
                    <h6 class="card-title"><i class="fas fa-lightbulb me-1 text-warning"></i>Accès rapides</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="${routes.facturesCreate}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i>Nouvelle facture</a>
                        <a href="${routes.depensesCreate}" class="btn btn-sm btn-outline-warning"><i class="fas fa-plus me-1"></i>Nouvelle dépense</a>
                        <a href="${routes.encaissementsCreate}" class="btn btn-sm btn-outline-success"><i class="fas fa-plus me-1"></i>Nouvel encaissement</a>
                        <a href="${routes.recettesCreate}" class="btn btn-sm btn-outline-info"><i class="fas fa-plus me-1"></i>Nouvelle recette</a>
                    </div>
                </div></div>
            </div>
        </div>`;
    }

    function statCard(label, value, icon, color, sub) {
        return `<div class="col-xl-3 col-md-6"><div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-${color} bg-opacity-10 text-${color} me-3"><i class="fas ${icon}"></i></div>
                <div><div class="text-muted" style="font-size:.75rem">${label}</div>
                <div class="fw-bold fs-5">${value !== '' ? fmt(value) : (sub || '0')}</div>
                ${value !== '' && sub ? `<small class="text-muted">${sub}</small>` : ''}
                </div>
            </div>
        </div></div>`;
    }

    // ─── FACTURATION ───
    function renderFacturation(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Factures', s.total_factures, 'fa-file-invoice', 'primary', fmtCFA(s.montant_total))}
            ${statCard('Impayées', s.impayees, 'fa-exclamation-circle', 'danger', fmtCFA(s.montant_impaye))}
            ${statCard('Devis', s.total_devis, 'fa-file-alt', 'info')}
            ${statCard('Proformas', s.total_proformas, 'fa-file-contract', 'secondary')}
        </div>
        <ul class="nav nav-tabs sub-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabFactures">Factures</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabDevis">Devis</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabProformas">Proformas</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabFactures">${dataTable(['Réf','Client','Date','Montant','Statut'], d.factures, r => [r.reference,r.client,r.date,fmtCFA(r.montant),badge(r.statut)])}</div>
            <div class="tab-pane fade" id="tabDevis">${dataTable(['Réf','Client','Date','Montant','Statut'], d.devis, r => [r.reference,r.client,r.date,fmtCFA(r.montant),badge(r.statut)])}</div>
            <div class="tab-pane fade" id="tabProformas">${dataTable(['Réf','Client','Date','Montant','Statut'], d.proformas, r => [r.reference,r.client,r.date,fmtCFA(r.montant),badge(r.statut)])}</div>
        </div>`;
    }

    // ─── ENCAISSEMENTS ───
    function renderEncaissements(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-muted text-uppercase fw-bold" style="font-size:.7rem; letter-spacing:.05em">Statistiques & Actions</h6>
            <div class="d-flex gap-2">
                <a href="${routes.encaissementsCreate}" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-plus me-1"></i>Nouvel encaissement
                </a>
                <a href="${routes.recettesCreate}" class="btn btn-sm btn-info text-white shadow-sm">
                    <i class="fas fa-plus me-1"></i>Nouvelle recette
                </a>
            </div>
        </div>
        <div class="row g-3 mb-3">
            ${statCard('Encaissements', s.total_encaissements, 'fa-arrow-down', 'success', fmtCFA(s.montant_encaissements))}
            ${statCard('Recettes', s.total_recettes, 'fa-coins', 'info', fmtCFA(s.montant_recettes))}
            ${statCard('Paiements clients', s.total_paiements, 'fa-credit-card', 'primary')}
        </div>
        <ul class="nav nav-tabs sub-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabEnc">Encaissements</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRec">Recettes</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPai">Paiements</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabEnc">${dataTable(['Réf','Date','Type','Client','Caisse','Montant','Statut'], d.encaissements, r=>[r.reference,r.date,r.type,r.client,r.caisse,fmtCFA(r.montant),badge(r.statut)])}</div>
            <div class="tab-pane fade" id="tabRec">${dataTable(['Réf','Date','Catégorie','Mode','Montant','Statut'], d.recettes, r=>[r.reference,r.date,r.categorie,r.mode_paiement,fmtCFA(r.montant),badge(r.statut)])}</div>
            <div class="tab-pane fade" id="tabPai">${dataTable(['Réf','Date','Mode','Montant','Statut'], d.paiements, r=>[r.reference,r.date,r.mode,fmtCFA(r.montant),badge(r.statut)])}</div>
        </div>`;
    }

    // ─── DEPENSES ───
    function renderDepenses(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-muted text-uppercase fw-bold" style="font-size:.7rem; letter-spacing:.05em">Statistiques & Actions</h6>
            <a href="${routes.depensesCreate}" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-plus me-1"></i>Nouvelle dépense
            </a>
        </div>
        <div class="row g-3 mb-3">
            ${statCard('Total dépenses', s.total, 'fa-receipt', 'warning', fmtCFA(s.montant_total))}
            ${statCard('Validées', s.validees, 'fa-check-circle', 'success')}
            ${statCard('En attente', s.en_attente, 'fa-clock', 'info')}
        </div>
        ${dataTable(['Réf','Date','Libellé','Caisse','Mode','Montant','Pièces','Statut'], d.depenses, r=>[r.reference,r.date,r.libelle,r.caisse,r.mode_paiement,fmtCFA(r.montant),r.has_pieces?'<i class="fas fa-paperclip text-success"></i>':'<i class="fas fa-minus text-muted"></i>',badge(r.statut)])}`;
    }

    // ─── CAISSE ───
    function renderCaisse(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Solde total', '', 'fa-cash-register', 'success', fmtCFA(s.solde_total))}
            ${statCard('Caisses', s.nb_caisses, 'fa-box', 'primary', s.nb_actives+' actives')}
            ${statCard('Mouvements ce mois', s.nb_mouvements_mois, 'fa-exchange-alt', 'info')}
        </div>
        <ul class="nav nav-tabs sub-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabCaisses">Caisses</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabMvts">Journal mouvements</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabCaisses">${dataTable(['Nom','Type','Devise','Solde initial','Solde actuel','Active'], d.caisses, r=>[r.nom,r.type,r.devise,fmtCFA(r.solde_initial),`<strong>${fmtCFA(r.solde_actuel)}</strong>`,r.est_active?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>'])}</div>
            <div class="tab-pane fade" id="tabMvts">${dataTable(['Date','Type','Libellé','Caisse','Montant'], d.mouvements, r=>[r.date,badge(r.type),r.libelle,r.caisse,`<strong class="${r.montant>=0?'text-success':'text-danger'}">${fmtCFA(r.montant)}</strong>`])}</div>
        </div>`;
    }

    // ─── BANQUE ───
    function renderBanque(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Comptes', s.nb_comptes, 'fa-university', 'primary', fmtCFA(s.solde_total))}
            ${statCard('Virements', s.nb_virements, 'fa-exchange-alt', 'info')}
            ${statCard('Banques', s.nb_banques, 'fa-landmark', 'dark')}
        </div>
        <ul class="nav nav-tabs sub-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabComptes">Comptes bancaires</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabVir">Virements</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabBanques">Banques</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabComptes">${dataTable(['N° Compte','Banque','Devise','Solde','Statut'], d.comptes, r=>[r.numero,r.banque,r.devise,`<strong>${fmtCFA(r.solde)}</strong>`,badge(r.statut)])}</div>
            <div class="tab-pane fade" id="tabVir">${dataTable(['Réf','Date','Montant','Motif','Statut'], d.virements, r=>[r.reference,r.date,fmtCFA(r.montant),r.motif,badge(r.statut)])}</div>
            <div class="tab-pane fade" id="tabBanques">${dataTable(['Nom','Code'], d.banques, r=>[r.nom,r.code])}</div>
        </div>`;
    }

    // ─── CLIENTS ───
    function renderClients(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Clients', s.total_clients, 'fa-users', 'primary')}
            ${statCard('Créances totales', '', 'fa-exclamation-triangle', 'danger', fmtCFA(s.total_creances))}
            ${statCard('Factures impayées', s.nb_factures_impayees, 'fa-file-invoice', 'warning')}
        </div>
        ${dataTable(['Nom','Email','Téléphone','Type','Factures','Total facturé','Impayé'], d.clients, r=>[r.nom,r.email,r.telephone,r.type,fmt(r.nb_factures),fmtCFA(r.montant_total),r.montant_impaye>0?`<span class="text-danger fw-bold">${fmtCFA(r.montant_impaye)}</span>`:fmtCFA(0)])}`;
    }

    // ─── FOURNISSEURS ───
    function renderFournisseurs(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Fournisseurs', s.total_fournisseurs, 'fa-truck', 'primary')}
            ${statCard('Dettes totales', '', 'fa-exclamation-triangle', 'danger', fmtCFA(s.total_dettes))}
            ${statCard('Commandes', s.total_commandes, 'fa-shopping-cart', 'info')}
            ${statCard('Factures impayées', s.nb_factures_impayees, 'fa-file-invoice', 'warning')}
        </div>
        ${dataTable(['Nom','Email','Téléphone','Catégorie','Commandes','Montant cmdé','Paiements','Montant payé'], d.fournisseurs, r=>[r.nom,r.email,r.telephone,r.categorie,fmt(r.nb_commandes),fmtCFA(r.montant_commandes),fmt(r.nb_paiements),fmtCFA(r.montant_paye)])}`;
    }

    // ─── CHARGES ───
    function renderCharges(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Total dépenses', '', 'fa-arrow-down', 'danger', fmtCFA(s.total_depenses))}
            ${statCard('Total recettes', '', 'fa-arrow-up', 'success', fmtCFA(s.total_recettes))}
            ${statCard('Catég. dépenses', s.nb_categories_depenses, 'fa-tags', 'warning')}
            ${statCard('Catég. recettes', s.nb_categories_recettes, 'fa-tags', 'info')}
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><canvas id="chartChargesDep" height="220"></canvas></div></div></div>
            <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><canvas id="chartChargesRec" height="220"></canvas></div></div></div>
        </div>
        <ul class="nav nav-tabs sub-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabDepCat">Dépenses par catégorie</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRecCat">Recettes par catégorie</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabDepCat">${dataTable(['Catégorie','Nombre','Total'], d.depenses_par_categorie, r=>[r.categorie,fmt(r.nb),fmtCFA(r.total)])}</div>
            <div class="tab-pane fade" id="tabRecCat">${dataTable(['Catégorie','Nombre','Total'], d.recettes_par_categorie, r=>[r.categorie,fmt(r.nb),fmtCFA(r.total)])}</div>
        </div>`;

        // Charts
        setTimeout(() => {
            const colors = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#5a5c69','#fd7e14','#6f42c1','#20c9a6'];
            if (d.depenses_par_categorie.length) {
                new Chart(document.getElementById('chartChargesDep'), {
                    type: 'doughnut',
                    data: { labels: d.depenses_par_categorie.map(r=>r.categorie), datasets: [{ data: d.depenses_par_categorie.map(r=>r.total), backgroundColor: colors }] },
                    options: { responsive: true, plugins: { title: { display: true, text: 'Dépenses par catégorie' } } }
                });
            }
            if (d.recettes_par_categorie.length) {
                new Chart(document.getElementById('chartChargesRec'), {
                    type: 'doughnut',
                    data: { labels: d.recettes_par_categorie.map(r=>r.categorie), datasets: [{ data: d.recettes_par_categorie.map(r=>r.total), backgroundColor: colors }] },
                    options: { responsive: true, plugins: { title: { display: true, text: 'Recettes par catégorie' } } }
                });
            }
        }, 100);
    }

    // ─── RAPPORTS ───
    function renderRapports(d) {
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Total recettes (6 mois)', '', 'fa-arrow-up', 'success', fmtCFA(d.total_recettes))}
            ${statCard('Total dépenses (6 mois)', '', 'fa-arrow-down', 'danger', fmtCFA(d.total_depenses))}
            ${statCard('Bénéfice net', '', 'fa-balance-scale', d.benefice >= 0 ? 'success' : 'danger', fmtCFA(d.benefice))}
        </div>
        <div class="card shadow-sm mb-3"><div class="card-body"><canvas id="chartRapports" height="100"></canvas></div></div>`;

        setTimeout(() => {
            currentChart = new Chart(document.getElementById('chartRapports'), {
                type: 'bar',
                data: {
                    labels: d.mois_labels,
                    datasets: [
                        { label: 'Recettes', data: d.recettes_mois, backgroundColor: 'rgba(28,200,138,.7)' },
                        { label: 'Dépenses', data: d.depenses_mois, backgroundColor: 'rgba(231,74,59,.7)' },
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { title: { display: true, text: 'Recettes vs Dépenses (6 derniers mois)' } } }
            });
        }, 100);
    }

    // ─── PIECES ───
    function renderPieces(d) {
        const s = d.stats;
        content.innerHTML = `
        <div class="row g-3 mb-3">
            ${statCard('Pièces jointes', s.total_pieces, 'fa-paperclip', 'primary')}
            ${statCard('Justificatifs dépenses', s.total_justificatifs_depenses, 'fa-file-alt', 'info')}
        </div>
        <ul class="nav nav-tabs sub-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabPiecesGen">Pièces jointes</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabJustDep">Justificatifs dépenses</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabPiecesGen">${dataTable(['Nom fichier','Type','Date','Dépense #','Validé'], d.justificatifs, r=>[r.nom,r.type,r.date,r.depense_id||'-',r.valide])}</div>
            <div class="tab-pane fade" id="tabJustDep">${dataTable(['Nom','Dépense #','Catégorie','Date','Validé'], d.justificatifs_depenses, r=>[r.nom,r.depense_id||'-',r.categorie,r.date,r.valide])}</div>
        </div>`;
    }

    // ─── TABLE HELPER ───
    function dataTable(headers, rows, mapFn) {
        if (!rows || !rows.length) {
            return `<div class="empty-state"><i class="fas fa-inbox d-block"></i><p>Aucune donnée disponible</p></div>`;
        }
        let html = '<div class="table-responsive"><table class="table table-hover table-compta mb-0"><thead><tr>';
        headers.forEach(h => html += `<th>${h}</th>`);
        html += '</tr></thead><tbody>';
        rows.forEach(r => {
            const cells = mapFn(r);
            html += '<tr>';
            cells.forEach(c => html += `<td>${c ?? '-'}</td>`);
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    // Routes pour les liens rapides
    const routes = {
        facturesCreate: '{{ route("comptabilite.factures.create") }}',
        depensesCreate: '{{ route("tresorerie.depenses.create") }}',
        encaissementsCreate: '{{ route("tresorerie.encaissements.create") }}',
        recettesCreate: '{{ route("comptabilite.recettes.create") }}',
    };

    // Chargement initial
    loadSection('overview', '{{ route("comptabilite.api.stats") }}');
});
</script>
@endpush
