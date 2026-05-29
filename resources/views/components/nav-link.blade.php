@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    class="{{ $active ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }} group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition"
>
    <span class="{{ $active ? 'bg-white/20 text-white ring-0' : 'text-slate-400 group-hover:text-slate-500 bg-white ring-1 ring-black/5' }} grid h-8 w-8 place-items-center rounded-xl">
        {!! $icon !!}
    </span>
    <span class="truncate">{{ $slot }}</span>
</a>

