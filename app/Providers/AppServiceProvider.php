<?php

namespace App\Providers;

use App\Models\RawMaterial;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.topbar', function ($view): void {
            $role = $view->getData()['role'] ?? 'admin';

            if ($role !== 'admin') {
                $view->with('lowStockMaterials', collect());

                return;
            }

            $lowStockMaterials = RawMaterial::query()
                ->needsRestock()
                ->orderBy('nama')
                ->get(['id', 'nama', 'jumlah', 'min', 'satuan']);

            $view->with('lowStockMaterials', $lowStockMaterials);
        });
    }
}
