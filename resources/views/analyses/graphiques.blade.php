@extends('layouts.app')
@section('title', 'Bibliothèque de Graphiques - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-chart-area me-2 text-primary"></i>Bibliothèque de Graphiques</h1>

    <div class="row mb-4">
        <div class="col-12 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Graphique "{{ $type }}" (période : {{ $period ?? 'month' }})
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="mainChart" height="80"></canvas>
                    <div id="noDataMessage" class="text-center text-muted mt-3" style="display: none;">
                        Aucune donnée disponible pour cette combinaison type/période.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const type = @json($type);
const rawData = @json($chartData);

const ctx = document.getElementById('mainChart').getContext('2d');
let labels = [];
let datasets = [];

if (type === 'operations' && Array.isArray(rawData) && rawData.length) {
    labels = rawData.map(item => item.month ?? item.label ?? '');
    datasets.push({
        label: 'Nombre d\'opérations',
        data: rawData.map(item => item.count ?? 0),
        borderColor: '#4e73df',
        backgroundColor: 'rgba(78, 115, 223, 0.1)',
        tension: 0.4,
        fill: true,
    });
} else if (type === 'financial' && rawData && (rawData.revenue || rawData.expenses)) {
    const revenue = rawData.revenue || [];
    const expenses = rawData.expenses || [];
    labels = revenue.map(item => item.month ?? '');
    datasets.push({
        label: 'Revenus',
        data: revenue.map(item => item.amount ?? 0),
        borderColor: '#4e73df',
        backgroundColor: 'rgba(78, 115, 223, 0.1)',
        tension: 0.4,
        fill: true,
    });
    datasets.push({
        label: 'Dépenses',
        data: expenses.map(item => item.amount ?? 0),
        borderColor: '#e74a3b',
        backgroundColor: 'rgba(231, 74, 59, 0.1)',
        tension: 0.4,
        fill: true,
    });
} else {
    document.getElementById('mainChart').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'block';
}

if (datasets.length) {
    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
        }
    });
}
</script>
@endsection
