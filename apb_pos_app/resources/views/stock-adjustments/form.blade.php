@php
    $isEdit = isset($adjustment);
@endphp

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">{{ $isEdit ? 'Edit' : '' }} Stock Adjustment</h4>
        @if($isEdit)
            <small class="text-muted">{{ $adjustment->adjustment_no }}</small>
        @else
            <small class="text-muted">Penyesuaian stock (koreksi fisik vs sistem)</small>
        @endif
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Outlet <span class="text-danger">*</span></label>
                <select name="outlet_id" id="outlet_id" class="form-select" required>
                    <option value="">Pilih Outlet</option>
                    @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" 
                            {{ ($isEdit && $adjustment->outlet_id == $outlet->id) ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Adjustment <span class="text-danger">*</span></label>
                <input type="date" name="adjustment_date" class="form-control"
                    value="{{ $isEdit ? $adjustment->adjustment_date->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Catatan Umum</label>
                <textarea name="general_note" class="form-control" rows="2" 
                    placeholder="Catatan untuk dokumen adjustment ini">{{ $isEdit ? $adjustment->note : '' }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Produk yang Disesuaikan</h4>
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
                        <th width="35%">Produk</th>
                        <th width="12%">Stock Sistem</th>
                        <th width="12%">Stock Fisik</th>
                        <th width="12%">Selisih</th>
                        <th width="20%">Catatan</th>
                        <th width="4%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($isEdit)
                        @foreach ($adjustment->details as $index => $detail)
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
                                <td class="text-end system-stock-{{ $index + 1 }}" data-stock="{{ $detail->product->stock }}">
                                    {{ number_format($detail->product->stock, 0, ',', '.') }}
                                </td>
                                <td class="text-center compact-input-cell">
                                    <input type="text" 
                                        name="new_stock[]" 
                                        class="form-control text-end new-stock autonumeric" 
                                        data-row="{{ $index + 1 }}"
                                        value="{{ $detail->qty_physical }}"
                                        required>
                                </td>
                                <td class="text-center difference-{{ $index + 1 }}">
                                    @if ($detail->difference > 0)
                                        <span class="text-success fw-bold">+{{ number_format($detail->difference, 0, ',', '.') }}</span>
                                    @elseif ($detail->difference < 0)
                                        <span class="text-danger fw-bold">{{ number_format($detail->difference, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="text" name="note[]" class="form-control" 
                                        value="{{ $detail->note }}" placeholder="Catatan...">
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
                            <td colspan="7" class="text-center text-muted">
                                Klik "Tambah Produk" untuk memulai
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ $isEdit ? route('stock-adjustments.show', $encryptedId) : route('stock-adjustments.index') }}" 
                class="btn btn-light">
                {{ $isEdit ? 'Batal' : 'Kembali' }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> {{ $isEdit ? 'Update' : 'Simpan' }} Adjustment
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let rowIndex = {{ $isEdit ? $adjustment->details->count() : 0 }};

        $(function() {
            @if($isEdit)
                initAutoNumeric();
            @endif

            $('#addRowBtn').on('click', function() {
                let outletId = $('#outlet_id').val();

                if (!outletId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Outlet',
                        text: 'Silakan pilih outlet terlebih dahulu.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                $('.empty-row').remove();

                rowIndex++;
                let row = `
                    <tr data-row="${rowIndex}">
                        <td class="text-center">${rowIndex}</td>
                        <td>
                            <select name="product_id[]" class="form-select product-select" data-row="${rowIndex}" required>
                                <option value="">Pilih Produk</option>
                            </select>
                        </td>
                        <td class="text-end system-stock-${rowIndex}">-</td>
                        <td class="text-center compact-input-cell">
                            <input type="text" 
                                name="new_stock[]" 
                                class="form-control text-end new-stock autonumeric" 
                                data-row="${rowIndex}"
                                value="0"
                                required>
                        </td>
                        <td class="text-center difference-${rowIndex}">-</td>
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

                $('#itemsTable tbody').append(row);

                initProductSelect(rowIndex, outletId);
                initAutoNumeric();
            });

            $(document).on('click', '.remove-row', function() {
                let row = $(this).data('row');
                $(`tr[data-row="${row}"]`).remove();
                reorderRows();

                if ($('#itemsTable tbody tr').length === 0) {
                    $('#itemsTable tbody').html(`
                        <tr class="empty-row">
                            <td colspan="7" class="text-center text-muted">
                                Klik "Tambah Produk" untuk memulai
                            </td>
                        </tr>
                    `);
                }
            });

            $(document).on('keyup change', '.new-stock', function() {
                let row = $(this).data('row');
                calculateDifference(row);
            });

            $('#adjustmentForm').on('submit', function(e) {
                e.preventDefault();

                let hasItems = $('#itemsTable tbody tr:not(.empty-row)').length > 0;
                if (!hasItems) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum ada produk',
                        text: 'Tambahkan minimal 1 produk untuk adjustment.',
                        timer: 2000
                    });
                    return;
                }

                Swal.fire({
                    title: '{{ $isEdit ? "Update" : "Simpan" }} Adjustment?',
                    text: 'Stock produk akan disesuaikan sesuai data yang diinput.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, {{ $isEdit ? "Update" : "Simpan" }}',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        function initProductSelect(row, outletId) {
            $(`select.product-select[data-row="${row}"]`).select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari produk...',
                allowClear: true,
                ajax: {
                    url: "{{ route('stock-adjustments.product-search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term,
                            outlet_id: outletId
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            }).on('select2:select', function(e) {
                let currentStock = e.params.data.current_stock || 0;
                $(`.system-stock-${row}`).text(formatNumber(currentStock));
                $(`.system-stock-${row}`).data('stock', currentStock);

                let newStockInput = $(`.new-stock[data-row="${row}"]`);
                setAutoNumericValue(newStockInput[0], currentStock);

                calculateDifference(row);
            });
        }

        function calculateDifference(row) {
            let systemStock = $(`.system-stock-${row}`).data('stock') || 0;
            let newStock = getAutoNumericValue($(`.new-stock[data-row="${row}"]`)[0]);
            let diff = newStock - systemStock;

            let diffCell = $(`.difference-${row}`);
            if (diff > 0) {
                diffCell.html(`<span class="text-success fw-bold">+${formatNumber(diff)}</span>`);
            } else if (diff < 0) {
                diffCell.html(`<span class="text-danger fw-bold">${formatNumber(diff)}</span>`);
            } else {
                diffCell.html(`<span class="text-muted">0</span>`);
            }
        }

        function reorderRows() {
            $('#itemsTable tbody tr:not(.empty-row)').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        function formatNumber(number) {
            number = parseFloat(number) || 0;
            return number.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    </script>
@endpush
