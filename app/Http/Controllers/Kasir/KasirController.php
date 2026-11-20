<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\MenuBarang;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function index(): View
    {
        $items = MenuBarang::query()
            ->where('is_active', true)
            ->where('is_dijual', true)
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        $categories = $items->pluck('kategori')->unique()->values();

        return view('kasir.index', compact('items', 'categories'));
    }
}
