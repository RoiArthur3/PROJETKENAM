@extends('layouts.app')

@section('title', 'Stocks - Entrepôt & Magasin')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-boxes-stacked me-2 text-primary"></i>Stocks</h1>
      <p class="text-muted mb-0">Catalogue articles, seuils, emplacements</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('entrepot.stocks.export') }}" class="btn btn-outline-primary">
        <i class="fas fa-file-csv me-2"></i>Exporter CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
      </button>
      <a href="{{ route('entrepot.entrees') }}" class="btn btn-success"><i class="fas fa-truck-ramp-box me-2"></i>Nouvelle Entrée</a>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-warehouse me-2"></i>Catalogue des Stocks</h6>
    </div>
    <div class="card-body">
      <!-- Filtres avancés -->
      <div class="row g-3 align-items-end mb-3">
        <div class="col-md-4">
          <label class="form-label fw-bold">Catégorie</label>
          <select id="filtreCategorie" class="form-select">
            <option value="">Toutes</option>
            <option value="Pièces PL">Pièces PL</option>
            <option value="Consommables">Consommables</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Seuil</label>
          <select id="filtreSeuil" class="form-select">
            <option value="">Tous</option>
            <option value="rupture">Rupture (stock = 0)</option>
            <option value="faible">Faible (stock < seuil)</option>
            <option value="ok">OK (stock ≥ seuil)</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Entrepôt</label>
          <select id="filtreEntrepot" class="form-select">
            <option value="">Tous</option>
            <option value="R1">R1</option>
            <option value="R2">R2</option>
            <option value="R3">R3</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button id="btnAppliquerFiltres" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Appliquer</button>
          <button id="btnReinitialiserFiltres" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-2"></i>Réinitialiser</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="stocksTable">
          <thead class="table-light">
            <tr>
              <th>Référence</th>
              <th>Désignation</th>
              <th>Catégorie</th>
              <th>Stock</th>
              <th>Seuil Min</th>
              <th>Emplacement</th>
              <th>Fournisseur</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>FH-10T</strong></td>
              <td>Filtre à huile Camion 10T</td>
              <td>Pièces PL</td>
              <td class="fw-bold text-danger">0</td>
              <td>20</td>
              <td>R1-A2</td>
              <td><a href="{{ route('fournisseurs.index') }}">TOTAL CI</a></td>
              <td>
                <a href="{{ route('entrepot.entrees') }}" class="btn btn-sm btn-success"><i class="fas fa-plus"></i></a>
              </td>
            </tr>
            <tr>
              <td><strong>PN-20T</strong></td>
              <td>Pneu 20T</td>
              <td>Consommables</td>
              <td class="fw-bold text-warning">12</td>
              <td>30</td>
              <td>R2-C1</td>
              <td><a href="{{ route('fournisseurs.index') }}">CIMAF</a></td>
              <td>
                <a href="{{ route('entrepot.entrees') }}" class="btn btn-sm btn-success"><i class="fas fa-plus"></i></a>
              </td>
            </tr>
            <tr>
              <td><strong>KF-PL</strong></td>
              <td>Kit frein Poids Lourd</td>
              <td>Pièces PL</td>
              <td class="fw-bold text-primary">58</td>
              <td>20</td>
              <td>R3-B4</td>
              <td><a href="{{ route('fournisseurs.index') }}">BOLLORE CI</a></td>
              <td>
                <a href="{{ route('entrepot.sorties') }}" class="btn btn-sm btn-primary"><i class="fas fa-minus"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
  // DataTable
  const table = $('#stocksTable').DataTable({
    pageLength: 25
  });

  // Custom filtre par seuil/catégorie/entrepôt
  $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    if (settings.nTable.id !== 'stocksTable') return true;
    const categorieSel = $('#filtreCategorie').val() || '';
    const seuilSel = $('#filtreSeuil').val() || '';
    const entrepotSel = $('#filtreEntrepot').val() || '';

    const categorie = (data[2] || '').trim();
    const stock = parseFloat((data[3] || '0').replace(/[^0-9.-]/g, '')) || 0;
    const seuil = parseFloat((data[4] || '0').replace(/[^0-9.-]/g, '')) || 0;
    const emplacement = (data[5] || '').trim();
    const zone = emplacement.split('-')[0] || '';

    // Catégorie
    if (categorieSel && categorie !== categorieSel) return false;
    // Entrepôt (zone R1/R2/R3)
    if (entrepotSel && zone !== entrepotSel) return false;
    // Seuil
    if (seuilSel === 'rupture' && stock !== 0) return false;
    if (seuilSel === 'faible' && !(stock > 0 && stock < seuil)) return false;
    if (seuilSel === 'ok' && !(stock >= seuil)) return false;
    return true;
  });

  function applyFilters(updateUrl = true) {
    table.draw();
    if (!updateUrl) return;
    const params = new URLSearchParams(window.location.search);
    const setOrDelete = (k, v) => { if (v) params.set(k, v); else params.delete(k); };
    setOrDelete('categorie', $('#filtreCategorie').val());
    setOrDelete('seuil', $('#filtreSeuil').val());
    setOrDelete('entrepot', $('#filtreEntrepot').val());
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState({}, '', newUrl);
  }

  // Charger valeurs depuis l'URL
  (function initFromQuery(){
    const params = new URLSearchParams(window.location.search);
    if (params.has('categorie')) $('#filtreCategorie').val(params.get('categorie'));
    if (params.has('seuil')) $('#filtreSeuil').val(params.get('seuil'));
    if (params.has('entrepot')) $('#filtreEntrepot').val(params.get('entrepot'));
    applyFilters(false);
  })();

  $('#btnAppliquerFiltres').on('click', function(e){ e.preventDefault(); applyFilters(true); });
  $('#btnReinitialiserFiltres').on('click', function(e){
    e.preventDefault();
    $('#filtreCategorie').val('');
    $('#filtreSeuil').val('');
    $('#filtreEntrepot').val('');
    applyFilters(true);
  });
});
</script>
@endsection
