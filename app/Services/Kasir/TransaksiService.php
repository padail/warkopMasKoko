<?php

namespace App\Services\Kasir;

use App\Models\MenuBarang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransaksiService
{
    public function process(int $userId, string $paymentMethod, ?int $amountReceived, array $items): Transaksi
    {
        return DB::transaction(function () use ($userId, $paymentMethod, $amountReceived, $items) {
            $total = 0;
            $details = [];

            foreach ($items as $item) {
                $menu = MenuBarang::query()
                    ->whereKey((int) $item['menu_barang_id'])
                    ->where('is_active', true)
                    ->where('is_dijual', true)
                    ->lockForUpdate()
                    ->first();

                if (! $menu) {
                    throw new RuntimeException('Barang tidak tersedia.');
                }

                $jumlah = (float) $item['jumlah'];
                if ($jumlah <= 0 || (float) $menu->stok < $jumlah) {
                    throw new RuntimeException("Stok {$menu->nama} tidak mencukupi.");
                }

                $subtotal = (int) round($menu->harga_jual * $jumlah);
                $total += $subtotal;
                $details[] = [
                    'menu' => $menu,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }

            $change = 0;
            if ($paymentMethod === 'tunai') {
                if ($amountReceived === null || $amountReceived < $total) {
                    throw new RuntimeException('Nominal tunai kurang dari total pembayaran.');
                }
                $change = $amountReceived - $total;
            }

            $transaction = Transaksi::create([
                'user_id' => $userId,
                'kode_transaksi' => 'TRX-'.now()->format('YmdHis').'-'.strtoupper(substr(uniqid(), -6)),
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => $paymentMethod,
                'total' => $total,
                'nominal_diterima' => $paymentMethod === 'tunai' ? $amountReceived : null,
                'kembalian' => $change,
                'status' => 'selesai',
            ]);

            foreach ($details as $detail) {
                $menu = $detail['menu'];
                $transaction->detailTransaksi()->create([
                    'menu_barang_id' => $menu->id,
                    'nama_item_snapshot' => $menu->nama,
                    'harga_satuan' => $menu->harga_jual,
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal'],
                ]);
                $menu->decrement('stok', $detail['jumlah']);
            }

            return $transaction->load('detailTransaksi');
        });
    }
}
