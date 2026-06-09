@php
    $isEdit = isset($transfer);
@endphp

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">{{ $isEdit ? 'Edit' : '' }} Stock Transfer</h4>
        @if($isEdit)
            <small class="text-muted">{{ $transfer->transfer_no }}</small>
        @else
            <small class="text-muted">Transfer stock antar outlet</small>
        @endif
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Dari Outlet <span class="text-danger">*</span></label>
                <select name="from_outlet_id" id="from_outlet_id" class="form-select" required>
                    <option value="">Pilih Outlet Asal</option>
                    @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" 
                            {{ ($isEdit && $transfer->from_outlet_id == $outlet->id) ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Ke Outlet <span class="text-danger">*</span></label>
                <select name="to_outlet_id" id="to_outlet_id" class="form-select" required>
                    <option value="">Pilih Outlet Tujuan</option>
                    @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" 
                            {{ ($isEdit && $transfer->to_outlet_id == $outlet->id) ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Transfer <span class="text-danger">*</span></label>
                <input type="date" name="transfer_date" class="form-control"
                    value="{{ $isEdit ? $transfer->transfer_date->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Catatan Umum</label>
                <textarea name="general_note" class="form-control" rows="2" 
                    placeholder="Catatan untuk dokumen transfer ini">{{ $isEdit ? $transfer->note : '' }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Produk yang Ditransfer</h4>
        <button type="button" class="btn btn-sm btn-primary" id="addRowBtn">
            <i class="ri-add-line"></i> Tambah Produk
        </button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-transaction" id="itemsTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="40%">Produk</th>
                        <th width="15%">Stock Asal</th>
                        <th width="15%">Qty Transfer</th>
                        <th width="20%">Catatan</th>
                        <th width="5%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($isEdit)
                        @foreach ($transfer->details as $index => $detail)
                            <tr data-row="{{ $index + 1 }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <select name="product_id[]" class="form-select product-select" 
                                        data-row="{{ $index + 1 }}" required>
                                        <option value="{{ $detail->product_id }}">
                                            {{ ($detail->product->sku ? $detail->product->sku . ' - ' : '') . $detail->product->name }}
                                        </option>
                                    </select>
                                </td>
                                <td class="text-end current-stock-{{ $index + 1 }}" data-stock="{{ $detail->product->stock }}">
                                    {{ number_format($detail->product->stock, 0, ',', '.') }}
                                </td>
                                <td class="text-center compact-input-cell">
                                    <input type="text" 
                                        name="quantity[]" 
                                        class="form-control text-end quantity autonumeric" 
                                        data-row="{{ $index + 1 }}"
                                        value="{{ $detail->quantity }}"
                                        required>
                                </td>
                                <td>
                                    <input type="text" name="note[]" class="form-control" 
                                        placeholder="Catatan...">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" data-row="{{ $index + 1 }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="empty-row">
                            <td colspan="6" class="text-center text-muted">
                                Klik "Tambah Produk" untuk memulai
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ $isEdit ? route('stock-transfers.show', $encryptedId) : route('stock-transfers.index') }}" 
                class="btn btn-light">
                {{ $isEdit ? 'Batal' : 'Kembali' }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> {{ $isEdit ? 'Update' : 'Simpan' }} Transfer
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let rowIndex = {{ $isEdit ? $transfer->details->count() : 0 }};

        $(function() {
            @if($isEdit)
                initAutoNumeric();
            @endif

            $('#addRowBtn').on('click', function() {
                let fromOutletId = $('#from_outlet_id').val();

                if (!fromOutletId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Outlet Asal',
                        text: 'Silakan pilih outlet asal terlebih dahulu.',
                    });
                    return;
                }

                rowIndex++;
                let newRow = `
                    <tr data-row="${rowIndex}">
                        <td class="text-center">${$('#itemsTable tbody tr').length + 1}</td>
                        <td>
                            <select name="product_id[]" class="form-select product-select" data-row="${rowIndex}" required>
                                <option value="">Pilih Produk</option>
                            </select>
                        </td>
                        <td class="text-end current-stock-${rowIndex}">0</td>
                        <td class="text-center compact-input-cell">
                            <input type="text" name="quantity[]" class="form-control text-end quantity autonumeric" 
                                data-row="${rowIndex}" value="0" required>
                        </td>
                        <td>
                            <input type="text" name="note[]" class="form-control" placeholder="Catatan...">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-row" data-row="${rowIndex}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                `;

                if ($('#itemsTable tbody tr.empty-row').length > 0) {
                    $('#itemsTable tbody tr.empty-row').replaceWith(newRow);
                } else {
                    $('#itemsTable tbody').append(newRow);
                }

                initAutoNumeric();
                initProductSelect();
                renumberRows();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                renumberRows();
                if ($('#itemsTable tbody tr').length === 0) {
                    $('#itemsTable tbody').html(`
                        <tr class="empty-row">
                            <td colspan="6" class="text-center text-muted">
                                Klik "Tambah Produk" untuk memulai
                            </td>
                        </tr>
                    `);
                }
            });

            initProductSelect();
        });

        function initProductSelect() {
            $('.product-select').off('select2').select2({
                allowClear: true,
                placeholder: 'Cari produk...',
                ajax: {
                    url: '{{ route('stock-transfers.product-search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            outlet_id: $('#from_outlet_id').val(),
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    return $('<span>' + data.text + ' <small class="text-muted">(' + 
                        data.current_stock + ' stok)</small></span>');
                }
            });

            $('.product-select').off('change').on('change', function() {
                let row = $(this).data('row');
                let productId = $(this).val();
                
                if (productId) {
                    let selected = $(this).select2('data')[0];
                    $('.current-stock-' + row).text(selected.current_stock)
                        .data('stock', selected.current_stock);
                }
            });
        }

        function renumberRows() {
            $('#itemsTable tbody tr:not(.empty-row)').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        $('#transferForm').on('submit', function(e) {
            let hasItems = $('#itemsTable tbody tr:not(.empty-row)').length > 0;

            if (!hasItems) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Produk Kosong',
                    text: 'Silakan tambahkan minimal 1 produk sebelum menyimpan.',
                });
                return false;
            }
        });
    </script>
@endpush
