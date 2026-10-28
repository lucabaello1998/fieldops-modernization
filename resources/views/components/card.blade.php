@props(['title' => null, 'sub' => null, 'pad' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title)
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <div class="min-w-0">
                <h3 class="card-title truncate">{{ $title }}</h3>
                @if ($sub)
                    <p class="card-sub mt-0.5">{{ $sub }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $pad ? 'card-pad' : '' }}">{{ $slot }}</div>
</div>
