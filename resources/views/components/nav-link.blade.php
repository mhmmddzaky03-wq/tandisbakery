@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    class="{{ $active ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200/70' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }} group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition"
>
    <span class="{{ $active ? 'text-amber-600' : 'text-slate-400 group-hover:text-slate-500' }} grid h-8 w-8 place-items-center rounded-xl bg-white ring-1 ring-black/5">
        {!! $icon !!}
    </span>
    <span class="truncate">{{ $slot }}</span>
</a>

