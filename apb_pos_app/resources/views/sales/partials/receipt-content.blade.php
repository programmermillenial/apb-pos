<h1 class="receipt-title">{{ $sale->store->name ?? config('app.name', 'APB POS') }}</h1>
<p class="receipt-subtitle">{{ $sale->outlet->name ?? '-' }}</p>
@if ($sale->store?->address || $sale->outlet?->address)
    <p class="receipt-subtitle">{{ $sale->store->address ?? $sale->outlet->address }}</p>
@endif
@if ($sale->store?->phone || $sale->outlet?->phone)
    <p class="receipt-subtitle">Telp: {{ $sale->store->phone ?? $sale->outlet->phone }}</p>
@endif

<div class="receipt-line"></div>

<div class="receipt-row">
    <span class="receipt-label">Invoice</span>
    <span class="receipt-value">{{ $sale->invoice_number }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Tanggal</span>
    <span class="receipt-value">{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Kasir</span>
    <span class="receipt-value">{{ $sale->creator->name ?? '-' }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Customer</span>
    <span class="receipt-value">{{ $sale->customer->name ?? 'Umum' }}</span>
</div>

<div class="receipt-line"></div>

@foreach ($sale->saleDetails as $item)
    <div class="receipt-item">
        <div class="receipt-item-name">{{ $item->product_name }}</div>
        <div class="receipt-row">
            <span class="receipt-label">{{ number_format($item->qty, 0, ',', '.') }} x {{ number_format($item->price, 0, ',', '.') }}</span>
            <span class="receipt-value">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @if ($item->discount_amount > 0)
            <div class="receipt-row">
                <span class="receipt-label">Diskon Item</span>
                <span class="receipt-value">-{{ number_format($item->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
@endforeach

<div class="receipt-line"></div>

<div class="receipt-row">
    <span class="receipt-label">Subtotal</span>
    <span class="receipt-value">{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Diskon</span>
    <span class="receipt-value">{{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Pajak</span>
    <span class="receipt-value">{{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
</div>
<div class="receipt-row receipt-total">
    <span class="receipt-label">Total</span>
    <span class="receipt-value">{{ number_format($sale->grand_total, 0, ',', '.') }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Bayar ({{ strtoupper($sale->payment_method) }})</span>
    <span class="receipt-value">{{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
</div>
<div class="receipt-row">
    <span class="receipt-label">Kembalian</span>
    <span class="receipt-value">{{ number_format($sale->change_amount, 0, ',', '.') }}</span>
</div>

@if ($sale->note)
    <div class="receipt-line"></div>
    <div>{{ $sale->note }}</div>
@endif

<div class="receipt-line"></div>

<p class="receipt-subtitle">Terima kasih</p>
<p class="receipt-subtitle">Barang yang sudah dibeli tidak dapat dikembalikan.</p>
