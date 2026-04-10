@extends('layouts.app')

@section('title', 'Notifications | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-bell me-2 text-warning"></i>Notifications
            </h1>
            <small class="text-muted">{{ $unreadCount }} notification(s) non lue(s)</small>
        </div>
        <div class="col-md-4 text-end">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-check-double me-1"></i>
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                <div class="d-flex align-items-start p-3 border-bottom {{ !$notification->is_read ? 'bg-light' : '' }}">
                    <div class="me-3">
                        @switch($notification->type)
                            @case('success')
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                                @break
                            @case('warning')
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                @break
                            @case('error')
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-times"></i>
                                </div>
                                @break
                            @default
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-info"></i>
                                </div>
                        @endswitch
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                    {{ $notification->title }}
                                </h6>
                                <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer comme lu">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune notification</p>
                </div>
            @endforelse
        </div>
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
