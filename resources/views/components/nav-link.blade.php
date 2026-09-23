@props(['active' => false, 'href' => '#'])

@php
$classes = $active 
    ? 'flex items-center w-full px-4 py-2.5 text-sm font-bold text-indigo-700 bg-indigo-50/80 rounded-xl transition-all duration-200 shadow-sm shadow-indigo-100/30' 
    : 'flex items-center w-full px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/50 rounded-xl transition-all duration-200 group';
@endphp

<a {{ $attributes->merge(['href' => $href]) }} class="{{ $classes }}">
    {{ $slot }}
</a>