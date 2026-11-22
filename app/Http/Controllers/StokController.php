<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stok\StoreMenuBarangRequest;
use App\Http\Requests\Stok\UpdateMenuBarangRequest;
use App\Http\Requests\Stok\UpdateStokRequest;
use App\Models\MenuBarang;
use App\Services\StokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StokController extends Controller
{
    public function __construct(
        private readonly StokService $stokService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'status_stok' => ['nullable', 'in:semua,normal,menipis,habis'],
        ]);

        $items = $this->stokService->paginateItems($filters, 10);
        $summary = $this->stokService->summaryCounts();
        $categories = $this->stokService->categories();
        $alasanOptions = StokService::alasanOptions();

        return view('stok.index', [
            'items' => $items,
            'summary' => $summary,
            'categories' => $categories,
            'alasanOptions' => $alasanOptions,
            'filters' => $filters,
        ]);
    }

    public function edit(MenuBarang $menuBarang): View
    {
        $menuBarang->load('stokDiperbaruiOleh');

        return view('stok.edit', [
            'item' => $menuBarang,
            'categories' => $this->stokService->categories(),
            'alasanOptions' => StokService::alasanOptions(),
        ]);
    }

    public function updateStok(UpdateStokRequest $request, MenuBarang $menuBarang): RedirectResponse
    {
        try {
            $this->stokService->updateStock(
                menuBarang: $menuBarang,
                userId: $request->user()->id,
                mode: $request->string('mode')->toString(),
                nilai: (float) $request->input('nilai'),
                alasan: $request->string('alasan')->toString(),
            );
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['nilai' => $exception->getMessage()]);
        }

        return redirect()
            ->route('stok.index')
            ->with('success', "Stok {$menuBarang->nama} berhasil diperbarui.");
    }

    public function store(StoreMenuBarangRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_dijual'] = $request->boolean('is_dijual');
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $item = $this->stokService->createItem($data, $request->user()->id);

        return redirect()
            ->route('stok.index')
            ->with('success', "Barang {$item->nama} berhasil ditambahkan.");
    }

    public function update(UpdateMenuBarangRequest $request, MenuBarang $menuBarang): RedirectResponse
    {
        $data = $request->validated();
        $data['is_dijual'] = $request->boolean('is_dijual');
        $data['is_active'] = $request->boolean('is_active');

        $this->stokService->updateItemDetails($menuBarang, $data, $request->user()->id);

        return redirect()
            ->route('stok.index')
            ->with('success', "Data barang {$menuBarang->nama} berhasil diperbarui.");
    }

    public function toggleActive(MenuBarang $menuBarang): RedirectResponse
    {
        $updated = $this->stokService->toggleActive($menuBarang);
        $label = $updated->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('stok.index')
            ->with('success', "Barang {$updated->nama} berhasil {$label}.");
    }
}
