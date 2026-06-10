@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ $title }}</h4>
                        <small class="text-muted">{{ $subtitle }}</small>
                    </div>
                    <a href="{{ route($csvRoute, request()->query()) }}" class="btn btn-success">
                        <i class="ri-download-2-line"></i> Export CSV
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end mb-4">
                        @isset($startDate)
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                        @endisset

                        <div class="col-md-{{ isset($startDate) ? '3' : '5' }}">
                            <label class="form-label">Outlet</label>
                            <select name="outlet_id" class="form-select">
                                <option value="">Semua Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" @selected(request('outlet_id') == $outlet->id)>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @isset($days)
                            <div class="col-md-3">
                                <label class="form-label">Minimal Hari Tidak Bergerak</label>
                                <input type="number" name="days" class="form-control" min="1" value="{{ $days }}">
                            </div>
                        @endisset

                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-filter-3-line"></i> Filter
                            </button>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="bg-soft-primary rounded p-3 mb-3">
                                <small class="text-muted">Total Baris</small>
                                <h4 class="mb-0">{{ number_format($summary['rows'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    @foreach ($columns as $column)
                                        <th class="{{ ($column['align'] ?? '') === 'end' ? 'text-end' : '' }}">
                                            {{ $column['label'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $row)
                                    <tr>
                                        @foreach ($columns as $column)
                                            <td class="{{ ($column['align'] ?? '') === 'end' ? 'text-end' : '' }}">
                                                {{ $row[$column['key']] ?? '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) }}" class="text-center text-muted">
                                            Belum ada data.
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
