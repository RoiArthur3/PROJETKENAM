@extends('layouts.app')

@section('title', 'Calcul de Paie - Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calculator text-primary me-2"></i>Calcul de Paie
            </h1>
            <p class="text-muted mb-0">Calcul automatique des salaires mensuels (Heures travaillées × Taux horaire - Déductions)</p>
        </div>
    </div>

    <!-- Paramètres de calcul -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Paramètres de Calcul
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Taux horaire (FCFA)</label>
                            <input type="number" class="form-control" id="hourlyRate" value="5000" min="0" step="100">
                            <small class="text-muted">Coût par heure travaillée</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Déductions mensuelles (FCFA)</label>
                            <input type="number" class="form-control" id="monthlyDeductions" value="0" min="0" step="1000">
                            <small class="text-muted">Retenues (CNPS, impôts, etc.)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mois de calcul</label>
                            <div class="input-group">
                                <select class="form-select" id="calcMonth">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromFormat('m', $i)->translatedFormat('F') }} {{ $currentYear }}
                                        </option>
                                    @endfor
                                </select>
                                <button class="btn btn-primary" onclick="calculatePays()">
                                    <i class="fas fa-calculator me-1"></i>Calculer
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Formule de calcul:</strong> (Heures travaillées × Taux horaire) - Déductions mensuelles<br>
                        <small>Les heures sont automatiquement plafonnées à 10h maximum par jour selon la politique RH.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-line me-2"></i>Résumé Mensuel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-0 text-primary" id="totalAgents">{{ $agents->count() }}</div>
                            <small class="text-muted">Agents</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-success" id="totalPay">0</div>
                            <small class="text-muted">Total Paie</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 mb-0 text-info" id="totalHours">0</div>
                            <small class="text-muted">Heures totales</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 text-warning" id="avgPay">0</div>
                            <small class="text-muted">Salaire moyen</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des calculs -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Détail des Calculs de Paie - {{ \Carbon\Carbon::createFromFormat('m', $currentMonth)->translatedFormat('F Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="payTable">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Service</th>
                            <th>Heures travaillées</th>
                            <th>Jours travaillés</th>
                            <th>Taux horaire</th>
                            <th>Salaire brut</th>
                            <th>Déductions</th>
                            <th>Salaire net</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                            <tr data-agent-id="{{ $agent->id }}">
                                <td>
                                    <strong>{{ $agent->name }}</strong>
                                    @if($agent->role)
                                        <br><small class="text-muted">{{ ucfirst($agent->role) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->service)
                                        <span class="badge bg-info">{{ $agent->service->nom }}</span>
                                    @else
                                        <span class="badge bg-secondary">Non assigné</span>
                                    @endif
                                </td>
                                <td class="monthly-hours">{{ $agent->monthly_hours }}</td>
                                <td class="work-days">
                                    {{ \App\Models\Pointage::where('user_id', $agent->id)->whereMonth('date_pointage', $currentMonth)->whereYear('date_pointage', $currentYear)->count() }}
                                </td>
                                <td class="hourly-rate">-</td>
                                <td class="gross-pay">-</td>
                                <td class="deductions">-</td>
                                <td class="net-pay font-weight-bold">-</td>
                                <td>
                                    <span class="badge bg-{{ $agent->actif ? 'success' : 'secondary' }}">
                                        {{ $agent->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @if($agents->isEmpty() || ($agents->count() == 1 && $agents->first()->id == 0))
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    Aucun agent trouvé pour le calcul de paie
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let currentHourlyRate = 5000;
let currentDeductions = 0;

document.getElementById('hourlyRate').addEventListener('input', function() {
    currentHourlyRate = parseFloat(this.value) || 0;
    calculatePays();
});

document.getElementById('monthlyDeductions').addEventListener('input', function() {
    currentDeductions = parseFloat(this.value) || 0;
    calculatePays();
});

document.getElementById('calcMonth').addEventListener('change', function() {
    // Reload page with new month
    const month = this.value;
    window.location.href = `?month=${month}`;
});

function calculatePays() {
    let totalPay = 0;
    let totalHours = 0;
    let activeAgents = 0;

    document.querySelectorAll('#payTable tbody tr').forEach(row => {
        const hoursCell = row.querySelector('.monthly-hours');
        const rateCell = row.querySelector('.hourly-rate');
        const grossCell = row.querySelector('.gross-pay');
        const deductionsCell = row.querySelector('.deductions');
        const netCell = row.querySelector('.net-pay');

        if (!hoursCell || !rateCell) return;

        const hours = parseFloat(hoursCell.textContent) || 0;
        const grossPay = hours * currentHourlyRate;
        const netPay = Math.max(0, grossPay - currentDeductions);

        rateCell.textContent = currentHourlyRate.toLocaleString() + ' FCFA';
        grossCell.textContent = grossPay.toLocaleString() + ' FCFA';
        deductionsCell.textContent = currentDeductions.toLocaleString() + ' FCFA';
        netCell.textContent = netPay.toLocaleString() + ' FCFA';

        // Add color coding
        if (netPay > 0) {
            netCell.classList.remove('text-danger');
            netCell.classList.add('text-success');
        } else {
            netCell.classList.remove('text-success');
            netCell.classList.add('text-danger');
        }

        if (hours > 0) {
            totalPay += netPay;
            totalHours += hours;
            activeAgents++;
        }
    });

    // Update summary
    document.getElementById('totalPay').textContent = totalPay.toLocaleString() + ' FCFA';
    document.getElementById('totalHours').textContent = totalHours.toFixed(1) + 'h';
    document.getElementById('avgPay').textContent = activeAgents > 0 ? (totalPay / activeAgents).toLocaleString() + ' FCFA' : '0 FCFA';
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    currentHourlyRate = parseFloat(document.getElementById('hourlyRate').value) || 0;
    currentDeductions = parseFloat(document.getElementById('monthlyDeductions').value) || 0;
    calculatePays();
});
</script>
@endsection
