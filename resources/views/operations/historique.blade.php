@extends('layouts.app')

@section('title', 'Opérations - Historique')

@section('content')
<div class="container-fluid">
    @php
        use App\Models\OperationStatusLog;
        use Illuminate\Support\Str;

        $qOp = request('op');
        $qUser = trim((string)request('user', ''));
        $qStatus = request('status');
        $qFrom = request('from');
        $qTo = request('to');

        $logs = OperationStatusLog::with('operation')
            ->when($qOp && ctype_digit((string)$qOp), fn($qr) => $qr->where('operation_id', (int)$qOp))
            ->when($qUser !== '', fn($qr) => $qr->where('user_name', 'like', '%'.$qUser.'%'))
            ->when($qStatus !== null && $qStatus !== '', function($qr) use ($qStatus) {
                return $qr->where('to_status', $qStatus);
            })
            ->when($qFrom, fn($qr) => $qr->whereDate('created_at', '>=', $qFrom))
            ->when($qTo, fn($qr) => $qr->whereDate('created_at', '<=', $qTo))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends(request()->query());
    @endphp

    <div class="card shadow mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET">
                <div class="col-md-2">
                    <label class="form-label small">Opération #</label>
                    <input type="number" class="form-control form-control-sm" name="op" value="{{ request('op') }}" placeholder="ID">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Utilisateur</label>
                    <input type="text" class="form-control form-control-sm" name="user" value="{{ request('user') }}" placeholder="Nom ou email">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Statut</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">Tous</option>
                        @foreach(['pending_validation','in_progress','controlled','rejected','approved','en_cours','rejete','termine'] as $st)
                            <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ str_replace('_',' ', ucfirst($st)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Du</label>
                    <input type="date" class="form-control form-control-sm" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Au</label>
                    <input type="date" class="form-control form-control-sm" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2 justify-content-end">
                    <a class="btn btn-sm btn-outline-success" href="{{ route('operations.historique.export', request()->query()) }}">
                        <i class="fas fa-file-csv me-1"></i>Exporter CSV
                    </a>
                    <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-filter me-1"></i>Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Opération</th>
                            <th>De</th>
                            <th>À</th>
                            <th>Utilisateur</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">#{{ str_pad($log->operation_id,5,'0',STR_PAD_LEFT) }}</span>
                                </td>
                                <td><span class="badge bg-secondary">{{ $log->from_status ?? '—' }}</span></td>
                                <td>
                                    <span class="badge {{ in_array($log->to_status, ['approved','termine','in_progress','en_cours']) ? 'bg-success' : (in_array($log->to_status, ['rejected','rejete']) ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $log->to_status }}
                                    </span>
                                </td>
                                <td>{{ $log->user_name ?? 'Système' }}</td>
                                <td class="text-truncate" style="max-width: 320px;" title="{{ $log->commentaire }}">{{ Str::limit($log->commentaire, 60) }}</td>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('operations.show', ['operation' => $log->operation_id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <div>Aucun historique trouvé.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
