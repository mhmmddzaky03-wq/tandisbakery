<?php

namespace App\Http\Controllers;

use App\Models\ProductionRecord;
use App\Support\FormatHelper;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = ProductionRecord::query()->with('product');

        $allRecords = ProductionRecord::all();
        $total      = $allRecords->count();
        $sukses     = $allRecords->where('status', 'Berhasil')->count();
        $gagal      = $allRecords->where('status', 'Gagal')->count();
        $rate       = $total > 0 ? round(($sukses / $total) * 100, 1).'%' : '0%';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $records = $query->orderByDesc('tanggal')->orderByDesc('id')->get();

        $stats = [
            [
                'label' => __('page.total_production'),
                'value' => $total,
                'tone'  => 'blue',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
            ],
            [
                'label' => __('page.production_success'),
                'value' => $sukses,
                'tone'  => 'green',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
            ],
            [
                'label' => __('page.production_failed'),
                'value' => $gagal,
                'tone'  => 'rose',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
            ],
            [
                'label' => __('page.success_rate'),
                'value' => $rate,
                'tone'  => 'amber',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>',
            ],
        ];

        $role     = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.produksi' : 'karyawan.produksi';

        return view($viewName, compact('records', 'search', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'satuan'       => ['required', 'string', 'max:50'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['product_name']);
        $data['id'] = ProductionRecord::generateNextId();

        ProductionRecord::create($data);

        return redirect()->back()->with('success', __('ui.flash_production_created'));
    }

    public function update(Request $request, $id)
    {
        $record = ProductionRecord::findOrFail($id);

        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'satuan'       => ['required', 'string', 'max:50'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['product_name']);

        if ($data['status'] === 'Gagal' && $record->product) {
            return redirect()->back()->withErrors([
                'status' => 'Produksi ini sudah terdaftar sebagai produk. Ubah status produk terlebih dahulu atau hapus produk terkait.',
            ]);
        }

        $record->update($data);

        if ($record->product) {
            $record->product->update([
                'nama'   => $record->product_name,
                'satuan' => $record->satuan,
            ]);
        }

        return redirect()->back()->with('success', __('ui.flash_production_updated'));
    }

    public function destroy($id)
    {
        $record = ProductionRecord::with('product')->findOrFail($id);

        if ($record->product) {
            return redirect()->back()->withErrors([
                'delete' => __('page.cannot_delete_linked'),
            ]);
        }

        $record->delete();

        return redirect()->back()->with('success', __('ui.flash_production_deleted'));
    }
}
