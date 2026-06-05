@php
    use App\Support\FormatHelper;

    $editMaterialRows = old('materials');
    if ($editMaterialRows === null) {
        $editMaterialRows = $r->materialUsages->map(fn ($u) => [
            'raw_material_id' => $u->raw_material_id,
            'raw_material_restock_id' => $u->raw_material_restock_id,
            'jumlah' => FormatHelper::formatQtyInput($u->jumlah),
            'satuan' => $u->satuan,
        ])->values()->all();
    }
    $editBahanDasarRows = old('bahan_dasar');
    if ($editBahanDasarRows === null) {
        $editBahanDasarRows = $r->bahanDasarUsages->map(fn ($u) => [
            'bahan_dasar_id' => $u->bahan_dasar_id,
            'batch_bahan_dasar_id' => $u->batch_bahan_dasar_id,
            'jumlah' => FormatHelper::formatQtyInput($u->jumlah),
            'satuan' => $u->satuan,
        ])->values()->all();
    }
    $isEditTarget = old('_edit_id') === $r->id;
    $hasProductionErrors = $errors->has('tanggal') || $errors->has('product_name') || $errors->has('jumlah') || $errors->has('status') || $errors->has('notes') || $errors->has('use_bahan_dasar') || $errors->has('materials') || $errors->has('materials.*') || $errors->has('bahan_dasar') || $errors->has('bahan_dasar.*');
@endphp
<x-modal id="edit-prod-{{ $r->id }}" size="lg" :title="__('production.modal_edit')" :subtitle="$r->id" :scrollable="true" :auto-open="$isEditTarget && $hasProductionErrors">
    <form id="form-edit-prod-{{ $r->id }}" method="POST" action="{{ route($updateRoute, $r->id) }}" data-modal-form data-production-form>
        @csrf @method('PUT')
        <input type="hidden" name="_edit_id" value="{{ $r->id }}" />
        @include('partials.production-form-body', [
            'prefix' => $r->id,
            'recordId' => $r->id,
            'tanggal' => $r->tanggal->format('Y-m-d'),
            'productName' => $r->product_name,
            'jumlah' => $r->jumlah,
            'satuan' => $r->satuan,
            'status' => $r->status,
            'notes' => $r->notes,
            'materials' => $materials,
            'materialRows' => $editMaterialRows,
            'bahanDasarItems' => $bahanDasarItems ?? collect(),
            'bahanDasarRows' => $editBahanDasarRows,
            'linkedProductId' => $r->product?->id,
            'autofocus' => true,
        ])
    </form>
    <x-slot:footer>
        <x-form-actions :form="'form-edit-prod-'.$r->id" compact />
    </x-slot:footer>
</x-modal>
