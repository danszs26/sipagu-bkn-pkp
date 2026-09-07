<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    /**
     * Snapshot data SEBELUM update disimpan di sini (array statis di luar model),
     * BUKAN sebagai properti dinamis di model — supaya tidak ikut ke-anggap
     * kolom database saat proses simpan (itu penyebab error "_oldSnapshot" kemarin).
     */
    private static array $snapshots = [];

    public function created(Transaction $transaction): void
    {
        $transaction->budgetCategory()->increment('total_terpakai', (float) $transaction->nominal);

        ActivityLog::catat(
            action: 'created',
            modelType: 'Transaction',
            modelId: $transaction->id,
            description: "Membuat transaksi {$transaction->no_referensi} (Rp " . number_format((float) $transaction->nominal, 0, ',', '.') . ")",
            oldData: null,
            newData: $transaction->only(['tanggal', 'nominal', 'budget_category_id', 'vendor_id', 'uraian']),
        );
    }

    public function updating(Transaction $transaction): void
    {
        self::$snapshots[$transaction->id] = $transaction->getOriginal();
    }

    public function updated(Transaction $transaction): void
    {
        $old = self::$snapshots[$transaction->id] ?? $transaction->getOriginal();
        unset(self::$snapshots[$transaction->id]);

        $fieldsPenting = ['tanggal', 'nominal', 'budget_category_id', 'vendor_id', 'uraian', 'bukti_file_path'];
        $oldRelevant = collect($old)->only($fieldsPenting)->toArray();
        $newRelevant = $transaction->only($fieldsPenting);

        if ($oldRelevant != $newRelevant) {
            DB::table('budget_categories')
                ->where('id', $old['budget_category_id'])
                ->decrement('total_terpakai', (float) $old['nominal']);

            $transaction->budgetCategory()->increment('total_terpakai', (float) $transaction->nominal);

            ActivityLog::catat(
                action: 'updated',
                modelType: 'Transaction',
                modelId: $transaction->id,
                description: "Mengubah transaksi {$transaction->no_referensi}",
                oldData: $oldRelevant,
                newData: $newRelevant,
            );
        }
    }

    public function deleted(Transaction $transaction): void
    {
        $transaction->budgetCategory()->decrement('total_terpakai', (float) $transaction->nominal);

        ActivityLog::catat(
            action: 'deleted',
            modelType: 'Transaction',
            modelId: $transaction->id,
            description: "Menghapus (soft-delete) transaksi {$transaction->no_referensi}",
            oldData: $transaction->only(['tanggal', 'nominal', 'budget_category_id', 'uraian']),
            newData: null,
        );
    }
}