@props(['label', 'value', 'delta' => null, 'icon' => 'bolt', 'tone' => 'brand', 'hint' => null])

@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400',
    ];
@endphp

<div class="card card-pad">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="tnum mt-2 text-[26px] font-semibold leading-none tracking-tight">{{ $value }}</p>

            <div class="mt-2 flex items-center gap-2">
                @if (! is_null($delta))
                    @php $positivo = $delta >= 0; @endphp
                    <span class="inline-flex items-center gap-1 text-xs font-medium {{ $positivo ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        <x-icon :name="$positivo ? 'trend-up' : 'trend-down'" class="h-3.5 w-3.5" />
                        {{ number_format(abs($delta), 1, ',', '.') }}%
                    </span>
                @endif
                @if ($hint)
                    <span class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</span>
                @endif
            </div>
        </div>

        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg {{ $tones[$tone] ?? $tones['brand'] }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
</div>
