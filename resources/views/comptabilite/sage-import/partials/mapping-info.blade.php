{{-- Partial: tableau de mapping colonnes SAGE → DB --}}
{{-- Variables: $title (string), $rows (array of [sage_col, db_col]) --}}

<div class="card shadow-sm border-0 bg-light h-100">
    <div class="card-header bg-white">
        <h6 class="mb-0 text-muted"><i class="fas fa-exchange-alt me-2"></i>{{ $title }}</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Colonne SAGE i7</th>
                    <th>Champ dans la base</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as [$sage, $db])
                    <tr>
                        <td class="ps-3"><code class="text-primary">{{ $sage }}</code></td>
                        <td class="text-muted small">{{ $db }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white text-muted small">
        Les colonnes non reconnues sont ignorées. Les noms alternatifs (ex: <code>Code</code>, <code>Intitule</code>) sont aussi acceptés.
    </div>
</div>
