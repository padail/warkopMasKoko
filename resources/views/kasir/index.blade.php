@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 md:px-6">
    <h1 class="text-2xl font-bold">Kasir</h1>
    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <section class="rounded-xl border border-[#E4DED6] bg-white p-5">
            <div class="grid gap-3 sm:grid-cols-2">
                <input id="search" type="search" placeholder="Cari barang"
                       class="h-11 rounded-lg border border-[#E4DED6] px-3">
                <select id="category" class="h-11 rounded-lg border border-[#E4DED6] px-3">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div id="product-list" class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach($items as $item)
                    <article class="product rounded-lg border border-[#E4DED6] p-4"
                             data-name="{{ strtolower($item->nama) }}"
                             data-category="{{ $item->kategori }}">
                        <p class="font-semibold">{{ $item->nama }}</p>
                        <p class="stock-label mt-1 text-sm text-[#70685F]">{{ $item->kategori }} · Stok {{ rtrim(rtrim(number_format((float) $item->stok, 3, '.', ''), '0'), '.') }} {{ $item->satuan }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <strong>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</strong>
                            <button type="button"
                                    class="add-item rounded-lg bg-[#6F4E37] px-3 py-2 text-sm font-semibold text-white"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->nama }}"
                                    data-price="{{ $item->harga_jual }}"
                                    data-stock="{{ $item->stok }}"
                                    data-unit="{{ $item->satuan }}">
                                Tambah
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="h-fit rounded-xl border border-[#E4DED6] bg-white p-5 lg:sticky lg:top-6">
            <h2 class="text-lg font-semibold">Keranjang</h2>
            <div id="cart" class="mt-4 space-y-3"></div>
            <div class="mt-5 border-t border-[#E4DED6] pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span id="total">Rp 0</span>
                </div>
                <label class="mt-4 block text-sm font-medium" for="payment-method">Metode pembayaran</label>
                <select id="payment-method" class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="gopay">GoPay</option>
                </select>
                <div id="cash-input" class="mt-4">
                    <label class="block text-sm font-medium" for="amount-received">Nominal diterima</label>
                    <input id="amount-received" type="number" min="0" class="mt-2 h-11 w-full rounded-lg border border-[#E4DED6] px-3">
                    <p class="mt-2 text-sm text-[#70685F]">Kembalian: <strong id="change">Rp 0</strong></p>
                </div>
                <button id="checkout" type="button"
                        class="mt-5 h-11 w-full rounded-lg bg-[#6F4E37] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50"
                        disabled>
                    Proses pembayaran
                </button>
                <p id="checkout-error" class="mt-3 text-sm text-[#B42318]" role="alert"></p>
            </div>
        </aside>
    </div>
</div>

<dialog id="receipt" class="w-[calc(100%-2rem)] max-w-md rounded-xl border border-[#E4DED6] p-6">
    <h2 class="text-xl font-bold">Transaksi berhasil</h2>
    <div id="receipt-content" class="mt-4 text-sm"></div>
    <button id="close-receipt" type="button" class="mt-5 h-11 w-full rounded-lg bg-[#6F4E37] font-semibold text-white">Selesai</button>
</dialog>

<dialog id="checkout-confirmation" class="w-[calc(100%-2rem)] max-w-md rounded-xl border border-[#E4DED6] p-6 shadow-xl">
    <h2 class="text-xl font-bold">Konfirmasi pembayaran</h2>
    <p id="confirmation-instruction" class="mt-2 text-sm text-[#70685F]"></p>
    <div id="confirmation-items" class="mt-4 max-h-48 space-y-2 overflow-y-auto border-y border-[#E4DED6] py-3"></div>
    <dl class="mt-4 space-y-2 text-sm">
        <div class="flex justify-between"><dt>Total</dt><dd id="confirmation-total" class="font-semibold"></dd></div>
        <div id="confirmation-cash-details" class="space-y-2">
            <div class="flex justify-between"><dt>Nominal diterima</dt><dd id="confirmation-received"></dd></div>
            <div class="flex justify-between"><dt>Kembalian</dt><dd id="confirmation-change"></dd></div>
        </div>
        <div class="flex justify-between"><dt>Metode</dt><dd id="confirmation-method" class="font-semibold"></dd></div>
    </dl>
    <p id="confirmation-error" class="mt-3 text-sm text-[#B42318]" role="alert"></p>
    <div class="mt-5 flex gap-3">
        <button id="cancel-confirmation" type="button" class="h-11 flex-1 rounded-lg border border-[#E4DED6] font-semibold">
            Kembali
        </button>
        <button id="confirm-checkout" type="button" class="h-11 flex-1 rounded-lg bg-[#6F4E37] font-semibold text-white">
            Konfirmasi pembayaran
        </button>
    </div>
