@php
    $isEdit = isset($opname);
@endphp

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">{{ $isEdit ? 'Edit' : '' }} Stock Opname</h4>
        @if($isEdit)
            <small class="text-muted">{{ $opname->opname_no }}</small>
        @else
            <small class="text-muted">Physical inventory count & verification</small>
        @endif
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Outlet <span class="text-danger">*</span></label>
                <select name="outlet_id" id="outlet_id" class="form-select" required {{ $isEdit ? 'disabled' : '' }}>
                    <option value="">Pilih Outlet</option>
                    @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" 
                            {{ ($isEdit && $opname->outlet_id == $outlet->id) ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                    @endforeach
                </select>
                @if($isEdit)
                    <input type="hidden" name="outlet_id" value="{{ $opname->outlet_id }}">
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Opname <span class="text-danger">*</span></label>
                <input type="date" name="opname_date" class="form-control"
                    value="{{ $isEdit ? $opname->opname_date->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tipe Opname <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-select" required {{ $isEdit ? 'disabled' : '' }}>
                    <option value="">Pilih Tipe</option>
                    <option value="full" {{ ($isEdit && $opname->type === 'full') ? 'selected' : '' }}>Full Opname (Semua Produk)</option>
                    <option value="partial" {{ ($isEdit && $opname->type === 'partial') ? 'selected' : '' }}>Partial Opname (Produk Tertentu)</option>
                </select>
                @if($isEdit)
                    <input type="hidden" name="type" value="{{ $opname->type }}">
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">PIC / Penanggung Jawab <span class="text-danger">*</span></label>
                <input type="text" name="pic_name" class="form-control" 
                    value="{{ $isEdit ? $opname->pic_name : '' }}" 
                    placeholder="Nama PIC yang melakukan perhitungan" required>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="note" class="form-control" rows="2" 
                    placeholder="Catatan untuk dokumen opname ini">{{ $isEdit ? $opname->note : '' }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Produk untuk Opname</h4>
        <button type="button" class="btn btn-sm btn-primary" id="loadProductsBtn" style="display: none;">
            <i class="ri-refresh-line"></i> Load Produk
        </button>
    </div>

    <div class="card-body">
        <div id="loadingProducts" style="display: none;">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat produk...</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-transaction" id="itemsTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Produk</th>
                        <th width="12%">Stock Sistem</th>
                        <th width="12%">Qty Hitung</th>
                        <th width="12%">Selisih</th>
                        <th width="20%">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @if($isEdit)
                        @foreach ($opname->details as $index => $detail)
                            <tr data-row="{{ $index + 1 }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="product_id[]" value="{{ $detail->product_id }}">
                                    <strong>{{ $detail->product->name }}</strong>
                                    @if($detail->product->sku)
                                        <br><small class="text-muted">SKU: {{ $detail->product->sku }}</small>
                                    @endif
                                </td>
                                <td class="text-end system-stock-{{ $index + 1 }}" data-stock="{{ $detail->qty_system }}">
                                    {{ number_format($detail->qty_system, 0, ',', '.') }}
                                </td>
                                <td class="text-center compact-input-cell">
                                    <input type="text" 
                                        name="qty_counted[]" 
                                        class="form-control text-end qty-counted autonumeric" 
                                        data-row="{{ $index + 1 }}"
                                        value="{{ $detail->qty_counted }}"
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
                                    <input type="text" name="detail_note[]" class="form-control" 
                                        value="{{ $detail->note }}" placeholder="Catatan...">
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="empty-row">
                            <td colspan="6" class="text-center text-muted">
                                Pilih outlet dan tipe opname, lalu klik "Load Produk"
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ $isEdit ? route('stock-opnames.show', $encryptedId) : route('stock-opnames.index') }}" 
                class="btn btn-light">
                {{ $isEdit ? 'Batal' : 'Kembali' }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> {{ $isEdit ? 'Update' : 'Simpan' }} Opname
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            @if($isEdit)
                initAutoNumeric();
            @endif

            $('#outlet_id, #type').on('change', function() {
                let outletId = $('#outlet_id').val();
                let type = $('#type').val();

                if (outletId && type) {
                    $('#loadProductsBtn').show();
                } else {
                    $('#loadProductsBtn').hide();
                }
            });

            $('#loadProductsBtn').on('click', function() {
                let outletId = $('#outlet_id').val();
                let type = $('#type').val();

                if (!outletId || !type) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Tidak Lengkap',
                        text: 'Pilih outlet dan tipe opname terlebih dahulu.',
                        timer: 2000
                    });
                    return;
                }

                $('#loadingProducts').show();
                $('#itemsTable').hide();

                $.ajax({
                    url: "{{ route('stock-opnames.get-products') }}",
                    type: 'GET',
                    data: {
                        outlet_id: outletId,
                        type: type
                    },
                    success: function(response) {
                        $('#itemsTable tbody').empty();

                        if (response.length === 0) {
                            $('#itemsTable tbody').html(`
                                <tr class="empty-row">
                                    <td colspan="6" class="text-center text-muted">
                                        Tidak ada produk ditemukan
                                    </td>
                                </tr>
                            `);
                        } else {
                            $.each(response, function(index, product) {
                                let row = `
                                    <tr data-row="${index + 1}">
                                        <td class="text-center">${index + 1}</td>
                                        <td>
                                            <input type="hidden" name="product_id[]" value="${product.id}">
                                            <strong>${product.name}</strong>
                                            ${product.sku ? `<br><small class="text-muted">SKU: ${product.sku}</small>` : ''}
                                        </td>
                                        <td class="text-end system-stock-${index + 1}" data-stock="${product.stock}">
                                            ${formatNumber(product.stock)}
                                        </td>
                                        <td class="text-center compact-input-cell">
                                            <input type="text" 
                                                name="qty_counted[]" 
                                                class="form-control text-end qty-counted autonumeric" 
                                                data-row="${index + 1}"
                                                value="0"
                                                required>
                                        </td>
                                        <td class="text-center difference-${index + 1}">-</td>
                                        <td>
                                            <input type="text" name="detail_note[]" class="form-control" placeholder="Catatan...">
                                        </td>
                                    </tr>
                                `;
                                $('#itemsTable tbody').append(row);
                            });

                            initAutoNumeric();
                        }

                        $('#loadingProducts').hide();
                        $('#itemsTable').show();

                        Swal.fire({
                            icon: 'success',
                            title: 'Produk Berhasil Dimuat',
                            text: `${response.length} produk siap untuk dihitung`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        $('#loadingProducts').hide();
                        $('#itemsTable').show();

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat produk. Silakan coba lagi.'
                        });
                    }
                });
            });

            $(document).on('keyup change', '.qty-counted', function() {
                let row = $(this).data('row');
                calculateDifference(row);
            });

            $('#opnameForm').on('submit', function(e) {
                e.preventDefault();

                let hasItems = $('#itemsTable tbody tr:not(.empty-row)').length > 0;
                if (!hasItems) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum ada produk',
                        text: 'Load produk terlebih dahulu untuk melakukan opname.',
                        timer: 2000
                    });
                    return;
                }

                Swal.fire({
                    title: '{{ $isEdit ? "Update" : "Simpan" }} Opname?',
                    text: 'Data perhitungan stock akan disimpan sebagai draft.',
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

        function calculateDifference(row) {
            let systemStock = $(`.system-stock-${row}`).data('stock') || 0;
            let countedQty = getAutoNumericValue($(`.qty-counted[data-row="${row}"]`)[0]);
            let diff = countedQty - systemStock;

            let diffCell = $(`.difference-${row}`);
            if (diff > 0) {
                diffCell.html(`<span class="text-success fw-bold">+${formatNumber(diff)}</span>`);
            } else if (diff < 0) {
                diffCell.html(`<span class="text-danger fw-bold">${formatNumber(diff)}</span>`);
            } else {
                diffCell.html(`<span class="text-muted">0</span>`);
            }
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
