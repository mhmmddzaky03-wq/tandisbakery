@php use App\Support\FormatHelper; @endphp
<div class="pt-6">
    <div class="grid gap-4 lg:grid-cols-3 mb-5">
        <div class="bakery-card p-5"><div class="text-xs text-slate-400">{{ __('page.total_cost') }}</div><div class="text-2xl font-extrabold">{{ FormatHelper::rupiah($totalCost) }}</div></div>
        <div class="bakery-card p-5"><div class="text-xs text-slate-400">{{ __('page.fixed_cost_total') }}</div><div class="text-2xl font-extrabold text-sky-600">{{ FormatHelper::rupiah($fixedTotal) }}</div></div>
        <div class="bakery-card p-5"><div class="text-xs text-slate-400">{{ __('page.variable_cost_total') }}</div><div class="text-2xl font-extrabold text-amber-600">{{ FormatHelper::rupiah($variableTotal) }}</div></div>
    </div>
    <div class="bakery-card">
        <div class="bakery-card-header flex-wrap gap-3">
            <div class="text-lg font-extrabold">{{ __('page.cost_list_title') }}</div>
            <div class="flex flex-wrap gap-2">
                <a href="?" class="bakery-btn-ghost text-xs {{ empty($filter) ? 'ring-2 ring-amber-300' : '' }}">{{ __('page.all') }}</a>
                <a href="?jenis=Fixed" class="bakery-btn-ghost text-xs {{ ($filter ?? '') === 'Fixed' ? 'ring-2 ring-amber-300' : '' }}">{{ __('page.fixed_cost') }}</a>
                <a href="?jenis=Variable" class="bakery-btn-ghost text-xs {{ ($filter ?? '') === 'Variable' ? 'ring-2 ring-amber-300' : '' }}">{{ __('page.variable_cost') }}</a>
            </div>
            <button type="button" class="bakery-btn-primary" data-modal-open="cost-baru">{{ __('page.add_cost') }}</button>
        </div>
        <div class="bakery-card-body">
            <form method="GET" class="mb-4 flex gap-2"><input class="bakery-input flex-1" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('page.search_cost') }}" /><button class="bakery-btn-ghost">Cari</button></form>
            <table class="bakery-table">
                <thead><tr><th>ID</th><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Jumlah</th><th>Jenis</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach ($costs as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ FormatHelper::dateId($c->tanggal) }}</td>
                            <td>{{ $c->kat }}</td>
                            <td class="max-w-xs truncate">{{ $c->desk }}</td>
                            <td class="font-extrabold text-rose-600">{{ FormatHelper::rupiah($c->jumlah) }}</td>
                            <td><span class="bakery-badge {{ $c->jenis === 'Fixed' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">{{ $c->jenis === 'Fixed' ? __('page.fixed_cost') : __('page.variable_cost') }}</span></td>
                            <td><button type="button" class="bakery-btn-ghost text-xs" data-modal-open="edit-cost-{{ $c->id }}">Edit</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($costs as $c)
        <x-modal id="edit-cost-{{ $c->id }}" title="Ubah Biaya" :subtitle="$c->id">
            <form method="POST" action="{{ route($updateRoute, $c->id) }}" data-modal-form>
                @csrf @method('PUT')
                <x-form-field label="Tanggal" name="tanggal" type="date" :value="$c->tanggal->format('Y-m-d')" required autofocus />
                <x-form-field label="Kategori" name="kat" :value="$c->kat" required helper="Contoh: Kemasan, Air, Gaji, Lainnya" />
                <x-form-field label="Deskripsi" name="desk" type="textarea" :value="$c->desk" required />
                <x-form-field label="Nominal (Rp)" name="jumlah" type="number" :value="$c->jumlah" min="0" required />
                <x-form-field label="Jenis biaya" name="jenis" type="select" required>
                    <option value="Fixed" @selected($c->jenis === 'Fixed')>Tetap (Fixed)</option>
                    <option value="Variable" @selected($c->jenis === 'Variable')>Variabel</option>
                </x-form-field>
                <x-form-actions />
            </form>
        </x-modal>
    @endforeach

    <x-modal id="cost-baru" title="Tambah Biaya Operasional" subtitle="Biaya tetap atau variabel untuk laporan laba rugi" :auto-open="$errors->has('tanggal') || $errors->has('kat')">
        <form method="POST" action="{{ route($storeRoute) }}" data-modal-form>
            @csrf
            <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
            <x-form-field label="Kategori" name="kat" :value="old('kat')" required placeholder="Kemasan" />
            <x-form-field label="Deskripsi" name="desk" type="textarea" :value="old('desk')" required placeholder="Contoh: Pembelian kemasan pasar" />
            <x-form-field label="Nominal (Rp)" name="jumlah" type="number" :value="old('jumlah')" min="0" required />
            <x-form-field label="Jenis biaya" name="jenis" type="select" required>
                <option value="Variable" @selected(old('jenis', 'Variable') === 'Variable')>Variabel</option>
                <option value="Fixed" @selected(old('jenis') === 'Fixed')>Tetap (Fixed)</option>
            </x-form-field>
            <x-form-actions />
        </form>
    </x-modal>
</div>
