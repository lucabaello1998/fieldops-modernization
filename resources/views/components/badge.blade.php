@props(['tone' => 'slate', 'dot' => true])

@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        'brand' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300',
        'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
        'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
        'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
        'sky' => 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
    ];
    $dots = [
        'slate' => 'bg-slate-400',
        'brand' => 'bg-brand-500',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'sky' => 'bg-sky-500',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . ($tones[$tone] ?? $tones['slate'])]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dots[$tone] ?? $dots['slate'] }}"></span>
    @endif
    {{ $slot }}
</span>
