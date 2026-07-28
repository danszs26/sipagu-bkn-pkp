<?php

namespace Database\Seeders;

use App\Models\AnggaranTahun;
use App\Models\MasterKomponen;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Master Komponen sesuai dokumen pagu anggaran
        $komponen = [
            ['kode' => 'F', 'nama_komponen' => 'Laporan Pelaksanaan Layanan Rumah Tangga'],
            ['kode' => 'G', 'nama_komponen' => 'Ketertiban Lingkungan'],
            ['kode' => 'H', 'nama_komponen' => 'Laporan Layanan Daya dan Jasa'],
            ['kode' => 'I', 'nama_komponen' => 'Pengelolaan Arsip dan Persuratan'],
            ['kode' => 'M', 'nama_komponen' => 'Kantor UPT BKN Bangka Belitung'],
            ['kode' => 'Q', 'nama_komponen' => 'Bangka Belitung'],
            ['kode' => 'R', 'nama_komponen' => 'Laporan Perawatan Gedung Kantor'],
        ];

        foreach ($komponen as $k) {
            MasterKomponen::firstOrCreate(['kode' => $k['kode']], $k);
        }

        // Tahun Anggaran 2026, langsung dijadikan tahun aktif
        if ($admin) {
            AnggaranTahun::firstOrCreate(
                ['tahun' => 2026],
                ['is_active' => true, 'created_by' => $admin->id]
            );
        }
    }
}
