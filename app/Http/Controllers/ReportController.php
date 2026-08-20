<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\ActivityLog;
use App\Models\AnggaranTahun;
use App\Models\BudgetCategory;
use App\Models\PejabatPenandatangan;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildQuery($request)->get();
        $categories = BudgetCategory::with('masterKomponen')->get();
        $vendors = Vendor::orderBy('nama_vendor')->get();
        $tahunList = AnggaranTahun::orderByDesc('tahun')->get();

        $summary = ['total_pengeluaran' => $data->sum('nominal')];

        $periode = $request->input('periode', 'harian');
        $grouped = $this->groupByPeriode($data, $periode);

        // === Total Pagu per Komponen/Akun ===
        $komponenTahun = $request->filled('komponen_tahun_id')
            ? $tahunList->firstWhere('id', (int) $request->input('komponen_tahun_id'))
            : ($tahunList->firstWhere('is_active', true) ?? $tahunList->first());

        $totalPerKomponen = collect();
        if ($komponenTahun) {
            $totalPerKomponen = BudgetCategory::with('masterKomponen')
                ->where('tahun_anggaran_id', $komponenTahun->id)
                ->get()
                ->groupBy('master_komponen_id')
                ->map(function ($group) {
                    return [
                        'komponen' => $group->first()->masterKomponen,
                        'jumlah_item' => $group->count(),
                        'total_pagu' => $group->sum('pagu_anggaran'),
                        'total_terpakai' => $group->sum('total_terpakai'),
                        'items' => $group->sortBy('uraian')->values(),
                    ];
                })
                ->sortBy(fn ($v) => $v['komponen']->kode ?? '');
        }

        return view('reports.index', compact(
            'data', 'categories', 'vendors', 'tahunList', 'summary', 'grouped', 'periode',
            'komponenTahun', 'totalPerKomponen'
        ));
    }

    protected function buildQuery(Request $request)
    {
        $query = Transaction::with(['budgetCategory.masterKomponen', 'budgetCategory.anggaranTahun', 'vendor', 'creator'])
            ->orderBy('tanggal');

        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal', '>=', $request->input('dari_tanggal'));
        }
        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal', '<=', $request->input('sampai_tanggal'));
        }
        if ($request->filled('budget_category_id')) {
            $query->where('budget_category_id', $request->input('budget_category_id'));
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }
        if ($request->filled('tahun_anggaran_id')) {
            $query->whereHas('budgetCategory', fn ($q) => $q->where('tahun_anggaran_id', $request->input('tahun_anggaran_id')));
        }

        return $query;
    }

    protected function groupByPeriode($data, string $periode)
    {
        return match ($periode) {
            'mingguan' => $data->groupBy(fn ($trx) => Carbon::parse($trx->tanggal)->startOfWeek()->format('Y-\WW')),
            'bulanan' => $data->groupBy(fn ($trx) => Carbon::parse($trx->tanggal)->format('Y-m')),
            default => $data->groupBy(fn ($trx) => Carbon::parse($trx->tanggal)->format('Y-m-d')),
        };
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('export', Transaction::class);
        $transactions = $this->buildQuery($request)->get();

        ActivityLog::catat('exported', 'Transaction', null, 'Export laporan ke Excel (' . $transactions->count() . ' baris)');

        return Excel::download(new TransactionsExport($transactions), 'laporan-keuangan-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('export', Transaction::class);
        $transactions = $this->buildQuery($request)->get();

        $summary = ['total_pengeluaran' => $transactions->sum('nominal')];

        ActivityLog::catat('exported', 'Transaction', null, 'Export laporan ke PDF (' . $transactions->count() . ' baris)');

        $pdf = Pdf::loadView('reports.pdf', compact('transactions', 'summary'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan-keuangan-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Laporan Resmi (gaya surat pemerintah) untuk satu Tahun Anggaran penuh,
     * lengkap dengan blok tanda tangan pejabat.
     */
    public function officialPdf(Request $request)
    {
        $this->authorize('export', Transaction::class);

        $tahunIds = $request->input('tahun_anggaran_id', []);
        if (empty($tahunIds)) {
            $active = AnggaranTahun::where('is_active', true)->first();
            $tahunIds = $active ? [$active->id] : [];
        }

        $tahunList = AnggaranTahun::whereIn('id', $tahunIds)->orderBy('tahun')->get();
        abort_if($tahunList->isEmpty(), 404, 'Tahun Anggaran tidak ditemukan.');

        $laporanPerTahun = $tahunList->map(function ($tahun) {
            $categories = BudgetCategory::with('masterKomponen')
                ->where('tahun_anggaran_id', $tahun->id)
                ->orderBy('master_komponen_id')
                ->get();

            $transactions = Transaction::with(['budgetCategory.masterKomponen', 'vendor', 'creator'])
                ->whereHas('budgetCategory', fn ($q) => $q->where('tahun_anggaran_id', $tahun->id))
                ->orderBy('tanggal')
                ->get();

            $totalPagu = $categories->sum('pagu_anggaran');
            $totalRealisasi = $categories->sum('total_terpakai');
            $sisa = $totalPagu - $totalRealisasi;
            $persenSerapan = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 1) : 0;

            return [
                'tahun' => $tahun,
                'categories' => $categories,
                'transactions' => $transactions,
                'totalPagu' => $totalPagu,
                'totalRealisasi' => $totalRealisasi,
                'sisa' => $sisa,
                'persenSerapan' => $persenSerapan,
            ];
        });

        $pejabat = PejabatPenandatangan::where('is_active', true)->first();

        ActivityLog::catat('exported', 'BudgetCategory', null,
            'Export Laporan Resmi PDF Tahun Anggaran: ' . $tahunList->pluck('tahun')->implode(', '));

        $pdf = Pdf::loadView('reports.official-pdf', compact('laporanPerTahun', 'pejabat'))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-resmi-realisasi-anggaran-' . $tahunList->pluck('tahun')->implode('-') . '.pdf');
    }
}    