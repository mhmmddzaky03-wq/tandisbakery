@props(['title' => null])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @if ($title)
        <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">{{ $title }}</div>
    @endif
    {{ $slot }}
</div>
