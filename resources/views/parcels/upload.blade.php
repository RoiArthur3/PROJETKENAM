@extends('layouts.app')

@section('title', 'Upload photos')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Upload photos</h1>
        <p class="text-sm text-gray-500">Ajoutez des photos pour le colis #{{ $parcel->tracking_number }}</p>
    </div>

    <form action="{{ route('parcels.upload', $parcel->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700" for="photos">Photos</label>
            <input id="photos" name="photos[]" type="file" multiple accept="image/*" class="input-file">
            <p class="text-xs text-gray-500">Formats acceptés: JPEG, PNG. Max 5MB par photo.</p>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700" for="type">Type de photos</label>
            <select id="type" name="type" class="input-select">
                <option value="package">Emballage du colis</option>
                <option value="content">Contenu du colis</option>
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Upload
            </button>
        </div>
    </form>
</div>
@endsection