</dialog>

<script>
const cart = new Map();
const cartElement = document.querySelector('#cart');
const totalElement = document.querySelector('#total');
const checkoutButton = document.querySelector('#checkout');
const checkoutConfirmation = document.querySelector('#checkout-confirmation');
const confirmCheckoutButton = document.querySelector('#confirm-checkout');
const paymentMethod = document.querySelector('#payment-method');
const amountReceived = document.querySelector('#amount-received');
const changeElement = document.querySelector('#change');
const errorElement = document.querySelector('#checkout-error');
const confirmationErrorElement = document.querySelector('#confirmation-error');

const formatRupiah = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
const formatStock = value => String(Number(value).toFixed(3)).replace(/\.?0+$/, '');

function updateProductStock(items) {
    items.forEach(purchasedItem => {
        const button = document.querySelector(`.add-item[data-id="${purchasedItem.menu_barang_id}"]`);
        if (!button) return;

        const product = button.closest('.product');
        const remainingStock = Math.max(
            0,
            Number(button.dataset.stock) - Number(purchasedItem.jumlah),
        );

        button.dataset.stock = remainingStock;
        button.disabled = remainingStock <= 0;
        button.classList.toggle('opacity-50', remainingStock <= 0);
        button.classList.toggle('cursor-not-allowed', remainingStock <= 0);

        const stockLabel = product.querySelector('.stock-label');
        stockLabel.textContent =
            `${button.closest('.product').dataset.category} · Stok ${formatStock(remainingStock)} ${button.dataset.unit}`;
    });
}

function calculateTotal() {
    return [...cart.values()].reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function renderCart() {
    cartElement.innerHTML = '';
    [...cart.values()].forEach(item => {
        const row = document.createElement('div');
        row.className = 'rounded-lg border border-[#E4DED6] p-3';
        row.innerHTML = `
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-semibold">${item.name}</p><p class="text-xs text-[#70685F]">${formatRupiah(item.price)}</p></div>
                <button type="button" class="remove text-xs text-[#B42318]" data-id="${item.id}">Hapus</button>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" class="decrease h-8 w-8 rounded border border-[#E4DED6]" data-id="${item.id}">-</button>
                    <span class="w-10 text-center text-sm">${item.quantity}</span>
                    <button type="button" class="increase h-8 w-8 rounded border border-[#E4DED6]" data-id="${item.id}">+</button>
                </div>
                <strong class="text-sm">${formatRupiah(item.price * item.quantity)}</strong>
            </div>`;
        cartElement.appendChild(row);
    });

    const total = calculateTotal();
    totalElement.textContent = formatRupiah(total);
    checkoutButton.disabled = cart.size === 0;
    updateChange();
}

function updateChange() {
    const total = calculateTotal();
    const received = Number(amountReceived.value || 0);
    changeElement.textContent = formatRupiah(paymentMethod.value === 'tunai' && received >= total ? received - total : 0);
}

document.querySelectorAll('.add-item').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;
        const existing = cart.get(id);
        const quantity = existing ? existing.quantity + 1 : 1;
        if (quantity > Number(button.dataset.stock)) {
            errorElement.textContent = 'Jumlah melebihi stok tersedia.';
            return;
        }
        cart.set(id, {
            id, name: button.dataset.name, price: Number(button.dataset.price),
            quantity, stock: Number(button.dataset.stock),
        });
        errorElement.textContent = '';
        renderCart();
    });
});

cartElement.addEventListener('click', event => {
    const button = event.target.closest('button');
    if (!button) return;
    const id = button.dataset.id;
    const item = cart.get(id);
    if (!item) return;
    if (button.classList.contains('remove')) cart.delete(id);
    if (button.classList.contains('decrease')) {
        item.quantity -= 1;
        if (item.quantity <= 0) cart.delete(id);
    }
    if (button.classList.contains('increase') && item.quantity < item.stock) item.quantity += 1;
    renderCart();
});

