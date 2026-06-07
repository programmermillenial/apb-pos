@php
    $isEdit = isset($purchaseOrder);
@endphp

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Outlet</label>
        <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror" required>
            <option value="">Pilih Outlet</option>
            @foreach ($outlets as $outlet)
                <option value="{{ $outlet->id }}"
                    {{ old('outlet_id', $purchaseOrder->outlet_id ?? '') == $outlet->id ? 'selected' : '' }}>
                    {{ $outlet->name }}
                </option>
            @endforeach
        </select>
        @error('outlet_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">Pilih Supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Tanggal PO</label>
        <input type="date" name="po_date" class="form-control @error('po_date') is-invalid @enderror"
            value="{{ old('po_date', $purchaseOrder->po_date ?? date('Y-m-d')) }}" required>
        @error('po_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Estimasi Datang</label>
        <input type="date" name="expected_date" class="form-control"
            value="{{ old('expected_date', $purchaseOrder->expected_date ?? '') }}">
    </div>
</div>

<hr>

<div class="table-responsive">
    <table class="table table-bordered align-middle table-transaction" id="po-item-table">
        <thead>
            <tr>
                <th width="30%">Produk</th>
                <th width="10%">Qty</th>
                <th width="15%">Harga</th>
                <th width="15%">Diskon</th>
                <th width="15%">Subtotal</th>
                <th width="5%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($isEdit)
                @foreach ($purchaseOrder->purchaseOrderDetails as $item)
                    <tr>
                        <td>
                            <select name="product_id[]" class="form-select product-select" required>
                                <option value="{{ $item->product_id }}" selected>
                                    {{ $item->sku }} - {{ $item->product_name }}
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="qty[]" class="form-control qty text-end"
                                value="{{ $item->qty }}" min="1" required>
                        </td>
                        <td>
                            <input type="text" name="price[]" class="form-control price text-end autonumeric"
                                value="{{ $item->price }}" required>
                        </td>
                        <td>
                            <input type="text" name="item_discount_amount[]"
                                class="form-control item-discount text-end autonumeric"
                                value="{{ $item->discount_amount }}">
                        </td>
                        <td>
                            <input type="text" class="form-control item-subtotal text-end autonumeric-readonly"
                                value="{{ $item->subtotal }}" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        <select name="product_id[]" class="form-select product-select" required>
                            <option value="">Cari Produk</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="qty[]" class="form-control qty text-end" value="1"
                            min="1" required>
                    </td>
                    <td>
                        <input type="text" name="price[]" class="form-control price text-end autonumeric"
                            value="0" required>
                    </td>
                    <td>
                        <input type="text" name="item_discount_amount[]"
                            class="form-control item-discount text-end autonumeric" value="0">
                    </td>
                    <td>
                        <input type="text" class="form-control item-subtotal text-end autonumeric-readonly"
                            value="0" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<button type="button" class="btn btn-success btn-sm" id="btn-add-item">
    <i class="ri-add-line"></i> Tambah Item
</button>

<hr>

<div class="row justify-content-end mt-3">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Subtotal</label>
            <input type="text" id="subtotal" class="form-control text-end autonumeric-readonly" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Diskon PO</label>
            <input type="text" name="discount_amount" id="discount_amount" class="form-control text-end autonumeric"
                value="{{ old('discount_amount', isset($purchaseOrder) ? $purchaseOrder->discount_amount : 0) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Pajak</label>
            <input type="text" name="tax_amount" id="tax_amount" class="form-control text-end autonumeric"
                value="{{ old('tax_amount', isset($purchaseOrder) ? $purchaseOrder->tax_amount : 0) }}">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Grand Total</label>
            <input type="text" id="grand_total" class="form-control text-end fw-bold autonumeric-readonly"
                readonly>
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="note" class="form-control" rows="3">{{ old('note', $purchaseOrder->note ?? '') }}</textarea>
</div>

<div class="text-end">
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-light">
        Kembali
    </a>

    <button type="submit" class="btn btn-primary" data-loading="true">
        {{ $button }}
    </button>
</div>

@push('scripts')
    <script>
        function initProductSelect2(element = '.product-select') {
            $(element).select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari produk...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('purchase-orders.product-search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                }
            });
        }

        function calculateTotal() {
            let subtotal = 0;

            $('#po-item-table tbody tr').each(function() {
                let qty = parseFloat($(this).find('.qty').val()) || 0;
                let price = getAutoNumericValue($(this).find('.price')[0]);
                let discount = getAutoNumericValue($(this).find('.item-discount')[0]);


                let itemSubtotal = (qty * price) - discount;
                if (itemSubtotal < 0) itemSubtotal = 0;

                setAutoNumericValue($(this).find('.item-subtotal')[0], itemSubtotal);

                subtotal += itemSubtotal;
            });

            let discountAmount = getAutoNumericValue($('#discount_amount')[0]);
            let taxAmount = getAutoNumericValue($('#tax_amount')[0]);


            let grandTotal = subtotal - discountAmount + taxAmount;
            if (grandTotal < 0) grandTotal = 0;

            setAutoNumericValue($('#subtotal')[0], subtotal);
            setAutoNumericValue($('#grand_total')[0], grandTotal);
        }

        $(document).on('select2:select', '.product-select', function(e) {
            let data = e.params.data;
            let price = data.cost_price ?? 0;
            setAutoNumericValue($(this).closest('tr').find('.price')[0], price);
            calculateTotal();
        });

        $(document).on('select2:clear', '.product-select', function() {
            setAutoNumericValue($(this).closest('tr').find('.price')[0], 0);
            calculateTotal();
        });

        $(document).on('input keyup change', '.qty, .price, .item-discount, #discount_amount, #tax_amount', function() {
            calculateTotal();
        });

        $('#btn-add-item').on('click', function() {
            let rowIndex = Date.now();

            let row = `
                        <tr>
                            <td>
                                <select name="product_id[]" class="form-select product-select product-select-${rowIndex}" required>
                                    <option value="">Cari Produk</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="qty[]" class="form-control qty text-end" value="1" min="1" required>
                            </td>
                            <td>
                                <input type="text" name="price[]" class="form-control price text-end autonumeric" value="0" required>
                            </td>
                            <td>
                                <input type="text" name="item_discount_amount[]" class="form-control item-discount text-end autonumeric" value="0">
                            </td>
                            <td>
                                <input type="text" class="form-control item-subtotal text-end autonumeric-readonly" value="0" readonly>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
            `;

            $('#po-item-table tbody').append(row);

            initProductSelect2('.product-select-' + rowIndex);
            initAutoNumeric();
            calculateTotal();
        });

        $(document).on('click', '.btn-remove-item', function() {
            if ($('#po-item-table tbody tr').length <= 1) {
                Swal.fire('Info', 'Minimal harus ada 1 item produk.', 'info');
                return;
            }

            $(this).closest('tr').remove();
            calculateTotal();
        });

        $(document).ready(function() {
            initProductSelect2();
            initAutoNumeric();
            calculateTotal();
        });
    </script>
@endpush
