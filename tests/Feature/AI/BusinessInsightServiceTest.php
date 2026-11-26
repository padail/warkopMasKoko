<?php

use App\Models\MenuBarang;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\AI\BusinessInsightService;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it builds sales and product insights from completed transactions only', function () {
    $this->travelTo('2026-11-26 12:00:00');

    $employee = User::factory()->create([
        'role' => User::ROLE_KARYAWAN,
    ]);

    $coffee = MenuBarang::create([
        'nama' => 'Kopi Hitam',
        'kategori' => 'Minuman',
        'harga_jual' => 20000,
        'stok' => 10,
        'satuan' => 'gelas',
        'stok_minimum' => 2,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $tea = MenuBarang::create([
        'nama' => 'Es Teh',
        'kategori' => 'Minuman',
        'harga_jual' => 5000,
        'stok' => 10,
        'satuan' => 'gelas',
        'stok_minimum' => 2,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $firstSale = Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-20261126-001',
        'tanggal_transaksi' => '2026-11-26 09:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 45000,
        'status' => 'selesai',
    ]);

    $firstSale->detailTransaksi()->createMany([
        [
            'menu_barang_id' => $coffee->id,
            'nama_item_snapshot' => 'Kopi Hitam',
            'harga_satuan' => 20000,
            'jumlah' => 2,
            'subtotal' => 40000,
        ],
        [
            'menu_barang_id' => $tea->id,
            'nama_item_snapshot' => 'Es Teh',
            'harga_satuan' => 5000,
            'jumlah' => 1,
            'subtotal' => 5000,
        ],
    ]);

    $secondSale = Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-20261126-002',
        'tanggal_transaksi' => '2026-11-26 10:00:00',
        'metode_pembayaran' => 'qris',
        'total' => 20000,
        'status' => 'selesai',
    ]);

    $secondSale->detailTransaksi()->create([
        'menu_barang_id' => $coffee->id,
        'nama_item_snapshot' => 'Kopi Hitam',
        'harga_satuan' => 20000,
        'jumlah' => 1,
        'subtotal' => 20000,
    ]);

    $cancelledSale = Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-20261126-003',
        'tanggal_transaksi' => '2026-11-26 11:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 999999,
        'status' => 'batal',
    ]);

    $cancelledSale->detailTransaksi()->create([
        'menu_barang_id' => $tea->id,
        'nama_item_snapshot' => 'Es Teh',
        'harga_satuan' => 5000,
        'jumlah' => 99,
        'subtotal' => 999999,
    ]);

    $previousSale = Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-20261125-001',
        'tanggal_transaksi' => '2026-11-25 10:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 10000,
        'status' => 'selesai',
    ]);

    $previousSale->detailTransaksi()->create([
        'menu_barang_id' => $tea->id,
        'nama_item_snapshot' => 'Es Teh',
        'harga_satuan' => 5000,
        'jumlah' => 2,
        'subtotal' => 10000,
    ]);

    $context = app(BusinessInsightService::class)->buildContext([
        'periode' => OwnerDashboardService::PRESET_HARI_INI,
    ]);

    expect($context['sales']['transactions_count'])->toBe(2)
        ->and($context['sales']['revenue_total'])->toBe(65000)
        ->and($context['sales']['payment_methods']['tunai']['revenue_total'])->toBe(45000)
        ->and($context['sales']['payment_methods']['qris']['revenue_total'])->toBe(20000)
        ->and($context['sales_comparison']['previous_revenue_total'])->toBe(10000)
        ->and($context['sales_comparison']['revenue_change_percent'])->toBe(550.0)
        ->and($context['sales_comparison']['revenue_direction'])->toBe('naik')
        ->and($context['top_products'])->toHaveCount(2)
        ->and($context['top_products'][0]['product_name'])->toBe('Kopi Hitam')
        ->and($context['top_products'][0]['quantity_sold'])->toBe(3.0)
        ->and($context['top_products'][0]['revenue_total'])->toBe(60000)
        ->and($context['top_products'][0]['transactions_count'])->toBe(2);
});

