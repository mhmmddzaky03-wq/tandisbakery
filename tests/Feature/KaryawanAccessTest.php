<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryawanAccessTest extends TestCase
{
    use RefreshDatabase;

    private function karyawan(): User
    {
        $this->seed();

        return User::where('username', 'karyawan')->firstOrFail();
    }

    public function test_karyawan_can_access_production_and_sales(): void
    {
        $this->actingAs($this->karyawan());

        $this->get(route('karyawan.produksi'))->assertOk();
        $this->get(route('karyawan.penjualan'))->assertOk();
        $this->get(route('karyawan.dashboard'))->assertOk();
    }

    public function test_karyawan_cannot_access_removed_modules(): void
    {
        $this->actingAs($this->karyawan());

        $this->get('/karyawan/input-persediaan')->assertNotFound();
        $this->get('/karyawan/input-operasional')->assertNotFound();
        $this->get('/karyawan/data-produk')->assertNotFound();
        $this->get(route('admin.dashboard'))->assertRedirect(route('karyawan.dashboard'));
    }
}
