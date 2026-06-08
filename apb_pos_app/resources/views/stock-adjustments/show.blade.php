@extends('layouts.app')

@section('title', 'Detail Stock Adjustment')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Adjustment</h4>
                        <small class="text-muted">{{ $adjustment->adjustment_no }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($adjustment->status === 'draft')
                            <form action="{{ route('stock-adjustments.approve', Crypt::encryptString($adjustment->id)) }}"
                                method="POST" id="approveForm">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                            </form>
                            <a href="{{ route('stock-adjustments.edit', Crypt::encryptString($adjustment->id)) }}"
                                class="btn btn-warning">
                                <i class="ri-edit-line"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('stock-adjustments.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">No Adjustment</th>
                                    <td><strong>{{ $adjustment->adjustment_no }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Adjustment</th>
                                    <td>{{ date('d/m/Y', strtotime($adjustment->adjustment_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>Outlet</th>
                                    <td>{{ $adjustment->outlet->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($adjustment->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($adjustment->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Dibuat Oleh</th>
                                    <td>{{ $adjustment->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Dibuat</th>
                                    <td>{{ date('d/m/Y H:i', strtotime($adjustment->created_at)) }}</td>
                                </tr>
                                @if ($adjustment->status === 'approved')
                                    <tr>
                                        <th>Diapprove Oleh</th>
                                        <td>{{ $adjustment->approver->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Approve</th>
                                        <td>{{ date('d/m/Y H:i', strtotime($adjustment->approved_at)) }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if ($adjustment->note)
                        <div class="alert alert-info">
                            <strong>Catatan:</strong><br>
                            {{ $adjustment->note }}
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
                                    <th width="12%">Stock Fisik</th>
                                    <th width="12%">Selisih</th>
                                    <th width="20%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($adjustment->details as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $detail->product->name ?? '-' }}</strong>
                                            @if ($detail->product->sku)
                                                <br><small class="text-muted">SKU: {{ $detail->product->sku }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($detail->qty_system, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($detail->qty_physical, 0, ',', '.') }}</td>
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
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Total Item Disesuaikan</small>
                                    <h4 class="text-primary mb-0">{{ $adjustment->details->count() }} item</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Total Penambahan</small>
                                    <h4 class="text-success mb-0">+{{ number_format($adjustment->details->where('difference', '>', 0)->sum('difference'), 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Total Pengurangan</small>
                                    <h4 class="text-danger mb-0">{{ number_format($adjustment->details->where('difference', '<', 0)->sum('difference'), 0, ',', '.') }}</h4>
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
                title: 'Approve Adjustment?',
                text: 'Stock akan disesuaikan sesuai qty fisik. Proses ini tidak bisa dibatalkan.',
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