test('it summarizes current stock alerts and expenses by category', function () {
    $this->travelTo('2026-11-26 12:00:00');

    $employee = User::factory()->create([
        'role' => User::ROLE_KARYAWAN,
    ]);

    MenuBarang::create([
        'nama' => 'Gula',
        'kategori' => 'Bahan Baku',
        'harga_jual' => 0,
        'stok' => 2,
        'satuan' => 'kg',
        'stok_minimum' => 5,
        'is_dijual' => false,
        'is_active' => true,
    ]);

    MenuBarang::create([
        'nama' => 'Kopi',
        'kategori' => 'Bahan Baku',
        'harga_jual' => 0,
        'stok' => 10,
        'satuan' => 'kg',
        'stok_minimum' => 5,
        'is_dijual' => false,
        'is_active' => true,
    ]);

    MenuBarang::create([
        'nama' => 'Susu Nonaktif',
        'kategori' => 'Bahan Baku',
        'harga_jual' => 0,
        'stok' => 0,
        'satuan' => 'liter',
        'stok_minimum' => 5,
        'is_dijual' => false,
        'is_active' => false,
    ]);

    Pengeluaran::factory()->create([
        'user_id' => $employee->id,
        'tanggal_pengeluaran' => '2026-11-26',
        'kategori' => 'Bahan baku',
        'jumlah' => 15000,
    ]);

    Pengeluaran::factory()->create([
        'user_id' => $employee->id,
        'tanggal_pengeluaran' => '2026-11-26',
        'kategori' => 'Operasional',
        'jumlah' => 5000,
    ]);

    Pengeluaran::factory()->create([
        'user_id' => $employee->id,
        'tanggal_pengeluaran' => '2026-11-25',
        'kategori' => 'Bahan baku',
        'jumlah' => 100000,
    ]);

    $context = app(BusinessInsightService::class)->buildContext([
        'periode' => OwnerDashboardService::PRESET_HARI_INI,
    ]);

    expect($context['stock']['summary']['active_count'])->toBe(2)
        ->and($context['stock']['summary']['low_count'])->toBe(1)
        ->and($context['stock']['alerts'])->toHaveCount(1)
        ->and($context['stock']['alerts'][0]['name'])->toBe('Gula')
        ->and($context['stock']['alerts'][0]['status'])->toBe('menipis')
        ->and($context['expenses']['expenses_count'])->toBe(2)
        ->and($context['expenses']['expenses_total'])->toBe(20000)
        ->and($context['expenses']['by_category'][0]['category'])->toBe('Bahan baku')
        ->and($context['expenses']['by_category'][0]['expenses_total'])->toBe(15000);
});

test('it reports an unavailable percentage when the previous period has no revenue', function () {
    $this->travelTo('2026-11-26 12:00:00');

    $context = app(BusinessInsightService::class)->buildContext([
        'periode' => OwnerDashboardService::PRESET_HARI_INI,
    ]);

    expect($context['sales']['revenue_total'])->toBe(0)
        ->and($context['sales_comparison']['previous_revenue_total'])->toBe(0)
        ->and($context['sales_comparison']['revenue_change_percent'])->toBeNull()
        ->and($context['sales_comparison']['revenue_direction'])->toBe('belum_ada_baseline')
        ->and($context['top_products'])->toBeEmpty()
        ->and($context['expenses']['expenses_total'])->toBe(0);
});

test('it compares custom date ranges with the immediately preceding range of equal length', function () {
    $this->travelTo('2026-11-26 12:00:00');

    $employee = User::factory()->create([
        'role' => User::ROLE_KARYAWAN,
    ]);

    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-RANGE-PREV',
        'tanggal_transaksi' => '2026-11-23 10:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 10000,
        'status' => 'selesai',
    ]);

    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-RANGE-CURRENT-1',
        'tanggal_transaksi' => '2026-11-25 10:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 20000,
        'status' => 'selesai',
    ]);

    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'AI-RANGE-CURRENT-2',
        'tanggal_transaksi' => '2026-11-26 10:00:00',
        'metode_pembayaran' => 'qris',
        'total' => 10000,
        'status' => 'selesai',
    ]);

    $context = app(BusinessInsightService::class)->buildContext([
        'periode' => OwnerDashboardService::PRESET_RENTANG,
        'start' => '2026-11-25',
        'end' => '2026-11-26',
    ]);

    expect($context['period']['days'])->toBe(2)
        ->and($context['sales']['revenue_total'])->toBe(30000)
        ->and($context['sales_comparison']['previous_period']['start'])->toBe('2026-11-23')
        ->and($context['sales_comparison']['previous_period']['end'])->toBe('2026-11-24')
        ->and($context['sales_comparison']['previous_revenue_total'])->toBe(10000)
        ->and($context['sales_comparison']['revenue_change_percent'])->toBe(200.0);
});
