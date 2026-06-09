@extends('layouts.app')

@section('title', 'Transaksi Kasir')

@push('styles')
    <style>
        .sidebar {
            display: none !important;
        }

        .sidebar + .main-content,
        .main-content {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .iq-navbar .sidebar-toggle {
            display: none !important;
        }

        .iq-navbar-header {
            height: 86px !important;
        }

        .iq-header-img {
            display: none !important;
        }

        .content-inner {
            padding-left: 18px !important;
            padding-right: 18px !important;
        }

        .cashier-page {
            margin-top: -34px;
        }

        .cashier-payment-card {
            position: sticky;
            top: 86px;
        }

        .cashier-page .card {
            border-radius: 8px;
        }

        .cashier-page .table-responsive {
            min-height: 360px;
        }

        .cashier-page .form-label {
            margin-bottom: 0.25rem;
            font-size: 12px;
            font-weight: 600;
        }

        .cashier-page .form-control,
        .cashier-page .form-select {
            min-height: 38px;
            height: 38px;
            font-size: 13px;
        }

        .cashier-page textarea.form-control {
            height: auto;
            min-height: 56px;
        }

        .cashier-customer-group .select2-container {
            flex: 1 1 auto;
        }

        .cashier-add-customer {
            width: 42px;
            min-width: 42px;
            height: 42px;
            padding: 0;
        }

        @media (max-width: 991.98px) {
            .cashier-page {
                margin-top: 0;
            }

            .cashier-payment-card {
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('sales.store') }}" method="POST" id="cashier-form">
        @csrf

        <div class="row cashier-page">
            <div class="col-lg-9">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Transaksi gagal disimpan.</strong>
                        <div class="mt-1">
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Transaksi Kasir</h4>
                            <small class="text-muted">Cari produk, masukkan ke keranjang, lalu proses pembayaran.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('sales.history') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line"></i> Sales History
                            </a>
                            <span class="badge bg-primary">{{ now()->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Outlet</label>
                                <select name="outlet_id" id="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror" required>
                                    <option value="">Pilih Outlet</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" {{ (string) $defaultOutletId === (string) $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('outlet_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Customer</label>
                                <div class="d-flex gap-2 cashier-customer-group">
                                    <select name="customer_id" id="customer_id" class="form-select">
                                        <option value="">Umum</option>
                                        @if (old('customer_id'))
                                            @php
                                                $selectedCustomer = $customers->firstWhere('id', old('customer_id'));
                                            @endphp

                                            @if ($selectedCustomer)
                                                <option value="{{ $selectedCustomer->id }}" selected>
                                                    {{ $selectedCustomer->name }}{{ $selectedCustomer->phone ? ' - ' . $selectedCustomer->phone : '' }}
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success cashier-add-customer" data-bs-toggle="modal"
                                        data-bs-target="#customer-modal" title="Tambah Customer">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror"
                                    value="{{ old('sale_date', now()->toDateString()) }}" required>
                                @error('sale_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Cari / Scan Produk</label>
                            <select id="product-search" class="form-select">
                                <option value="">Ketik SKU, barcode, atau nama produk</option>
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle table-transaction" id="cart-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th width="90">Stok</th>
                                        <th width="95">Qty</th>
                                        <th width="150">Harga</th>
                                        <th width="140">Diskon</th>
                                        <th width="160">Subtotal</th>
                                        <th width="60" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="empty-cart-row">
                                        <td colspan="7" class="text-center text-muted py-4">Keranjang masih kosong.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Opsional">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card cashier-payment-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Pembayaran</h4>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="text" id="subtotal_display" class="form-control text-end autonumeric-readonly" value="0" readonly>
                            <input type="hidden" id="subtotal" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diskon Transaksi</label>
                            <input type="text" name="discount_amount" id="discount_amount"
                                class="form-control text-end autonumeric" value="{{ old('discount_amount', 0) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" id="tax_label">Pajak</label>
                            <input type="text" name="tax_amount" id="tax_amount"
                                class="form-control text-end autonumeric-readonly" value="{{ old('tax_amount', 0) }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Grand Total</label>
                            <input type="text" id="grand_total_display" class="form-control text-end fw-bold autonumeric-readonly" value="0" readonly>
                            <input type="hidden" id="grand_total" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Metode Bayar</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="debit" {{ old('payment_method') === 'debit' ? 'selected' : '' }}>Debit</option>
                                <option value="credit" {{ old('payment_method') === 'credit' ? 'selected' : '' }}>Credit</option>
                                <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bayar</label>
                            <input type="text" name="paid_amount" id="paid_amount"
                                class="form-control text-end autonumeric @error('paid_amount') is-invalid @enderror"
                                value="{{ old('paid_amount', 0) }}" required>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Kembalian</label>
                            <input type="text" id="change_amount_display" class="form-control text-end fw-bold autonumeric-readonly" value="0" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="btn-pay">
                            <i class="ri-bank-card-line"></i> Simpan Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="customer-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form id="customer-form" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" id="customer_name" class="form-control" required>
                        <div class="invalid-feedback" id="customer_name_error"></div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" name="phone" id="customer_phone" class="form-control" placeholder="081234567890">
                        <div class="invalid-feedback" id="customer_phone_error"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-customer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const cart = new Map();
        const outletTaxRates = @json($outletTaxRates);

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(Math.max(Number(value) || 0, 0));
        }

        function getSelectedTaxRate() {
            return Number(outletTaxRates[$('#outlet_id').val()] || 0);
        }

        function refreshTaxLabel() {
            const taxRate = getSelectedTaxRate();
            $('#tax_label').text('Pajak (' + taxRate.toLocaleString('id-ID') + '%)');
        }

        function refreshCart() {
            const tbody = $('#cart-table tbody');
            tbody.empty();

            if (cart.size === 0) {
                tbody.append('<tr id="empty-cart-row"><td colspan="7" class="text-center text-muted py-4">Keranjang masih kosong.</td></tr>');
                calculateTotal();
                return;
            }

            cart.forEach((item) => {
                tbody.append(`
                    <tr data-id="${item.id}">
                        <td>
                            <strong>${item.name}</strong><br>
                            <small class="text-muted">${item.sku || '-'} ${item.unit ? ' / ' + item.unit : ''}</small>
                            <input type="hidden" name="product_id[]" value="${item.id}">
                            <input type="hidden" name="price[]" value="${item.price}">
                            <input type="hidden" name="item_discount_amount[]" class="item-discount-hidden" value="${item.discount}">
                        </td>
                        <td class="text-center">${item.stock}</td>
                        <td>
                            <input type="number" name="qty[]" class="form-control text-end cart-qty" min="1" max="${item.stock}" value="${item.qty}">
                        </td>
                        <td class="text-end">Rp ${formatRupiah(item.price)}</td>
                        <td>
                            <input type="text" class="form-control text-end cart-discount autonumeric" value="${item.discount}">
                        </td>
                        <td class="text-end fw-semibold cart-subtotal">Rp ${formatRupiah((item.qty * item.price) - item.discount)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            initAutoNumeric('.cart-discount');
            calculateTotal();
        }

        function addProduct(product) {
            const productId = String(product.id);

            if (cart.has(productId)) {
                const item = cart.get(productId);
                if (item.qty >= item.stock) {
                    Swal.fire('Info', 'Qty sudah mencapai stok tersedia.', 'info');
                    return;
                }
                item.qty += 1;
                cart.set(productId, item);
            } else {
                cart.set(productId, {
                    id: productId,
                    sku: product.sku,
                    name: product.name,
                    price: Number(product.price) || 0,
                    stock: Number(product.stock) || 0,
                    unit: product.unit || '',
                    qty: 1,
                    discount: 0,
                });
            }

            refreshCart();
        }

        function calculateTotal() {
            let subtotal = 0;

            $('#cart-table tbody tr[data-id]').each(function() {
                const id = String($(this).data('id'));
                const item = cart.get(id);

                if (!item) return;

                item.qty = Math.min(Math.max(Number($(this).find('.cart-qty').val()) || 1, 1), item.stock);
                item.discount = getAutoNumericValue($(this).find('.cart-discount')[0]);

                const itemSubtotal = Math.max((item.qty * item.price) - item.discount, 0);
                subtotal += itemSubtotal;

                $(this).find('.cart-qty').val(item.qty);
                $(this).find('.item-discount-hidden').val(item.discount);
                $(this).find('.cart-subtotal').text('Rp ' + formatRupiah(itemSubtotal));
                cart.set(id, item);
            });

            const discountAmount = getAutoNumericValue($('#discount_amount')[0]);
            const paidAmount = getAutoNumericValue($('#paid_amount')[0]);
            const taxRate = getSelectedTaxRate();
            const taxableAmount = Math.max(subtotal - discountAmount, 0);
            const taxAmount = Math.round(taxableAmount * taxRate / 100);
            const grandTotal = Math.max(taxableAmount + taxAmount, 0);
            const changeAmount = Math.max(paidAmount - grandTotal, 0);

            $('#subtotal').val(subtotal);
            $('#grand_total').val(grandTotal);
            setAutoNumericValue($('#subtotal_display')[0], subtotal);
            setAutoNumericValue($('#tax_amount')[0], taxAmount);
            setAutoNumericValue($('#grand_total_display')[0], grandTotal);
            setAutoNumericValue($('#change_amount_display')[0], changeAmount);
        }

        $(function() {
            initAutoNumeric();

            $('#customer_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Umum / cari customer...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('sales.customer-search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            outlet_id: $('#outlet_id').val()
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                }
            });

            $('#product-search').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik SKU, barcode, atau nama produk',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('sales.product-search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            outlet_id: $('#outlet_id').val()
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                },
                templateResult: function(item) {
                    if (!item.id) return item.text;
                    return $(`<div><strong>${item.text}</strong><br><small>Stok: ${item.stock} | Harga: Rp ${formatRupiah(item.price)}</small></div>`);
                }
            });

            $('#product-search').on('select2:select', function(e) {
                addProduct(e.params.data);
                $(this).val(null).trigger('change');
            });

            $('#outlet_id').on('change', function() {
                $('#customer_id').val(null).trigger('change');
                refreshTaxLabel();
                calculateTotal();

                if (cart.size === 0) return;

                Swal.fire({
                    title: 'Ganti outlet?',
                    text: 'Keranjang akan dikosongkan karena stok mengikuti outlet.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kosongkan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart.clear();
                        refreshCart();
                    }
                });
            });

            $('#customer-modal').on('shown.bs.modal', function() {
                $('#customer_name').trigger('focus');
            });

            $('#customer-modal').on('hidden.bs.modal', function() {
                $('#customer-form')[0].reset();
                $('#customer_name, #customer_phone').removeClass('is-invalid');
                $('#customer_name_error, #customer_phone_error').text('');
            });

            $('#customer-form').on('submit', function(e) {
                e.preventDefault();

                $('#customer_name, #customer_phone').removeClass('is-invalid');
                $('#customer_name_error, #customer_phone_error').text('');
                $('#btn-save-customer').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('sales.customer-store') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        outlet_id: $('#outlet_id').val(),
                        name: $('#customer_name').val(),
                        phone: $('#customer_phone').val()
                    },
                    success: function(response) {
                        const option = new Option(response.text, response.id, true, true);
                        $('#customer_id').append(option).trigger('change');
                        $('#customer-modal').modal('hide');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2200,
                            timerProgressBar: true
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};

                        if (errors.name) {
                            $('#customer_name').addClass('is-invalid');
                            $('#customer_name_error').text(errors.name[0]);
                        }

                        if (errors.phone) {
                            $('#customer_phone').addClass('is-invalid');
                            $('#customer_phone_error').text(errors.phone[0]);
                        }

                        if (!errors.name && !errors.phone) {
                            Swal.fire('Gagal', xhr.responseJSON?.message ?? 'Customer gagal ditambahkan.', 'error');
                        }
                    },
                    complete: function() {
                        $('#btn-save-customer').prop('disabled', false).text('Simpan');
                    }
                });
            });

            $(document).on('input keyup change', '.cart-qty, .cart-discount, #discount_amount, #paid_amount', calculateTotal);

            $(document).on('click', '.btn-remove-item', function() {
                cart.delete(String($(this).closest('tr').data('id')));
                refreshCart();
            });

            $('#cashier-form').on('submit', function(e) {
                calculateTotal();

                if (cart.size === 0) {
                    e.preventDefault();
                    Swal.fire('Info', 'Tambahkan minimal 1 produk ke keranjang.', 'info');
                    return;
                }

                if (getAutoNumericValue($('#paid_amount')[0]) < Number($('#grand_total').val())) {
                    e.preventDefault();
                    Swal.fire('Info', 'Nominal bayar kurang dari grand total.', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Sedang menyimpan transaksi...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            });

            refreshTaxLabel();
            refreshCart();
        });
    </script>
@endpush
