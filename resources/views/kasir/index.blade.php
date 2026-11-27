@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-4 pb-8 md:px-6 md:py-6">
    <div class="mb-4">
        <h1 class="text-2xl font-bold">Kasir</h1>
        <p class="mt-1 text-sm text-[#70685F]">Pilih barang, atur jumlah, lalu selesaikan pembayaran.</p>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-6">
        <section class="min-w-0 rounded-xl border border-[#E4DED6] bg-white p-4 sm:p-5">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="search" class="sr-only">Cari barang</label>
                    <input id="search" type="search" placeholder="Cari nama barang..."
                           class="min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                </div>
                <div>
                    <label for="category" class="sr-only">Filter kategori</label>
                    <select id="category" class="min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div id="product-list" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($items as $item)
                    <article class="product min-w-0 rounded-xl border border-[#E4DED6] p-3 sm:p-4"
                             data-name="{{ strtolower($item->nama) }}"
                             data-category="{{ $item->kategori }}">
                        <p class="font-semibold">{{ $item->nama }}</p>
                        <p class="stock-label mt-1 text-sm text-[#70685F]">{{ $item->kategori }} · Stok {{ rtrim(rtrim(number_format((float) $item->stok, 3, '.', ''), '0'), '.') }} {{ $item->satuan }}</p>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <strong>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</strong>
                            <button type="button"
                                    class="add-item inline-flex min-h-11 min-w-24 items-center justify-center rounded-lg bg-[#6F4E37] px-4 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->nama }}"
                                    data-price="{{ $item->harga_jual }}"
                                    data-stock="{{ $item->stok }}"
                                    data-unit="{{ $item->satuan }}"
                                    @disabled((float) $item->stok <= 0)>
                                {{ (float) $item->stok <= 0 ? 'Stok habis' : 'Tambah' }}
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
            <p id="product-empty" class="mt-5 hidden rounded-lg bg-[#F7F5F1] px-4 py-6 text-center text-sm text-[#70685F]">
                Barang tidak ditemukan. Coba kata kunci atau kategori lain.
            </p>
        </section>

        <aside id="cart-panel" tabindex="-1" class="h-fit scroll-mt-24 rounded-xl border border-[#E4DED6] bg-white p-4 sm:p-5 lg:sticky lg:top-24">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Keranjang</h2>
                <span id="cart-items-count" class="rounded-full bg-[#EFE7DF] px-3 py-1 text-xs font-semibold text-[#3B2A20]">0 barang</span>
            </div>
            <div id="cart" class="mt-4 space-y-3" aria-live="polite"></div>
            <div class="mt-5 border-t border-[#E4DED6] pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span id="total">Rp 0</span>
                </div>
                <label class="mt-4 block text-sm font-medium" for="payment-method">Metode pembayaran</label>
                <select id="payment-method" class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="gopay">GoPay</option>
                </select>
                <div id="cash-input" class="mt-4">
                    <label class="block text-sm font-medium" for="amount-received">Nominal diterima</label>
                    <input id="amount-received" type="number" min="0" class="mt-2 min-h-11 w-full rounded-lg border border-[#E4DED6] px-3 text-base sm:text-sm">
                    <p class="mt-2 text-sm text-[#70685F]">Kembalian: <strong id="change">Rp 0</strong></p>
                </div>
                <button id="checkout" type="button"
                        class="mt-5 min-h-12 w-full rounded-lg bg-[#6F4E37] px-4 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50"
                        disabled>
                    Proses pembayaran
                </button>
                <p id="checkout-error" class="mt-3 text-sm text-[#B42318]" role="alert"></p>
            </div>
        </aside>
    </div>
</div>

<div id="mobile-cart-bar" class="mobile-cart-bar fixed inset-x-3 z-30 hidden md:hidden">
    <button id="mobile-cart-button" type="button" class="flex min-h-14 w-full items-center justify-between gap-3 rounded-xl bg-[#3B2A20] px-4 text-left text-white shadow-xl">
        <span class="min-w-0">
            <span class="block text-xs text-white/75"><span id="mobile-cart-count">0</span> barang di keranjang</span>
            <strong id="mobile-cart-total" class="block truncate text-base">Rp 0</strong>
        </span>
        <span class="shrink-0 text-sm font-semibold">Lihat keranjang ↓</span>
    </button>
