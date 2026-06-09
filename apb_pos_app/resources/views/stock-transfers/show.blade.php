@extends('layouts.app')

@section('title', 'Detail Stock Transfer')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Transfer</h4>
                        <small class="text-muted">{{ $transfer->transfer_no }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($transfer->status === 'draft')
                            <form action="{{ route('stock-transfers.approve', Crypt::encryptString($transfer->id)) }}"
                                method="POST" id="approveForm" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                            </form>
                            <a href="{{ route('stock-transfers.edit', Crypt::encryptString($transfer->id)) }}"
                                class="btn btn-warning">
                                <i class="ri-edit-line"></i> Edit
                            </a>
                        @elseif ($transfer->status === 'approved')
                            <form action="{{ route('stock-transfers.receive', Crypt::encryptString($transfer->id)) }}"
                                method="POST" id="receiveForm" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-double-line"></i> Terima
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('stock-transfers.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">No Transfer</th>
                                    <td><strong>{{ $transfer->transfer_no }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Transfer</th>
                                    <td>{{ date('d/m/Y', strtotime($transfer->transfer_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>Dari Outlet</th>
                                    <td>{{ $transfer->fromOutlet->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Ke Outlet</th>
                                    <td>{{ $transfer->toOutlet->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($transfer->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($transfer->status === 'approved')
                                            <span class="badge bg-warning text-dark">Approved</span>
                                        @elseif($transfer->status === 'received')
                                            <span class="badge bg-success">Received</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Dibuat Oleh</th>
                                    <td>{{ $transfer->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Dibuat</th>
                                    <td>{{ date('d/m/Y H:i', strtotime($transfer->created_at)) }}</td>
                                </tr>
                                @if ($transfer->status !== 'draft')
                                    <tr>
                                        <th>Diapprove Oleh</th>
                                        <td>{{ $transfer->approver->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Approve</th>
                                        <td>{{ date('d/m/Y H:i', strtotime($transfer->approved_at)) }}</td>
                                    </tr>
                                @endif
                                @if ($transfer->status === 'received')
                                    <tr>
                                        <th>Waktu Diterima</th>
                                        <td>{{ date('d/m/Y H:i', strtotime($transfer->received_at)) }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if ($transfer->note)
                        <div class="alert alert-info">
                            <strong>Catatan:</strong><br>
                            {{ $transfer->note }}
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
                                    <th width="15%" class="text-center">Qty Transfer</th>
                                    @if($transfer->status === 'received')
                                        <th width="15%" class="text-center">Qty Terima</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transfer->details as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $detail->product->sku }}</strong> - {{ $detail->product->name }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($detail->quantity, 0, ',', '.') }}
                                        </td>
                                        @if($transfer->status === 'received')
                                            <td class="text-center">
                                                {{ number_format($detail->quantity_received, 0, ',', '.') }}
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $transfer->status === 'received' ? 4 : 3 }}" class="text-center text-muted">
                                            Tidak ada item
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('approveForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approve Transfer?',
                text: 'Stock akan dikurangi dari outlet asal setelah diapprove.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Approve!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        document.getElementById('receiveForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Terima Transfer?',
                text: 'Stock akan ditambahkan ke outlet tujuan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
@endpush
