<?php

namespace App\Http\Controllers;

use App\Models\AnggaranTahun;
use App\Models\BudgetCategory;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Akun petugas (bukan admin) tidak punya akses ke dashboard Pagu Anggaran —
        // arahkan langsung ke dashboard KAK ICUT miliknya sendiri.
        if ($user->isPetugas() && !$user->isAdmin()) {
            return redirect()->route('consultations.dashboard');
        }

        $tahunAktif = AnggaranTahun::where('is_active', true)->first();

        $categories = $tahunAktif
            ? BudgetCategory::with('masterKomponen')->where('tahun_anggaran_id', $tahunAktif->id)->where('is_active', true)->get()
            : collect();

        $totalPagu = $categories->sum('pagu_anggaran');
        $totalTerpakai = $categories->sum('total_terpakai');
        $sisaPagu = $totalPagu - $totalTerpakai;

        $bulanIni = Carbon::now()->startOfMonth();
        $totalKeluarBulanIni = $tahunAktif
            ? Transaction::whereHas('budgetCategory', fn ($q) => $q->where('tahun_anggaran_id', $tahunAktif->id))
                ->where('tanggal', '>=', $bulanIni)
                ->sum('nominal')
            : 0;

        $pengeluaran30hari = Transaction::selectRaw('tanggal, SUM(nominal) as total')
            ->where('tanggal', '>=', Carbon::now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $transaksiTerbaru = Transaction::with(['budgetCategory.masterKomponen', 'vendor', 'creator'])
            ->orderByDesc('id')->limit(10)->get();

        return view('dashboard', compact(
            'tahunAktif', 'totalPagu', 'totalTerpakai', 'sisaPagu', 'totalKeluarBulanIni',
            'pengeluaran30hari', 'categories', 'transaksiTerbaru'
        ));
    }
}
