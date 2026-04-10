<!-- Statistiques SMS Dashboard -->
@if(isset($smsStats))
    <div class="alert alert-info mb-3">
        <strong>Debug SMS Stats:</strong>
        Today: {{ $smsStats['today']['count'] ?? 'undefined' }} |
        Success Rate: {{ $smsStats['today']['success_rate'] ?? 'undefined' }}%
    </div>

    <div class="row mb-4">
        <!-- Carte SMS du jour -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                SMS Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['today']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $smsStats['today']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sms fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte SMS de la semaine -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                SMS Semaine
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['week']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $smsStats['week']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte SMS du mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                SMS Mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['month']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $smsStats['month']['success_rate'] ?? 0 }}% succès
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte SMS Total -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total SMS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['total']['count'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $smsStats['total']['success_rate'] ?? 0 }}% succès
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
@else
    <div class="alert alert-warning">
        <strong>Debug:</strong> $smsStats n'est pas défini dans le contrôleur.
    </div>
@endif
