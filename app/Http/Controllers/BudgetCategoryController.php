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

        $tahunTerpilih = $request->filled('tahun_anggaran_id')
            ? $tahunList->firstWhere('id', (int) $request->input('tahun_anggaran_id'))
            : ($tahunList->firstWhere('is_active', true) ?? $tahunList->first());

        $categories = $tahunTerpilih
            ? BudgetCategory::with('masterKomponen')
                ->where('tahun_anggaran_id', $tahunTerpilih->id)
                ->orderBy('master_komponen_id')
                ->get()
            : collect();

        $perKomponen = $categories->groupBy('master_komponen_id')->map(function ($group) {
            return [
                'komponen' => $group->first()->masterKomponen,
                'items' => $group->sortBy('uraian')->values(),
                'total_pagu' => $group->sum('pagu_anggaran'),
                'total_terpakai' => $group->sum('total_terpakai'),
            ];
        })->sortBy(fn ($v) => $v['komponen']->kode ?? '');

        $komponenList = MasterKomponen::where('is_active', true)->orderBy('kode')->get();

        return view('budget-categories.index', compact('categories', 'tahunList', 'tahunTerpilih', 'perKomponen', 'komponenList'));
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
        ]);

        $validated['is_active'] = $request->boolean('is_active');

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

    public function copyFrom(Request $request)
    {
        $this->authorize('create', BudgetCategory::class);

        $validated = $request->validate([
            'dari_tahun_anggaran_id' => ['required', 'exists:anggaran_tahun,id'],
            'ke_tahun_anggaran_id' => ['required', 'exists:anggaran_tahun,id', 'different:dari_tahun_anggaran_id'],
        ]);

        $sumber = BudgetCategory::where('tahun_anggaran_id', $validated['dari_tahun_anggaran_id'])->get();

        $sudahAda = BudgetCategory::where('tahun_anggaran_id', $validated['ke_tahun_anggaran_id'])
            ->get()
            ->map(fn ($c) => $c->master_komponen_id . '|' . $c->uraian)
            ->toArray();

        $jumlahDisalin = 0;
        foreach ($sumber as $item) {
            $kunci = $item->master_komponen_id . '|' . $item->uraian;
            if (in_array($kunci, $sudahAda)) {
                continue; // sudah ada, jangan dobel
            }

            BudgetCategory::create([
                'tahun_anggaran_id' => $validated['ke_tahun_anggaran_id'],
                'master_komponen_id' => $item->master_komponen_id,
                'uraian' => $item->uraian,
                'pagu_anggaran' => 1, // placeholder, WAJIB diisi manual lewat Edit
                'total_terpakai' => 0,
                'keterangan' => $item->keterangan,
                'is_active' => true,
                'created_by' => $request->user()->id,
            ]);
            $jumlahDisalin++;
        }

        ActivityLog::catat(
            action: 'created',
            modelType: 'BudgetCategory',
            modelId: null,
            description: "Menyalin {$jumlahDisalin} komponen/uraian pagu dari tahun anggaran lain"
        );

        return redirect()->route('budget-categories.index', ['tahun_anggaran_id' => $validated['ke_tahun_anggaran_id']])
            ->with('success', "Berhasil menyalin {$jumlahDisalin} komponen/uraian. Nilai pagu masing-masing masih Rp1 (placeholder) — silakan isi nilai sebenarnya lewat tombol Edit di tiap kartu.");
    }
}
