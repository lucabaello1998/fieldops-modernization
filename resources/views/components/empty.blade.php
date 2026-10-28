@props(['title', 'sub' => null, 'icon' => 'search'])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <span class="grid h-12 w-12 place-items-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <p class="mt-4 text-sm font-medium text-slate-900 dark:text-slate-200">{{ $title }}</p>
    @if ($sub)
        <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">{{ $sub }}</p>
    @endif
    @isset($slot)
        <div class="mt-5">{{ $slot }}</div>
    @endisset
</div>
