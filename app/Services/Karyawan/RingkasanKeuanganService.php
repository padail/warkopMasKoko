<?php

namespace App\Services\Karyawan;

use App\Models\Pengeluaran;
use App\Models\Transaksi;

class RingkasanKeuanganService
{
    /**
     * @return array{
     *     transactions_count: int,
     *     expenses_count: int,
     *     income_cash: int,
     *     income_non_cash: int,
     *     expense_cash: int,
     *     expense_non_cash: int,
     *     income_total: int,
     *     expense_total: int,
     *     net_cash: int,
     *     net_non_cash: int,
     *     net_total: int,
     *     deposit_cash: int,
     *     deposit_non_cash: int
     * }
     */
    public function forEmployee(int $userId, ?string $date = null): array
    {
        $transactions = Transaksi::query()
            ->where('user_id', $userId)
            ->where('status', 'selesai')
            ->when($date, fn ($query) => $query->whereDate('tanggal_transaksi', $date));

        $expenses = Pengeluaran::query()
            ->where('user_id', $userId)
            ->when($date, fn ($query) => $query->whereDate('tanggal_pengeluaran', $date));

        $incomeCash = (int) (clone $transactions)
            ->where('metode_pembayaran', 'tunai')
            ->sum('total');
        $incomeNonCash = (int) (clone $transactions)
            ->whereIn('metode_pembayaran', ['qris', 'gopay'])
            ->sum('total');
        $expenseCash = (int) (clone $expenses)
            ->where('metode_pembayaran', Pengeluaran::METODE_TUNAI)
            ->sum('jumlah');
        $expenseNonCash = (int) (clone $expenses)
            ->where('metode_pembayaran', Pengeluaran::METODE_NON_TUNAI)
            ->sum('jumlah');
        $netCash = $incomeCash - $expenseCash;
        $netNonCash = $incomeNonCash - $expenseNonCash;

        return [
            'transactions_count' => (int) (clone $transactions)->count(),
            'expenses_count' => (int) (clone $expenses)->count(),
            'income_cash' => $incomeCash,
            'income_non_cash' => $incomeNonCash,
            'expense_cash' => $expenseCash,
            'expense_non_cash' => $expenseNonCash,
            'income_total' => $incomeCash + $incomeNonCash,
            'expense_total' => $expenseCash + $expenseNonCash,
            'net_cash' => $netCash,
            'net_non_cash' => $netNonCash,
            'net_total' => $netCash + $netNonCash,
            'deposit_cash' => max(0, $netCash),
            'deposit_non_cash' => max(0, $netNonCash),
        ];
    }
}
