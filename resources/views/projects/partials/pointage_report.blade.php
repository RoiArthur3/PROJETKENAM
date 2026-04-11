@php
use Carbon\Carbon;
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Rapport de pointage des engins</h2>
        <form method="GET" action="" class="d-flex gap-2 align-items-end">
            <div>
                <label class="form-label small">Engin</label>
                <select name="vehicule_id" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($vehicules as $vehicule)
                        <option value="{{ $vehicule->id }}" {{ $vehiculeId == $vehicule->id ? 'selected' : '' }}>
                            {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label small">Date début</label>
                <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ $dateDebut }}">
            </div>
            <div>
                <label class="form-label small">Date fin</label>
                <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ $dateFin }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>
        <a href="?@php
            $params = array_filter([
                'vehicule_id' => $vehiculeId,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'export' => 'csv',
            ]);
            echo http_build_query($params);
        @endphp" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-csv me-1"></i>Exporter CSV
        </a>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Engin</th>
                        <th>Heure début</th>
                        <th>Heure fin</th>
                        <th>Durée (h)</th>
                        <th>Coût fournisseur</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pointages as $p)
                        <tr>
                            <td>{{ Carbon::parse($p->date_pointage)->format('d/m/Y') }}</td>
                            <td>{{ $p->vehicle->immatriculation ?? '-' }}</td>
                            <td>{{ $p->heure_arrivee }}</td>
                            <td>{{ $p->heure_depart }}</td>
                            <td>
                                @if($p->heure_arrivee && $p->heure_depart)
                                    @php
                                        $start = Carbon::createFromFormat('H:i', $p->heure_arrivee);
                                        $end = Carbon::createFromFormat('H:i', $p->heure_depart);
                                        $duration = $end->floatDiffInHours($start);
                                    @endphp
                                    {{ number_format($duration, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($p->total_supplier_cost, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $p->notes }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Aucun pointage trouvé pour ces critères.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
