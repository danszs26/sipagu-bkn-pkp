<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MasterKomponen;
use Illuminate\Http\Request;

class MasterKomponenController extends Controller
{
    public function index()
    {
        $komponenList = MasterKomponen::orderBy('kode')->get();
        return view('settings.master-komponen.index', compact('komponenList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:master_komponen,kode'],
            'nama_komponen' => ['nullable', 'string', 'max:255'],
        ]);

        $komponen = MasterKomponen::create($validated);

        ActivityLog::catat('created', 'MasterKomponen', $komponen->id, "Membuat Master Komponen {$komponen->kode}");

        return redirect()->route('settings.master-komponen.index')->with('success', 'Master Komponen berhasil dibuat.');
    }

    public function update(Request $request, MasterKomponen $masterKomponen)
    {
        $validated = $request->validate([
            'nama_komponen' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $masterKomponen->update($validated);

        return redirect()->route('settings.master-komponen.index')->with('success', 'Master Komponen berhasil diperbarui.');
    }
}
