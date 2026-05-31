<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $users = [
            [
                'name' => 'Haris',
                'username' => 'admin',
                'email' => 'admin@tandisbakery.com',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'Karyawan Toko',
                'username' => 'karyawan',
                'email' => 'karyawan@tandisbakery.com',
                'password' => 'karyawan123',
                'role' => 'karyawan',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                $data
            );
        }
    }

    public function down(): void
    {
        User::whereIn('username', ['admin', 'karyawan'])->delete();
    }
};
