@props([
    'name' => '',
    'id' => null,
    'rows' => 3,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'class' => '',
])

@php
    $id = $id ?? $name;
    $classes = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ' .
               ($errors->has($name) ? 'border-red-500' : 'border-gray-300') . ' ' . $class;
@endphp

<textarea
    name="{{ $name }}"
    id="{{ $id }}"
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    {{ $attributes->merge(['class' => $classes]) }}
>{{ $slot }}</textarea>

@error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
