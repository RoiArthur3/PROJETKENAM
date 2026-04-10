@php
    $services = \App\Models\Service::actif()->get();
    $hasServices = $services->isNotEmpty();
@endphp

@if($hasServices)
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Soumettre à des services</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('controleur.submit-to-services', $controle->id) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="service_ids" class="form-label">Sélectionnez les services</label>
                    <select name="service_ids[]" id="service_ids" class="form-select" multiple required>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ in_array($service->id, old('service_ids', $controle->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs services.</div>
                </div>
                
                <div class="mb-3">
                    <label for="commentaire_soumission" class="form-label">Commentaire (optionnel)</label>
                    <textarea name="commentaire_soumission" id="commentaire_soumission" rows="3" class="form-control">{{ old('commentaire_soumission', $controle->commentaire_soumission) }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Soumettre aux services
                </button>
            </form>
        </div>
    </div>
@else
    <div class="alert alert-warning mt-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Aucun service n'est actuellement configuré. Veuillez contacter l'administrateur.
    </div>
@endif

@push('scripts')
<script>
    // Activer le plugin Select2 pour une meilleure expérience utilisateur
    $(document).ready(function() {
        $('#service_ids').select2({
            placeholder: 'Sélectionnez un ou plusieurs services',
            width: '100%',
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
@endpush

@push('styles')
<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        padding: 3px 5px 0;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        margin-right: 5px;
        margin-top: 5px;
        padding: 0 5px;
    }
</style>
@endpush
