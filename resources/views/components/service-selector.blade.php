@props([
    'name' => 'service_id',
    'selected' => null,
    'required' => false,
    'placeholder' => 'Sélectionner un service',
    'multiple' => false,
    'class' => 'form-select'
])

@php
    $services = App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->orderBy('nom')->get();
@endphp

<div class="service-selector">
    <label class="form-label">
        <i class="fas fa-building me-2"></i>{{ $label ?? 'Service' }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
    
    @if($multiple)
        <select name="{{ $name }}[]" class="{{ $class }}" @if($required) required @endif multiple>
            <option value="">{{ $placeholder }}</option>
            @foreach($services as $service)
                <option value="{{ $service->id }}" 
                        @if(is_array($selected) && in_array($service->id, $selected)) selected @endif>
                    {{ $service->nom }} ({{ $service->email }})
                </option>
            @endforeach
        </select>
    @else
        <select name="{{ $name }}" class="{{ $class }}" @if($required) required @endif>
            <option value="">{{ $placeholder }}</option>
            @foreach($services as $service)
                <option value="{{ $service->id }}" 
                        @if($selected == $service->id) selected @endif>
                    {{ $service->nom }} ({{ $service->email }})
                </option>
            @endforeach
        </select>
    @endif
    
    @if(isset($help))
        <small class="form-text text-muted">{{ $help }}</small>
    @endif
</div>

<script>
// Initialiser le sélecteur avec Select2 si disponible
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.querySelector('{{ $multiple ? "select[name=\'{$name}[]\']" : "select[name=\'{$name}\']" }}');
    if (selector && typeof $ !== 'undefined' && $.fn.select2) {
        $(selector).select2({
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
