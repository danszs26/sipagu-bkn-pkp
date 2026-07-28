<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PejabatPenandatangan;
use Illuminate\Http\Request;

class PejabatController extends Controller
{
    public function index()
    {
        $pejabatList = PejabatPenandatangan::orderByDesc('is_active')->orderBy('nama')->get();
        return view('settings.pejabat.index', compact('pejabatList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'jabatan' => ['required', 'string', 'max:150'],
            'nip' => ['nullable', 'string', 'max:30'],
        ]);

        // Kalau ini pejabat pertama, otomatis jadi aktif
        $isFirst = PejabatPenandatangan::count() === 0;

        $pejabat = PejabatPenandatangan::create($validated + [
            'is_active' => $isFirst,
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::catat('created', 'PejabatPenandatangan', $pejabat->id, "Menambah pejabat penandatangan \"{$pejabat->nama}\"");

        return redirect()->route('settings.pejabat.index')->with('success', 'Pejabat penandatangan berhasil ditambahkan.');
    }

    public function update(Request $request, PejabatPenandatangan $pejabat)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'jabatan' => ['required', 'string', 'max:150'],
            'nip' => ['nullable', 'string', 'max:30'],
        ]);

        $pejabat->update($validated);

        ActivityLog::catat('updated', 'PejabatPenandatangan', $pejabat->id, "Mengubah data pejabat penandatangan \"{$pejabat->nama}\"");

        return redirect()->route('settings.pejabat.index')->with('success', 'Data pejabat berhasil diperbarui.');
    }

    /**
     * Jadikan pejabat ini yang aktif (dipakai di laporan resmi). Otomatis
     * menonaktifkan pejabat lain, karena hanya boleh ada satu yang aktif.
     */
    public function aktifkan(PejabatPenandatangan $pejabat)
    {
        PejabatPenandatangan::where('is_active', true)->update(['is_active' => false]);
        $pejabat->update(['is_active' => true]);

        ActivityLog::catat('updated', 'PejabatPenandatangan', $pejabat->id, "Mengaktifkan \"{$pejabat->nama}\" sebagai penandatangan laporan");

        return redirect()->route('settings.pejabat.index')->with('success', "\"{$pejabat->nama}\" sekarang jadi penandatangan aktif.");
    }
}