</div>

<dialog id="receipt" class="max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-md overflow-y-auto rounded-xl border border-[#E4DED6] p-5 sm:p-6">
    <h2 class="text-xl font-bold">Transaksi berhasil</h2>
    <div id="receipt-content" class="mt-4 text-sm"></div>
    <button id="close-receipt" type="button" class="mt-5 h-11 w-full rounded-lg bg-[#6F4E37] font-semibold text-white">Selesai</button>
</dialog>

<dialog id="checkout-confirmation" class="max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-md overflow-y-auto rounded-xl border border-[#E4DED6] p-5 shadow-xl sm:p-6">
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
const cartPanel = document.querySelector('#cart-panel');
const totalElement = document.querySelector('#total');
const checkoutButton = document.querySelector('#checkout');
const checkoutConfirmation = document.querySelector('#checkout-confirmation');
const confirmCheckoutButton = document.querySelector('#confirm-checkout');
const paymentMethod = document.querySelector('#payment-method');
const amountReceived = document.querySelector('#amount-received');
const changeElement = document.querySelector('#change');
const errorElement = document.querySelector('#checkout-error');
const confirmationErrorElement = document.querySelector('#confirmation-error');
const mobileCartBar = document.querySelector('#mobile-cart-bar');
const mobileCartCount = document.querySelector('#mobile-cart-count');
const mobileCartTotal = document.querySelector('#mobile-cart-total');
let cartPanelVisible = false;

const formatRupiah = value => new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
}).format(value);

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
        button.textContent = remainingStock <= 0 ? 'Stok habis' : 'Tambah';

        const stockLabel = product.querySelector('.stock-label');
        stockLabel.textContent =
            `${product.dataset.category} · Stok ${formatStock(remainingStock)} ${button.dataset.unit}`;
    });
}

function calculateTotal() {
    return [...cart.values()].reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function makeCartButton(label, className, id, action) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = className;
    button.dataset.id = id;
    button.textContent = label;
    button.setAttribute('aria-label', action);
    return button;
}

function renderCart() {
    cartElement.replaceChildren();

    if (cart.size === 0) {
        const empty = document.createElement('p');
        empty.className = 'rounded-lg bg-[#F7F5F1] px-4 py-6 text-center text-sm text-[#70685F]';
        empty.textContent = 'Keranjang masih kosong. Tambahkan barang dari daftar produk.';
        cartElement.appendChild(empty);
    }

    [...cart.values()].forEach(item => {
        const row = document.createElement('div');
        row.className = 'rounded-xl border border-[#E4DED6] p-3';

        const top = document.createElement('div');
        top.className = 'flex items-start justify-between gap-3';

        const details = document.createElement('div');
        details.className = 'min-w-0';
        const name = document.createElement('p');
        name.className = 'break-words text-sm font-semibold';
        name.textContent = item.name;
        const price = document.createElement('p');
        price.className = 'mt-1 text-xs text-[#70685F]';
        price.textContent = formatRupiah(item.price);
        details.append(name, price);

        const remove = makeCartButton(
            'Hapus',
            'inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 text-xs font-semibold text-[#B42318]',
            item.id,
            `Hapus ${item.name} dari keranjang`,
        );
        remove.classList.add('remove');
        top.append(details, remove);

        const bottom = document.createElement('div');
        bottom.className = 'mt-3 flex items-center justify-between gap-3';

        const controls = document.createElement('div');
        controls.className = 'flex items-center gap-2';
        const decrease = makeCartButton(
            '−',
            'decrease inline-flex h-11 w-11 items-center justify-center rounded-lg border border-[#E4DED6] text-lg font-semibold',
            item.id,
            `Kurangi jumlah ${item.name}`,
        );
        const quantity = document.createElement('span');
        quantity.className = 'w-8 text-center text-sm font-semibold';
        quantity.textContent = String(item.quantity);
        const increase = makeCartButton(
            '+',
            'increase inline-flex h-11 w-11 items-center justify-center rounded-lg border border-[#E4DED6] text-lg font-semibold',
            item.id,
            `Tambah jumlah ${item.name}`,
        );
        controls.append(decrease, quantity, increase);

        const lineTotal = document.createElement('strong');
        lineTotal.className = 'shrink-0 text-sm';
        lineTotal.textContent = formatRupiah(item.price * item.quantity);
        bottom.append(controls, lineTotal);

        row.append(top, bottom);
        cartElement.appendChild(row);
    });

    const total = calculateTotal();
    const count = [...cart.values()].reduce((sum, item) => sum + item.quantity, 0);

    totalElement.textContent = formatRupiah(total);
    checkoutButton.disabled = cart.size === 0;
    mobileCartCount.textContent = String(count);
    mobileCartTotal.textContent = formatRupiah(total);
    updateChange();
    updateMobileCartBar();
}

