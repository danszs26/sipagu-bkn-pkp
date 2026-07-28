<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AnggaranTahun;
use App\Models\BudgetCategory;
use App\Models\MasterKomponen;
use Illuminate\Http\Request;

class BudgetCategoryController extends Controller
{
    public function index(Request $request)
    {
        $tahunList = AnggaranTahun::orderByDesc('tahun')->get();

        // Default: tahun yang sedang aktif, atau tahun terbaru kalau belum ada yang aktif
        $tahunTerpilih = $request->filled('tahun_anggaran_id')
            ? $tahunList->firstWhere('id', (int) $request->input('tahun_anggaran_id'))
            : ($tahunList->firstWhere('is_active', true) ?? $tahunList->first());

        $categories = $tahunTerpilih
            ? BudgetCategory::with('masterKomponen')
                ->where('tahun_anggaran_id', $tahunTerpilih->id)
                ->orderBy('master_komponen_id')
                ->get()
            : collect();

        return view('budget-categories.index', compact('categories', 'tahunList', 'tahunTerpilih'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', BudgetCategory::class);

        $tahunId = $request->query('tahun_anggaran_id');
        $tahun = $tahunId ? AnggaranTahun::find($tahunId) : AnggaranTahun::where('is_active', true)->first();

        if (!$tahun) {
            return redirect()->route('settings.anggaran-tahun.index')
                ->with('error', 'Buat dan aktifkan Tahun Anggaran terlebih dahulu sebelum menambah pagu.');
        }

        $komponenList = MasterKomponen::where('is_active', true)->orderBy('kode')->get();

        return view('budget-categories.create', compact('tahun', 'komponenList'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BudgetCategory::class);

        $validated = $request->validate([
            'tahun_anggaran_id' => ['required', 'exists:anggaran_tahun,id'],
            'master_komponen_id' => ['required', 'exists:master_komponen,id'],
            'uraian' => ['required', 'string', 'max:255'],
            'pagu_anggaran' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $category = BudgetCategory::create($validated + ['created_by' => $request->user()->id]);

        ActivityLog::catat(
            action: 'created',
            modelType: 'BudgetCategory',
            modelId: $category->id,
            description: "Membuat pagu anggaran \"{$category->uraian}\" sebesar Rp " . number_format($category->pagu_anggaran, 0, ',', '.'),
        );

        return redirect()->route('budget-categories.index', ['tahun_anggaran_id' => $category->tahun_anggaran_id])
            ->with('success', 'Pagu anggaran berhasil dibuat.');
    }

    public function update(Request $request, BudgetCategory $budgetCategory)
    {
        $this->authorize('update', $budgetCategory);

        $old = $budgetCategory->only(['pagu_anggaran', 'uraian', 'master_komponen_id', 'is_active']);

        $validated = $request->validate([
            'master_komponen_id' => ['required', 'exists:master_komponen,id'],
            'uraian' => ['required', 'string', 'max:255'],
            'pagu_anggaran' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $budgetCategory->update($validated);

        ActivityLog::catat(
            action: 'updated',
            modelType: 'BudgetCategory',
            modelId: $budgetCategory->id,
            description: "Mengubah pagu anggaran \"{$budgetCategory->uraian}\"",
            oldData: $old,
            newData: $budgetCategory->only(['pagu_anggaran', 'uraian', 'master_komponen_id', 'is_active']),
        );

        return redirect()->route('budget-categories.index', ['tahun_anggaran_id' => $budgetCategory->tahun_anggaran_id])
            ->with('success', 'Pagu anggaran berhasil diperbarui.');
    }
}
