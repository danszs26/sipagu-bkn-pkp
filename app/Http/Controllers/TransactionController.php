<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\BudgetCategory;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['budgetCategory.masterKomponen', 'budgetCategory.anggaranTahun', 'vendor', 'creator'])
            ->whereHas('budgetCategory.anggaranTahun', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function bukti(Transaction $transaction)
    {
        $this->authorize('viewAny', Transaction::class);

        if (!Storage::disk('private')->exists($transaction->bukti_file_path)) {
            abort(404, 'File bukti tidak ditemukan.');
        }

        return Storage::disk('private')->response(
            $transaction->bukti_file_path,
            $transaction->bukti_file_original_name
        );
    }

    public function create()
    {
        $this->authorize('create', Transaction::class);

        // Hanya tampilkan pagu anggaran dari tahun anggaran yang sedang aktif
        $categories = BudgetCategory::with(['masterKomponen', 'anggaranTahun'])
            ->where('is_active', true)
            ->whereHas('anggaranTahun', fn ($q) => $q->where('is_active', true))
            ->get();

        $vendors = Vendor::where('is_active', true)->orderBy('nama_vendor')->get();

        return view('transactions.create', compact('categories', 'vendors'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated, $request) {
            $category = BudgetCategory::where('id', $validated['budget_category_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $nominal = (float) $validated['nominal'];

            $vendorId = $validated['vendor_id'] ?? null;
            if (!empty($validated['vendor_baru'])) {
                $vendor = \App\Models\Vendor::firstOrCreate(
                    ['nama_vendor' => trim($validated['vendor_baru'])],
                    ['is_active' => true, 'created_by' => $request->user()->id]
                );
                $vendorId = $vendor->id;
            }

            if (!$category->bisaMenampung($nominal)) {
                throw ValidationException::withMessages([
                    'nominal' => "Nominal melebihi sisa pagu anggaran \"{$category->uraian}\". Sisa pagu: Rp " . number_format($category->sisa_anggaran, 0, ',', '.'),
                ]);
            }

            $path = $request->file('bukti_file')->store('bukti-transaksi', 'private');

            return Transaction::create([
                'no_referensi' => Transaction::generateNoReferensi(now()),
                'tanggal' => $validated['tanggal'],
                'budget_category_id' => $category->id,
                'vendor_id' => $vendorId,
                'nominal' => $nominal,
                'uraian' => $validated['uraian'],
                'bukti_file_path' => $path,
                'bukti_file_original_name' => $request->file('bukti_file')->getClientOriginalName(),
                'idempotency_token' => $validated['idempotency_token'],
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi {$result->no_referensi} berhasil disimpan.");
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $categories = BudgetCategory::with(['masterKomponen', 'anggaranTahun'])
            ->where('is_active', true)
            ->get();
        $vendors = Vendor::where('is_active', true)->orderBy('nama_vendor')->get();

        return view('transactions.edit', compact('transaction', 'categories', 'vendors'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $transaction) {
            $category = BudgetCategory::where('id', $validated['budget_category_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $nominalBaru = (float) $validated['nominal'];

            $vendorId = $validated['vendor_id'] ?? null;
            if (!empty($validated['vendor_baru'])) {
                $vendor = \App\Models\Vendor::firstOrCreate(
                    ['nama_vendor' => trim($validated['vendor_baru'])],
                    ['is_active' => true, 'created_by' => $request->user()->id]
                );
                $vendorId = $vendor->id;
            }

            $excludeAmount = ($transaction->budget_category_id == $category->id)
                ? (float) $transaction->nominal
                : 0;

            if (!$category->bisaMenampung($nominalBaru, $excludeAmount)) {
                throw ValidationException::withMessages([
                    'nominal' => "Nominal melebihi sisa pagu anggaran \"{$category->uraian}\".",
                ]);
            }

            $data = [
                'tanggal' => $validated['tanggal'],
                'budget_category_id' => $category->id,
                'vendor_id' => $vendorId,
                'nominal' => $nominalBaru,
                'uraian' => $validated['uraian'],
                'updated_by' => $request->user()->id,
            ];

            if ($request->hasFile('bukti_file')) {
                Storage::disk('private')->delete($transaction->bukti_file_path);
                $data['bukti_file_path'] = $request->file('bukti_file')->store('bukti-transaksi', 'private');
                $data['bukti_file_original_name'] = $request->file('bukti_file')->getClientOriginalName();
            }

            $transaction->update($data);
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
