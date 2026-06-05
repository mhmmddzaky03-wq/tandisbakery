@props([
    'title' => '',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:mb-7 sm:flex-row sm:items-start sm:justify-between sm:gap-6']) }}>
    <div class="min-w-0 flex-1">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl lg:text-[1.65rem]">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1.5 text-sm font-medium leading-relaxed text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions) && ! $actions->isEmpty())
        <div class="flex w-full shrink-0 flex-wrap items-stretch gap-2 sm:w-auto sm:items-center sm:justify-end sm:gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
