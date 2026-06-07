@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('goods-receipts.store') }}" method="POST" id="goodsReceiptForm">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Create Goods Receipt</h4>
                        <small class="text-muted">Penerimaan barang berdasarkan Purchase Order yang sudah approved</small>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Order</label>
                                <select name="purchase_order_id" id="purchase_order_id" class="form-control"
                                    required></select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Receipt Date</label>
                                <input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan penerimaan barang"></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Items</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-transaction" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Product</th>
                                        <th width="12%">Ordered Qty</th>
                                        <th width="12%">Received</th>
                                        <th width="12%">Remaining</th>
                                        <th width="15%">Receive Now</th>
                                        <th width="15%">Cost Price</th>
                                        <th width="15%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <td colspan="8" class="text-center text-muted">
                                        Pilih Purchase Order terlebih dahulu
                                    </td>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <th colspan="7" class="text-end">TOTAL</th>
                                        <th id="grandTotal">Rp 0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('goods-receipts.index') }}" class="btn btn-light">
                                Kembali
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#purchase_order_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari Purchase Order...',
                allowClear: true,
                ajax: {
                    url: "{{ route('goods-receipts.search-po') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#purchase_order_id').on('change', function() {
                let poId = $(this).val();

                if (!poId) {
                    $('#itemsTable tbody').html(`
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Pilih Purchase Order terlebih dahulu
                            </td>
                        </tr>
                    `);

                    $('#grandTotal').text('Rp 0');
                    return;
                }

                let url = "{{ route('goods-receipts.po-details', ':id') }}";
                url = url.replace(':id', poId);

                $.ajax({
                    url: url,
                    type: "GET",
                    beforeSend: function() {
                        $('#itemsTable tbody').html(`
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        Loading items...
                                    </td>
                                </tr>
                        `);
                    },
                    success: function(res) {
                        let html = '';

                        if (res.details.length === 0) {
                            html = `
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            Item tidak ditemukan
                                        </td>
                                    </tr>
                                `;
                        } else {
                            $.each(res.details, function(index, item) {
                                html += `
                                            <tr>
                                                <td class="text-center">${index + 1}</td>
                                                <td>${item.product_name}</td>
                                                <td class="text-end">${formatNumber(item.qty)}</td>
                                                <td class="text-end">${formatNumber(item.received_qty)}</td>
                                                <td class="text-end">${formatNumber(item.remaining_qty)}</td>
                                                <td class="text-center compact-input-cell">
                                                    <input type="text"
                                                        name="received_qty[${item.id}]"
                                                        class="form-control text-end received-qty autonumeric"
                                                        data-price="${item.price}"
                                                        data-max="${item.remaining_qty}"
                                                        value="${item.remaining_qty}">
                                                </td>
                                                <td class="text-end">Rp ${formatNumber(item.price)}</td>
                                                <td class="text-end row-subtotal">Rp ${formatNumber(item.subtotal)}</td>
                                            </tr>
                                        `;
                            });
                        }

                        $('#itemsTable tbody').html(html);
                        initAutoNumeric();
                        calculateTotal();
                    },
                    error: function(xhr) {
                        $('#itemsTable tbody').html(`
                                <tr>
                                    <td colspan="8" class="text-center text-danger">
                                        Gagal mengambil item Purchase Order
                                    </td>
                                </tr>
                        `);

                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('keyup change', '.received-qty', function() {
                let row = $(this).closest('tr');
                let qty = getAutoNumericValue(this);
                let max = parseFloat($(this).data('max')) || 0;
                let price = parseFloat($(this).data('price')) || 0;

                if (qty > max) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Qty melebihi sisa',
                        text: 'Qty diterima tidak boleh lebih dari sisa qty PO.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    let an = $(this).data('autonumeric');

                    if (an) {
                        an.set(max);
                    } else {
                        $(this).val(max);
                    }

                    qty = max;
                }

                let subtotal = qty * price;

                row.find('.row-subtotal').text('Rp ' + formatNumber(subtotal));

                calculateTotal();
            });

            $('#goodsReceiptForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Simpan Goods Receipt?',
                    text: 'Stok produk akan bertambah sesuai qty yang diterima.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            function calculateTotal() {
                let total = 0;

                $('.received-qty').each(function() {
                    let qty = getAutoNumericValue(this);
                    let price = parseFloat($(this).data('price')) || 0;

                    total += qty * price;
                });

                $('#grandTotal').text('Rp ' + formatNumber(total));
            }

            function formatNumber(number) {
                number = parseFloat(number) || 0;

                return number.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }
        });
    </script>
@endpush
