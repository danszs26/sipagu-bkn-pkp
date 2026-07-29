<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::orderBy('nama_vendor')->get();
        return view('settings.vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $vendor = Vendor::create($validated + ['created_by' => $request->user()->id]);

        ActivityLog::catat('created', 'Vendor', $vendor->id, "Membuat vendor \"{$vendor->nama_vendor}\"");

        return redirect()->route('settings.vendors.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $vendor->update($validated);

        return redirect()->route('settings.vendors.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->transactions()->exists()) {
            return back()->with('error', "Vendor \"{$vendor->nama_vendor}\" masih dipakai di salah satu transaksi, tidak bisa dihapus. Nonaktifkan saja.");
        }

        ActivityLog::catat('deleted', 'Vendor', $vendor->id, "Menghapus vendor \"{$vendor->nama_vendor}\"");

        $vendor->delete();

        return redirect()->route('settings.vendors.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
