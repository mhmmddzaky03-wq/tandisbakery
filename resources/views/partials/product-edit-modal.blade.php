@php
    $editProductId = old('_edit_id');
    $hasProductErrors = $errors->has('harga');
    $isEditTarget = $editProductId === $product->id;
    $batchCount = $productionBatchCount ?? \App\Http\Controllers\ProductController::productionsForProduct($product)->count();
@endphp
<x-modal
    id="edit-produk-{{ $product->id }}"
    title="Edit Produk"
    :subtitle="$product->id"
    size="lg"
    :scrollable="true"
    :auto-open="$isEditTarget && $hasProductErrors"
>
    <form method="POST" action="{{ route($updateRoute, $product->id) }}" class="space-y-3" data-modal-form>
        @csrf @method('PUT')
        <input type="hidden" name="_edit_id" value="{{ $product->id }}" />

        <div class="rounded-lg bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
            <div class="min-w-0">
                <div class="truncate font-bold text-slate-800">{{ $product->nama }}</div>
                <div class="mt-0.5 text-xs font-semibold text-slate-500">
                    {{ $product->satuan }} · Stok {{ number_format($product->jumlah, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-emerald-50/70 px-3 py-3 ring-1 ring-emerald-100">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">Stok Saat Ini</p>
                    <p class="mt-1 text-2xl font-extrabold tabular-nums text-emerald-800">
                        {{ number_format($product->jumlah, 0, ',', '.') }}
                        <span class="text-sm font-bold uppercase text-emerald-700">{{ $product->satuan }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700/80">Batch Produksi</p>
                    <p class="mt-1 text-sm font-extrabold text-emerald-800">{{ $batchCount }} entri</p>
                </div>
            </div>
            <p class="mt-2 text-[11px] font-semibold text-emerald-700/80">Stok bertambah otomatis setiap produksi berhasil dengan nama produk yang sama.</p>
        </div>

        <div class="min-w-0">
            <label for="field-harga-edit-{{ $product->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                Harga Jual
                <span class="text-rose-500" aria-hidden="true">*</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="inline-flex h-11 shrink-0 items-center rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-600">Rp</span>
                <input
                    id="field-harga-edit-{{ $product->id }}"
                    name="harga"
                    type="number"
                    value="{{ old('harga', $product->harga) }}"
                    min="0"
                    required
                    inputmode="numeric"
                    class="bakery-input h-11 min-w-0 flex-1 {{ $errors->has('harga') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                />
            </div>
            @if ($errors->has('harga') && $isEditTarget)
                <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('harga') }}</p>
            @endif
        </div>

        <x-form-actions compact submit="Simpan Perubahan" />
    </form>
</x-modal>
