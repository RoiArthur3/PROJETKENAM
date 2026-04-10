@extends('layouts.app')

@section('title', 'Scan colis')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Scan colis</h1>
        <p class="text-sm text-gray-500">Scanner un colis pour le réceptionner</p>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex flex-col items-center justify-center py-12">
                <div class="mb-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l2-2m0 0l2-2m-2 2l2 2m-2-2l-2 2m9-2l2-2m0 0l2-2m-2 2l2 2m-2-2l-2 2m9-2l2-2m0 0l2-2m-2 2l2 2m-2-2l-2 2" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Scanner le code-barres du colis</p>
                </div>

                <form action="{{ route('admin.processScan') }}" method="POST" class="w-full max-w-md">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700" for="tracking_number">Numéro de suivi</label>
                        <input id="tracking_number" name="tracking_number" type="text" class="input-text" placeholder="GRP12345678" required autofocus>
                    </div>

                    <button type="submit" class="btn-primary w-full mt-4">
                        Valider
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
