<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AnggaranTahun;
use Illuminate\Http\Request;

class AnggaranTahunController extends Controller
{
    public function index()
    {
        $tahunList = AnggaranTahun::withCount('budgetCategories')->orderByDesc('tahun')->get();
        return view('settings.anggaran-tahun.index', compact('tahunList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100', 'unique:anggaran_tahun,tahun'],
        ]);

        $tahun = AnggaranTahun::create($validated + ['created_by' => $request->user()->id]);

        ActivityLog::catat('created', 'AnggaranTahun', $tahun->id, "Membuat Tahun Anggaran {$tahun->tahun}");

        return redirect()->route('settings.anggaran-tahun.index')->with('success', "Tahun Anggaran {$tahun->tahun} berhasil dibuat.");
    }

    /**
     * Jadikan tahun ini sebagai tahun yang sedang berjalan (dipakai default di form transaksi).
     * Otomatis menonaktifkan tahun lain.
     */
    public function aktifkan(AnggaranTahun $anggaranTahun)
    {
        AnggaranTahun::where('is_active', true)->update(['is_active' => false]);
        $anggaranTahun->update(['is_active' => true]);

        ActivityLog::catat('updated', 'AnggaranTahun', $anggaranTahun->id, "Mengaktifkan Tahun Anggaran {$anggaranTahun->tahun} sebagai tahun berjalan");

        return redirect()->route('settings.anggaran-tahun.index')->with('success', "Tahun Anggaran {$anggaranTahun->tahun} sekarang aktif.");
    }
}