paymentMethod.addEventListener('change', () => {
    document.querySelector('#cash-input').classList.toggle('hidden', paymentMethod.value !== 'tunai');
    updateChange();
});
amountReceived.addEventListener('input', updateChange);

document.querySelector('#search').addEventListener('input', filterProducts);
document.querySelector('#category').addEventListener('change', filterProducts);
function filterProducts() {
    const search = document.querySelector('#search').value.toLowerCase().trim();
    const category = document.querySelector('#category').value;
    document.querySelectorAll('.product').forEach(product => {
        const matchesSearch = product.dataset.name.includes(search);
        const matchesCategory = !category || product.dataset.category === category;
        product.classList.toggle('hidden', !(matchesSearch && matchesCategory));
    });
}

function showCheckoutConfirmation() {
    if (cart.size === 0) return;

    const method = paymentMethod.value;
    const received = Number(amountReceived.value || 0);
    const total = calculateTotal();

    if (method === 'tunai' && received < total) {
        errorElement.textContent = 'Nominal diterima harus sama dengan atau lebih besar dari total.';
        return;
    }

    errorElement.textContent = '';
    confirmationErrorElement.textContent = '';
    document.querySelector('#confirmation-items').replaceChildren();

    cart.forEach(item => {
        const row = document.createElement('div');
        row.className = 'flex justify-between gap-3 text-sm';

        const itemLabel = document.createElement('span');
        itemLabel.textContent = `${item.name} × ${item.quantity}`;

        const itemTotal = document.createElement('strong');
        itemTotal.textContent = formatRupiah(item.price * item.quantity);

        row.append(itemLabel, itemTotal);
        document.querySelector('#confirmation-items').appendChild(row);
    });

    document.querySelector('#confirmation-total').textContent = formatRupiah(total);
    document.querySelector('#confirmation-method').textContent = method.toUpperCase();
    document.querySelector('#confirmation-cash-details').classList.toggle('hidden', method !== 'tunai');

    if (method === 'tunai') {
        document.querySelector('#confirmation-received').textContent = formatRupiah(received);
        document.querySelector('#confirmation-change').textContent = formatRupiah(received - total);
        document.querySelector('#confirmation-instruction').textContent =
            'Pastikan nominal pembayaran dan kembalian sudah sesuai sebelum mengonfirmasi.';
        confirmCheckoutButton.textContent = 'Konfirmasi pembayaran';
    } else {
        document.querySelector('#confirmation-instruction').textContent =
            `Pastikan pembayaran ${method.toUpperCase()} sudah berhasil diverifikasi melalui perangkat pembayaran sebelum mengonfirmasi.`;
        confirmCheckoutButton.textContent = 'Pembayaran terverifikasi';
    }

    checkoutConfirmation.showModal();
}

checkoutButton.addEventListener('click', showCheckoutConfirmation);
document.querySelector('#cancel-confirmation').addEventListener('click', () => checkoutConfirmation.close());

confirmCheckoutButton.addEventListener('click', async () => {
    confirmCheckoutButton.disabled = true;
    confirmationErrorElement.textContent = '';
    const payload = {
        metode_pembayaran: paymentMethod.value,
        nominal_diterima: paymentMethod.value === 'tunai' ? Number(amountReceived.value) : null,
        items: [...cart.values()].map(item => ({ menu_barang_id: Number(item.id), jumlah: item.quantity })),
    };
    try {
        const response = await fetch('{{ route('kasir.transaksi.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Transaksi gagal.');
        updateProductStock(data.transaction.detail_transaksi);
        cart.clear();
        renderCart();
        amountReceived.value = '';
        checkoutConfirmation.close();
        document.querySelector('#receipt-content').innerHTML =
            `<p>Kode: <strong>${data.transaction.kode_transaksi}</strong></p>
             <p class="mt-2">Total: <strong>${formatRupiah(data.transaction.total)}</strong></p>
             <p>Kembalian: <strong>${formatRupiah(data.transaction.kembalian)}</strong></p>`;
        document.querySelector('#receipt').showModal();
    } catch (error) {
        confirmationErrorElement.textContent = error.message;
    } finally {
        confirmCheckoutButton.disabled = false;
    }
});

document.querySelector('#close-receipt').addEventListener('click', () => document.querySelector('#receipt').close());
</script>
@endsection
