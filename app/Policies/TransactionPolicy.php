<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'bendahara']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'bendahara']);
    }

    public function update(User $user, Transaction $transaction): bool
    {
        // admin bisa edit apa saja & kapan saja.
        // bendahara hanya boleh edit transaksi yang dia input sendiri, dan yang tanggal
        // transaksinya masih dalam 7 hari terakhir (mencegah rekayasa laporan lama).
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isBendahara()
            && $transaction->created_by === $user->id
            && $transaction->tanggal->diffInDays(now()) <= 7;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        // hanya admin yang boleh menghapus (soft delete) transaksi
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return in_array($user->role, ['admin', 'bendahara']);
    }
}
