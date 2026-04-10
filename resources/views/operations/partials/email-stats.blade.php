<!-- Statistiques Email Dashboard -->
@if(isset($emailStats))
    <div class="alert alert-success mb-3">
        <strong>Debug Email Stats:</strong>
        Today: {{ $emailStats['today']['count'] ?? 'undefined' }} |
        Success Rate: {{ $emailStats['today']['success_rate'] ?? 'undefined' }}%
    </div>

    <div class="row mb-4">
        <!-- Carte Email du jour -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Emails Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $emailStats['today']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $emailStats['today']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Email de la semaine -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Emails Semaine
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $emailStats['week']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $emailStats['week']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Email du mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Emails Mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $emailStats['month']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $emailStats['month']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Email Total -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Emails
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $emailStats['total']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $emailStats['total']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique de tendance Email -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">Tendance Emails (7 derniers jours)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="emailTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emails par type -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Emails par Type</h6>
                </div>
                <div class="card-body">
                    @if(!empty($emailStats['by_type']))
                        @foreach($emailStats['by_type'] as $type)
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $type['success_rate'] ?? 0 }}%">
                                {{ $type['template'] ?? 'Unknown' }}: {{ $type['count'] ?? 0 }}
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">Aucun email envoyé</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Emails récents -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Emails Récents</h6>
                </div>
                <div class="card-body">
                    @if(!empty($emailStats['recent']))
                        <div class="table-responsive">
                            <table class="table table-bordered" id="emailTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Destinataire</th>
                                        <th>Type</th>
                                        <th>Opération</th>
                                        <th>Statut</th>
                                        <th>Sujet</th>
                                        <th>Utilisateur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($emailStats['recent'] as $email)
                                    <tr>
                                        <td>{{ isset($email['created_at']) ? \Carbon\Carbon::parse($email['created_at'])->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $email['recipient'] ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $email['template'] ?? 'Unknown' }}</span>
                                        </td>
                                        <td>
                                            @if(isset($email['operation_reference']) && $email['operation_reference'])
                                                <a href="{{ route('operations.show', $email['operation_reference']) }}" class="text-decoration-none">
                                                    {{ $email['operation_reference'] }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(($email['status'] ?? 'pending') == 'success')
                                                <span class="badge badge-success">✓ Envoyé</span>
                                            @else
                                                <span class="badge badge-danger">✗ Échec</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $email['subject'] ?? '-' }}">
                                                {{ $email['subject'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $email['user_name'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucun email récent</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour le graphique Email -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique de tendance Email
        const emailCtx = document.getElementById('emailTrendChart');
        if (emailCtx) {
            new Chart(emailCtx, {
                type: 'line',
                data: {
                    labels: @json(isset($emailStats['trend']) ? array_column($emailStats['trend'], 'date') : []),
                    datasets: [{
                        label: 'Emails envoyés',
                        data: @json(isset($emailStats['trend']) ? array_column($emailStats['trend'], 'total') : []),
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Emails réussis',
                        data: @json(isset($emailStats['trend']) ? array_column($emailStats['trend'], 'success') : []),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Emails échoués',
                        data: @json(isset($emailStats['trend']) ? array_column($emailStats['trend'], 'failed') : []),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
    </script>
@else
    <div class="alert alert-warning">
        <strong>Debug:</strong> $emailStats n'est pas défini dans le contrôleur.
    </div>
@endif