function updateMobileCartBar() {
    mobileCartBar.classList.toggle('hidden', cart.size === 0 || cartPanelVisible);
}

if ('IntersectionObserver' in window) {
    const cartObserver = new IntersectionObserver(entries => {
        cartPanelVisible = entries[0]?.isIntersecting ?? false;
        updateMobileCartBar();
    }, { threshold: 0.2, rootMargin: '0px 0px -88px 0px' });
    cartObserver.observe(cartPanel);
}

document.querySelector('#mobile-cart-button').addEventListener('click', () => {
    cartPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    cartPanel.focus({ preventScroll: true });
});

function updateChange() {
    const total = calculateTotal();
    const received = Number(amountReceived.value || 0);
    changeElement.textContent = formatRupiah(
        paymentMethod.value === 'tunai' && received >= total ? received - total : 0
    );
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
            id,
            name: button.dataset.name,
            price: Number(button.dataset.price),
            quantity,
            stock: Number(button.dataset.stock),
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

    if (button.classList.contains('increase') && item.quantity < item.stock) {
        item.quantity += 1;
    }

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
    let visibleCount = 0;

    document.querySelectorAll('.product').forEach(product => {
        const matchesSearch = product.dataset.name.includes(search);
        const matchesCategory = !category || product.dataset.category === category;
        const visible = matchesSearch && matchesCategory;

        product.classList.toggle('hidden', !visible);
        if (visible) visibleCount += 1;
    });

    document.querySelector('#product-empty').classList.toggle('hidden', visibleCount > 0);
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
        items: [...cart.values()].map(item => ({
            menu_barang_id: Number(item.id),
            jumlah: item.quantity,
        })),
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

        const receiptContent = document.querySelector('#receipt-content');
        receiptContent.replaceChildren();

        const codeLine = document.createElement('p');
        codeLine.textContent = 'Kode: ';
        const codeValue = document.createElement('strong');
        codeValue.textContent = data.transaction.kode_transaksi;
        codeLine.appendChild(codeValue);

        const totalLine = document.createElement('p');
        totalLine.className = 'mt-2';
        totalLine.textContent = 'Total: ';
        const totalValue = document.createElement('strong');
        totalValue.textContent = formatRupiah(data.transaction.total);
        totalLine.appendChild(totalValue);

        const changeLine = document.createElement('p');
        changeLine.textContent = 'Kembalian: ';
        const changeValue = document.createElement('strong');
        changeValue.textContent = formatRupiah(data.transaction.kembalian);
        changeLine.appendChild(changeValue);

        receiptContent.append(codeLine, totalLine, changeLine);
        document.querySelector('#receipt').showModal();
    } catch (error) {
        confirmationErrorElement.textContent = error.message;
    } finally {
        confirmCheckoutButton.disabled = false;
    }
});

document.querySelector('#close-receipt').addEventListener('click', () => {
    document.querySelector('#receipt').close();
});

renderCart();
filterProducts();
</script>
@endsection
