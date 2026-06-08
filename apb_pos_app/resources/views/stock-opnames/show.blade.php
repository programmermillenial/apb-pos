@extends('layouts.app')

@section('title', 'Detail Stock Opname')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Opname</h4>
                        <small class="text-muted">{{ $opname->opname_no }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($opname->status === 'draft')
                            <form action="{{ route('stock-opnames.approve', Crypt::encryptString($opname->id)) }}"
                                method="POST" id="approveForm">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-line"></i> Approve & Generate Adjustment
                                </button>
                            </form>
                            <a href="{{ route('stock-opnames.edit', Crypt::encryptString($opname->id)) }}"
                                class="btn btn-warning">
                                <i class="ri-edit-line"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('stock-opnames.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">No Opname</th>
                                    <td><strong>{{ $opname->opname_no }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Opname</th>
                                    <td>{{ date('d/m/Y', strtotime($opname->opname_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>Outlet</th>
                                    <td>{{ $opname->outlet->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Opname</th>
                                    <td>
                                        @if ($opname->type === 'full')
                                            <span class="badge bg-primary">Full Opname</span>
                                        @else
                                            <span class="badge bg-info">Partial Opname</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($opname->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($opname->status === 'in_progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @elseif($opname->status === 'review')
                                            <span class="badge bg-warning">Review</span>
                                        @elseif($opname->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($opname->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>PIC</th>
                                    <td>{{ $opname->pic_name }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Dibuat Oleh</th>
                                    <td>{{ $opname->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Dibuat</th>
                                    <td>{{ date('d/m/Y H:i', strtotime($opname->created_at)) }}</td>
                                </tr>
                                @if ($opname->status === 'approved')
                                    <tr>
                                        <th>Diapprove Oleh</th>
                                        <td>{{ $opname->approver->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Approve</th>
                                        <td>{{ date('d/m/Y H:i', strtotime($opname->approved_at)) }}</td>
                                    </tr>
                                    @if($opname->stock_adjustment_id)
                                        <tr>
                                            <th>Stock Adjustment</th>
                                            <td>
                                                <a href="{{ route('stock-adjustments.show', Crypt::encryptString($opname->stock_adjustment_id)) }}" 
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ri-file-text-line"></i> Lihat Adjustment
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            </table>
                        </div>
                    </div>

                    @if ($opname->note)
                        <div class="alert alert-info">
                            <strong>Catatan:</strong><br>
                            {{ $opname->note }}
                        </div>
                    @endif

                    <hr>

                    <h5 class="mb-3">Detail Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-transaction align-middle">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Produk</th>
                                    <th width="12%">Stock Sistem</th>
                                    <th width="12%">Qty Hitung</th>
                                    <th width="12%">Selisih</th>
                                    <th width="20%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($opname->details as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $detail->product->name ?? '-' }}</strong>
                                            @if ($detail->product->sku)
                                                <br><small class="text-muted">SKU: {{ $detail->product->sku }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($detail->qty_system, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($detail->qty_counted, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if ($detail->difference > 0)
                                                <span class="badge bg-success">+{{ number_format($detail->difference, 0, ',', '.') }}</span>
                                            @elseif($detail->difference < 0)
                                                <span class="badge bg-danger">{{ number_format($detail->difference, 0, ',', '.') }}</span>
                                            @else
                                                <span class="badge bg-secondary">0</span>
                                            @endif
                                        </td>
                                        <td>{{ $detail->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada item</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Total Item Dihitung</small>
                                    <h4 class="text-primary mb-0">{{ $opname->details->count() }} item</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Item Sesuai</small>
                                    <h4 class="text-secondary mb-0">{{ $opname->details->where('difference', 0)->count() }} item</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Total Penambahan</small>
                                    <h4 class="text-success mb-0">+{{ number_format($opname->details->where('difference', '>', 0)->sum('difference'), 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Total Pengurangan</small>
                                    <h4 class="text-danger mb-0">{{ number_format($opname->details->where('difference', '<', 0)->sum('difference'), 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#approveForm').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Approve Stock Opname?',
                html: 'Proses ini akan:<br>1. Membuat dokumen Stock Adjustment<br>2. Menyesuaikan stock sesuai hasil perhitungan<br><br><strong>Proses ini tidak bisa dibatalkan.</strong>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
@endpush
