@props([
    'title' => '',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-[1.65rem]">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm font-medium text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions) && ! $actions->isEmpty())
        <div class="flex shrink-0 flex-wrap items-center justify-end gap-2 sm:gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
