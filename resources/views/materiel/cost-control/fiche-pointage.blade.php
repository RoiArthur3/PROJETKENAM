<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche de Pointage – {{ $mission->reference ?? ('Mission #' . $mission->id) }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;}
.page{max-width:900px;margin:0 auto;padding:20px;}
.header{display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #0d6efd;padding-bottom:12px;margin-bottom:16px;}
.logo-area h2{color:#0d6efd;font-size:20px;margin:0;}
.logo-area p{color:#555;font-size:11px;}
.doc-title h3{font-size:18px;color:#212529;text-align:right;}
.doc-title p{font-size:11px;color:#777;text-align:right;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
.info-box{border:1px solid #dee2e6;border-radius:4px;padding:12px;}
.info-box h4{font-size:11px;text-transform:uppercase;color:#666;letter-spacing:.5px;border-bottom:1px solid #eee;padding-bottom:6px;margin-bottom:8px;}
.info-row{display:flex;justify-content:space-between;margin-bottom:4px;font-size:11.5px;}
.info-row .lbl{color:#666;}
.info-row .val{font-weight:600;}
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10.5px;font-weight:600;}
.badge-plateau{background:#fff3cd;color:#856404;}
.badge-standard{background:#cff4fc;color:#0c5e67;}
table.ptg{width:100%;border-collapse:collapse;margin-bottom:16px;font-size:11.5px;}
table.ptg thead th{background:#0d6efd;color:#fff;padding:7px 8px;text-align:left;}
table.ptg thead th.num{text-align:right;}
table.ptg tbody tr:nth-child(even){background:#f8f9fa;}
table.ptg tbody td{padding:6px 8px;border-bottom:1px solid #dee2e6;}
table.ptg tbody td.num{text-align:right;font-weight:600;}
table.ptg tbody td.danger{color:#dc3545;font-weight:600;}
table.ptg tbody td.success{color:#198754;font-weight:600;}
table.ptg tbody td.primary{color:#0d6efd;}
.totals{background:#f1f3f5;border-radius:4px;padding:12px 16px;margin-bottom:20px;}
.totals table{width:100%;border-collapse:collapse;}
.totals td{padding:5px 8px;font-size:12.5px;}
.totals .lbl{color:#444;}
.totals .val{text-align:right;font-weight:700;}
.totals .big{font-size:14px;}
.totals .success{color:#198754;}
.totals .danger{color:#dc3545;}
.totals .primary{color:#0d6efd;}
.margin-box{display:flex;gap:20px;justify-content:flex-end;margin-bottom:20px;}
.margin-card{border-radius:6px;padding:10px 18px;text-align:center;min-width:140px;}
.margin-card .lab{font-size:10px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;}
.margin-card .amt{font-size:18px;font-weight:700;}
.mc-cost{background:#fff5f5;border:1px solid #f5c6cb;}
.mc-client{background:#f0fff4;border:1px solid #c3e6cb;}
.mc-margin{border:2px solid;} 
.mc-margin-pos{background:#e7f3ff;border-color:#0d6efd;}
.mc-margin-neg{background:#fff0f0;border-color:#dc3545;}
.signatures{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-top:30px;}
.sig-box{border-top:1px solid #aaa;padding-top:8px;text-align:center;font-size:11px;color:#666;}
.footer{margin-top:24px;border-top:1px solid #dee2e6;padding-top:8px;font-size:10px;color:#888;text-align:center;}
@media print{
  body{font-size:11px;}
  .no-print{display:none!important;}
  .page{padding:10px;}
}
</style>
</head>
<body>
<div class="page">

  {{-- ── Boutons actions (non imprimés) --}}
  <div class="no-print" style="text-align:right;margin-bottom:12px;">
    <button onclick="window.print()" style="background:#0d6efd;color:#fff;border:none;padding:8px 18px;border-radius:4px;font-size:13px;cursor:pointer;margin-right:8px;">
      <i class="fas fa-print"></i> Imprimer / PDF
    </button>
    <a href="{{ route('materiel.cost-control.plateau.projets-termines') }}" style="background:#6c757d;color:#fff;border:none;padding:8px 18px;border-radius:4px;font-size:13px;cursor:pointer;text-decoration:none;">
      ← Retour
    </a>
  </div>

  {{-- ── En-tête --}}
  <div class="header">
    <div class="logo-area">
      <h2>KENAM SERVICES</h2>
      <p>Cost Control – Fiche de Pointage</p>
    </div>
    <div class="doc-title">
      <h3>Fiche de Pointage</h3>
      <p>Réf. : {{ $mission->reference ?? ('Mission #' . $mission->id) }}</p>
      <p>Générée le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
  </div>

  {{-- ── Infos mission --}}
  <div class="info-grid">
    <div class="info-box">
      <h4><i>Mission / Projet de location</i></h4>
      <div class="info-row"><span class="lbl">Référence</span><span class="val">{{ $mission->reference ?? ('Mission #' . $mission->id) }}</span></div>
      <div class="info-row"><span class="lbl">Type</span>
        <span class="val">
          @php $profile = $mission->pointage_submodule ?? 'engin'; @endphp
          <span class="badge {{ $profile === 'camion_plateau' ? 'badge-plateau' : 'badge-standard' }}">
            {{ $profile === 'camion_plateau' ? 'Camion Plateau' : 'Engin Standard' }}
          </span>
        </span>
      </div>
      <div class="info-row"><span class="lbl">Destination</span><span class="val">{{ $mission->destination ?? '—' }}</span></div>
      <div class="info-row"><span class="lbl">Début</span><span class="val">{{ optional($mission->start_at)->format('d/m/Y') ?? '—' }}</span></div>
      <div class="info-row"><span class="lbl">Fin</span><span class="val">{{ optional($mission->end_at)->format('d/m/Y') ?? '—' }}</span></div>
      <div class="info-row"><span class="lbl">Durée</span><span class="val">{{ $mission->duration_days ?? '—' }} jour(s)</span></div>
    </div>
    <div class="info-box">
      <h4><i>Engin / Parties</i></h4>
      <div class="info-row"><span class="lbl">Engin</span><span class="val">{{ $mission->vehicle->immatriculation ?? '—' }}</span></div>
      <div class="info-row"><span class="lbl">Marque / Modèle</span><span class="val">{{ trim(($mission->vehicle->marque ?? '') . ' ' . ($mission->vehicle->modele ?? '')) ?: '—' }}</span></div>
      <div class="info-row"><span class="lbl">Conducteur</span>
        <span class="val">{{ optional($mission->driver)->name ?? optional($mission->driver)->nom ?? '—' }}</span>
      </div>
      <div class="info-row"><span class="lbl">Client</span>
        <span class="val">{{ optional($mission->client)->nom ?? optional($mission->client)->raison_sociale ?? optional($mission->client)->name ?? '—' }}</span>
      </div>
      <div class="info-row"><span class="lbl">Nb pointages</span><span class="val">{{ $mission->pointages->count() }}</span></div>
      @if($profile === 'camion_plateau')
      <div class="info-row"><span class="lbl">Total voyages</span><span class="val">{{ number_format($mission->pointages->sum('trip_count'), 0, ',', ' ') }}</span></div>
      @else
      <div class="info-row"><span class="lbl">Total heures</span><span class="val">{{ number_format($mission->pointages->where('unit_type','heure')->sum('quantity'), 2, ',', ' ') }} h</span></div>
      @endif
    </div>
  </div>

  {{-- ── Tableau des pointages --}}
  @if($profile === 'camion_plateau')
  <table class="ptg">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Tâche / Trajet</th>
        <th>Départ</th>
        <th>Arrivée</th>
        <th>N° BL</th>
        <th>Facturation</th>
        <th class="num">Voyages</th>
        <th class="num">Coût fourn.</th>
        <th class="num">Montant client</th>
        <th class="num">Marge</th>
      </tr>
    </thead>
    <tbody>
      @foreach($mission->pointages as $i => $p)
        @php
          $pMargin = (float)$p->total_client_amount - (float)$p->total_supplier_cost;
        @endphp
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ optional($p->date_pointage)->format('d/m/Y') }}</td>
          <td>{{ $p->task_label ?? '—' }}</td>
          <td>{{ $p->departure_location ?? '—' }}</td>
          <td>{{ $p->arrival_location ?? '—' }}</td>
          <td>{{ $p->delivery_note_number ?? '—' }}</td>
          <td>{{ $p->billing_mode === 'monthly' ? 'Mois' : 'Voyage' }}</td>
          <td class="num primary">{{ number_format($p->trip_count ?? 0, 0, ',', ' ') }}</td>
          <td class="num danger">{{ number_format($p->total_supplier_cost ?? 0, 0, ',', ' ') }}</td>
          <td class="num success">{{ number_format($p->total_client_amount ?? 0, 0, ',', ' ') }}</td>
          <td class="num {{ $pMargin >= 0 ? 'primary' : 'danger' }}">{{ number_format($pMargin, 0, ',', ' ') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <table class="ptg">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Heure début</th>
        <th>Heure fin</th>
        <th>Unité</th>
        <th class="num">Quantité</th>
        <th class="num">P.U. fournisseur</th>
        <th class="num">P.U. client</th>
        <th class="num">Coût fourn.</th>
        <th class="num">Montant client</th>
        <th class="num">Marge</th>
      </tr>
    </thead>
    <tbody>
      @foreach($mission->pointages as $i => $p)
        @php $pMargin = (float)$p->total_client_amount - (float)$p->total_supplier_cost; @endphp
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ optional($p->date_pointage)->format('d/m/Y') }}</td>
          <td>{{ $p->heure_arrivee ?? '—' }}</td>
          <td>{{ $p->heure_depart ?? '—' }}</td>
          <td>{{ ucfirst($p->unit_type ?? '—') }}</td>
          <td class="num">{{ number_format($p->quantity ?? 0, 2, ',', ' ') }}</td>
          <td class="num danger">{{ number_format($p->supplier_unit_cost ?? 0, 0, ',', ' ') }}</td>
          <td class="num success">{{ number_format($p->client_unit_price ?? 0, 0, ',', ' ') }}</td>
          <td class="num danger">{{ number_format($p->total_supplier_cost ?? 0, 0, ',', ' ') }}</td>
          <td class="num success">{{ number_format($p->total_client_amount ?? 0, 0, ',', ' ') }}</td>
          <td class="num {{ $pMargin >= 0 ? 'primary' : 'danger' }}">{{ number_format($pMargin, 0, ',', ' ') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- ── Récapitulatif --}}
  <div class="margin-box">
    <div class="margin-card mc-cost">
      <div class="lab">Coût fournisseur</div>
      <div class="amt" style="color:#dc3545;">{{ number_format($supplierTotal, 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="margin-card mc-client">
      <div class="lab">Montant à facturer</div>
      <div class="amt" style="color:#198754;">{{ number_format($clientTotal, 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="margin-card {{ $margin >= 0 ? 'mc-margin-pos' : 'mc-margin-neg' }}">
      <div class="lab">Marge brute</div>
      <div class="amt" style="color:{{ $margin >= 0 ? '#0d6efd' : '#dc3545' }};">
        {{ number_format($margin, 0, ',', ' ') }} FCFA
        <br><small style="font-size:13px;">{{ $marginPct }}%</small>
      </div>
    </div>
  </div>

  {{-- ── Signatures --}}
  <div class="signatures">
    <div class="sig-box">Établi par<br><br><br></div>
    <div class="sig-box">Vérifié par (Comptabilité)<br><br><br></div>
    <div class="sig-box">Approuvé par (DG)<br><br><br></div>
  </div>

  <div class="footer">
    KENAM SERVICES — Cost Control — {{ now()->format('d/m/Y') }} — Document confidentiel
  </div>
</div>
</body>
</html>
