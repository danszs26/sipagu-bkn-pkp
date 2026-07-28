<?php

namespace Database\Seeders;

use App\Models\PejabatPenandatangan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PejabatSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        PejabatPenandatangan::firstOrCreate(
            ['nama' => 'Eko Nugroho, S.Psi.'],
            [
                'jabatan' => 'Kepala UPT BKN Pangkalpinang',
                'nip' => '', // TODO: isi NIP asli lewat Settings > Pejabat Penandatangan
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );
    }
}
