<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductOutlet;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockMovement;
use App\Models\StockOpnameDetail;
use App\Models\StockTransferDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return redirect()->route('reports.sales');
    }

    public function sales(Request $request)
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $outlets = $this->availableOutlets();
        $sales = $this->salesQuery($request, $startDate, $endDate)
            ->with(['outlet', 'customer', 'creator'])
            ->latest('sale_date')
            ->latest('id')
            ->get();

        $summary = [
            'transactions' => $sales->count(),
            'subtotal' => $sales->sum('subtotal'),
            'discount' => $sales->sum('discount_amount'),
            'tax' => $sales->sum('tax_amount'),
            'grand_total' => $sales->sum('grand_total'),
        ];

        $paymentSummary = $sales
            ->groupBy('payment_method')
            ->map(fn($items) => [
                'count' => $items->count(),
                'total' => $items->sum('grand_total'),
            ])
            ->sortKeys();

        return view('reports.sales', compact(
            'sales',
            'summary',
            'paymentSummary',
            'outlets',
            'startDate',
            'endDate'
        ));
    }

    public function profit(Request $request)
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $outlets = $this->availableOutlets();
        $details = $this->profitQuery($request, $startDate, $endDate)
            ->orderByDesc('sales.sale_date')
            ->orderByDesc('sales.id')
            ->get();

        $summary = [
            'qty' => $details->sum('qty'),
            'revenue' => $details->sum('revenue'),
            'cost' => $details->sum('cost_total'),
            'profit' => $details->sum('gross_profit'),
        ];

        $summary['margin'] = $summary['revenue'] > 0
            ? ($summary['profit'] / $summary['revenue']) * 100
            : 0;

        $productSummary = $details
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();
                $revenue = $items->sum('revenue');
                $profit = $items->sum('gross_profit');

                return [
                    'sku' => $first->sku,
                    'product_name' => $first->product_name,
                    'qty' => $items->sum('qty'),
                    'revenue' => $revenue,
                    'cost' => $items->sum('cost_total'),
                    'profit' => $profit,
                    'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                ];
            })
            ->sortByDesc('profit')
            ->values();

        return view('reports.profit', compact(
            'details',
            'summary',
            'productSummary',
            'outlets',
            'startDate',
            'endDate'
        ));
    }

    public function salesCsv(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $sales = $this->salesQuery($request, $startDate, $endDate)
            ->with(['outlet', 'customer', 'creator'])
            ->latest('sale_date')
            ->latest('id')
            ->get();

        return $this->downloadCsv('sales-report-' . $startDate . '-' . $endDate . '.csv', [
            'Invoice',
            'Tanggal',
            'Outlet',
            'Customer',
            'Kasir',
            'Metode',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Grand Total',
            'Status',
        ], $sales->map(fn($sale) => [
            $sale->invoice_number,
            $sale->sale_date?->format('Y-m-d'),
            $sale->outlet->name ?? '-',
            $sale->customer->name ?? 'Umum',
            $sale->creator->name ?? '-',
            strtoupper($sale->payment_method),
            $sale->subtotal,
            $sale->discount_amount,
            $sale->tax_amount,
            $sale->grand_total,
            ucfirst($sale->status),
        ]));
    }

    public function profitCsv(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $details = $this->profitQuery($request, $startDate, $endDate)
            ->orderByDesc('sales.sale_date')
            ->orderByDesc('sales.id')
            ->get();

        return $this->downloadCsv('profit-report-' . $startDate . '-' . $endDate . '.csv', [
            'Invoice',
            'Tanggal',
            'Outlet',
            'SKU',
            'Produk',
            'Qty',
            'Harga Jual',
            'HPP',
            'Revenue',
            'Cost',
            'Profit',
            'Margin %',
        ], $details->map(fn($detail) => [
            $detail->invoice_number,
            $detail->sale_date,
            $detail->outlet_name,
            $detail->sku,
            $detail->product_name,
            $detail->qty,
            $detail->sell_price,
            $detail->cost_price,
            $detail->revenue,
            $detail->cost_total,
            $detail->gross_profit,
            $detail->revenue > 0 ? round(($detail->gross_profit / $detail->revenue) * 100, 2) : 0,
        ]));
    }

    public function stock(Request $request)
    {
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'stock_status' => 'nullable|in:all,available,low,empty',
        ]);

        $outlets = $this->availableOutlets();
        $stockStatus = $request->input('stock_status', 'all');
        $stocks = $this->stockQuery($request, $stockStatus)
            ->orderBy('outlets.name')
            ->orderBy('products.name')
            ->get();

        $summary = [
            'products' => $stocks->count(),
            'total_stock' => $stocks->sum('stock'),
            'stock_value_cost' => $stocks->sum('stock_value_cost'),
            'stock_value_sell' => $stocks->sum('stock_value_sell'),
            'low_stock' => $stocks->filter(fn($item) => $item->stock > 0 && $item->stock <= $item->reorder_point)->count(),
            'empty_stock' => $stocks->where('stock', '<=', 0)->count(),
        ];

        return view('reports.stock', compact('stocks', 'summary', 'outlets', 'stockStatus'));
    }

    public function stockCsv(Request $request): StreamedResponse
    {
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'stock_status' => 'nullable|in:all,available,low,empty',
        ]);

        $stockStatus = $request->input('stock_status', 'all');
        $stocks = $this->stockQuery($request, $stockStatus)
            ->orderBy('outlets.name')
            ->orderBy('products.name')
            ->get();

        return $this->downloadCsv('stock-report-' . now()->format('Ymd-His') . '.csv', [
            'Outlet',
            'SKU',
            'Produk',
            'Kategori',
            'Brand',
            'Unit',
            'Stock',
            'Reorder Point',
            'HPP',
            'Harga Jual',
            'Nilai HPP',
            'Nilai Jual',
            'Status',
        ], $stocks->map(fn($stock) => [
            $stock->outlet_name,
            $stock->sku,
            $stock->product_name,
            $stock->category_name,
            $stock->brand_name,
            $stock->unit_name,
            $stock->stock,
            $stock->reorder_point,
            $stock->cost_price,
            $stock->sell_price,
            $stock->stock_value_cost,
            $stock->stock_value_sell,
            $this->stockStatusLabel((int) $stock->stock, (int) $stock->reorder_point),
        ]));
    }

    public function stockMovement(Request $request)
    {
        [$startDate, $endDate] = $this->dateRange($request);

        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'type' => 'nullable|in:IN,OUT,ADJUSTMENT',
            'source_type' => 'nullable|string|max:50',
        ]);

        $outlets = $this->availableOutlets();
        $movements = $this->stockMovementQuery($request, $startDate, $endDate)
            ->with(['outlet', 'product', 'user'])
            ->latest('movement_date')
            ->latest('id')
            ->get();

        $summary = [
            'movements' => $movements->count(),
            'qty_in' => $movements->sum('qty_in'),
            'qty_out' => $movements->sum('qty_out'),
            'net_qty' => $movements->sum('qty_in') - $movements->sum('qty_out'),
        ];

        $sourceTypes = StockMovement::query()
            ->select('source_type')
            ->whereNotNull('source_type')
            ->distinct()
            ->orderBy('source_type')
            ->pluck('source_type');

        return view('reports.stock-movement', compact(
            'movements',
            'summary',
            'outlets',
            'sourceTypes',
            'startDate',
            'endDate'
        ));
    }

    public function stockMovementCsv(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->dateRange($request);

        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'type' => 'nullable|in:IN,OUT,ADJUSTMENT',
            'source_type' => 'nullable|string|max:50',
        ]);

        $movements = $this->stockMovementQuery($request, $startDate, $endDate)
            ->with(['outlet', 'product', 'user'])
            ->latest('movement_date')
            ->latest('id')
            ->get();

        return $this->downloadCsv('stock-movement-report-' . $startDate . '-' . $endDate . '.csv', [
            'Tanggal',
            'Outlet',
            'SKU',
            'Produk',
            'Tipe',
            'Source',
            'Reference',
            'Qty In',
            'Qty Out',
            'Balance',
            'HPP',
            'Harga Jual',
            'User',
            'Note',
        ], $movements->map(fn($movement) => [
            $movement->movement_date?->format('Y-m-d'),
            $movement->outlet->name ?? '-',
            $movement->product->sku ?? '-',
            $movement->product->name ?? '-',
            $movement->type,
            $movement->source_type,
            $movement->reference_no,
            $movement->qty_in,
            $movement->qty_out,
            $movement->balance,
            $movement->cost_price,
            $movement->sell_price,
            $movement->user->name ?? '-',
            $movement->note,
        ]));
    }

    public function lowStock(Request $request)
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->stockQuery($request, 'low')
            ->orderBy('outlets.name')
            ->orderBy('products.name')
            ->get()
            ->map(fn($row) => $this->stockRow($row));

        return $this->stockTableView($request, 'Low Stock / Reorder Report', 'Produk yang sudah menyentuh reorder point', $rows, [
            ['key' => 'outlet_name', 'label' => 'Outlet'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'category_name', 'label' => 'Kategori'],
            ['key' => 'stock', 'label' => 'Stok', 'align' => 'end'],
            ['key' => 'reorder_point', 'label' => 'ROP', 'align' => 'end'],
            ['key' => 'shortage_qty', 'label' => 'Saran Beli', 'align' => 'end'],
            ['key' => 'stock_value_cost_fmt', 'label' => 'Nilai HPP', 'align' => 'end'],
        ], 'reports.low-stock.csv');
    }

    public function lowStockCsv(Request $request): StreamedResponse
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->stockQuery($request, 'low')->get();

        return $this->downloadCsv('low-stock-report-' . now()->format('Ymd-His') . '.csv', [
            'Outlet', 'SKU', 'Produk', 'Kategori', 'Stock', 'Reorder Point', 'Saran Beli', 'Nilai HPP',
        ], $rows->map(fn($row) => [
            $row->outlet_name,
            $row->sku,
            $row->product_name,
            $row->category_name,
            $row->stock,
            $row->reorder_point,
            max((int) $row->reorder_point - (int) $row->stock, 0),
            $row->stock_value_cost,
        ]));
    }

    public function slowMoving(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'days' => 'nullable|integer|min:1|max:3650',
        ]);

        $rows = $this->movingStockRows($request, $days)
            ->filter(fn($row) => $row->stock > 0 && ((int) $row->sold_qty === 0 || !$row->last_sale_date || $row->days_since_last_sale >= $days))
            ->values()
            ->map(fn($row) => [
                'outlet_name' => $row->outlet_name,
                'sku' => $row->sku,
                'product_name' => $row->product_name,
                'stock' => number_format($row->stock, 0, ',', '.'),
                'sold_qty' => number_format($row->sold_qty, 0, ',', '.'),
                'last_sale_date' => $row->last_sale_date ? Carbon::parse($row->last_sale_date)->format('d/m/Y') : 'Belum pernah',
                'days_since_last_sale' => $row->days_since_last_sale ?? '-',
                'stock_value_cost_fmt' => $this->rupiah($row->stock_value_cost),
            ]);

        return $this->stockTableView($request, 'Slow Moving / Dead Stock Report', 'Produk berstok yang tidak bergerak dalam periode tertentu', $rows, [
            ['key' => 'outlet_name', 'label' => 'Outlet'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'stock', 'label' => 'Stok', 'align' => 'end'],
            ['key' => 'sold_qty', 'label' => 'Terjual', 'align' => 'end'],
            ['key' => 'last_sale_date', 'label' => 'Last Sale'],
            ['key' => 'days_since_last_sale', 'label' => 'Hari', 'align' => 'end'],
            ['key' => 'stock_value_cost_fmt', 'label' => 'Nilai HPP', 'align' => 'end'],
        ], 'reports.slow-moving.csv', ['days' => $days]);
    }

    public function slowMovingCsv(Request $request): StreamedResponse
    {
        $days = (int) $request->input('days', 30);
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'days' => 'nullable|integer|min:1|max:3650',
        ]);

        $rows = $this->movingStockRows($request, $days)
            ->filter(fn($row) => $row->stock > 0 && ((int) $row->sold_qty === 0 || !$row->last_sale_date || $row->days_since_last_sale >= $days))
            ->values();

        return $this->downloadCsv('slow-moving-report-' . $days . '-days.csv', [
            'Outlet', 'SKU', 'Produk', 'Stock', 'Terjual', 'Last Sale', 'Hari Sejak Sale', 'Nilai HPP',
        ], $rows->map(fn($row) => [
            $row->outlet_name,
            $row->sku,
            $row->product_name,
            $row->stock,
            $row->sold_qty,
            $row->last_sale_date,
            $row->days_since_last_sale,
            $row->stock_value_cost,
        ]));
    }

    public function stockAging(Request $request)
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->agingRows($request)->map(fn($row) => [
            'outlet_name' => $row->outlet_name,
            'sku' => $row->sku,
            'product_name' => $row->product_name,
            'stock' => number_format($row->stock, 0, ',', '.'),
            'last_in_date' => $row->last_in_date ? Carbon::parse($row->last_in_date)->format('d/m/Y') : '-',
            'age_days' => $row->age_days,
            'stock_value_cost_fmt' => $this->rupiah($row->stock_value_cost),
        ]);

        return $this->stockTableView($request, 'Stock Aging Report', 'Umur stok dari tanggal barang terakhir masuk', $rows, [
            ['key' => 'outlet_name', 'label' => 'Outlet'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'stock', 'label' => 'Stok', 'align' => 'end'],
            ['key' => 'last_in_date', 'label' => 'Last In'],
            ['key' => 'age_days', 'label' => 'Umur Hari', 'align' => 'end'],
            ['key' => 'stock_value_cost_fmt', 'label' => 'Nilai HPP', 'align' => 'end'],
        ], 'reports.stock-aging.csv');
    }

    public function stockAgingCsv(Request $request): StreamedResponse
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);
        $rows = $this->agingRows($request);

        return $this->downloadCsv('stock-aging-report-' . now()->format('Ymd-His') . '.csv', [
            'Outlet', 'SKU', 'Produk', 'Stock', 'Last In', 'Umur Hari', 'Nilai HPP',
        ], $rows->map(fn($row) => [
            $row->outlet_name,
            $row->sku,
            $row->product_name,
            $row->stock,
            $row->last_in_date,
            $row->age_days,
            $row->stock_value_cost,
        ]));
    }

    public function stockValuation(Request $request)
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->stockQuery($request, 'all')
            ->orderBy('outlets.name')
            ->orderBy('products.name')
            ->get()
            ->map(fn($row) => [
                'outlet_name' => $row->outlet_name,
                'sku' => $row->sku,
                'product_name' => $row->product_name,
                'category_name' => $row->category_name ?? '-',
                'stock' => number_format($row->stock, 0, ',', '.'),
                'cost_price_fmt' => $this->rupiah($row->cost_price),
                'sell_price_fmt' => $this->rupiah($row->sell_price),
                'stock_value_cost_fmt' => $this->rupiah($row->stock_value_cost),
                'stock_value_sell_fmt' => $this->rupiah($row->stock_value_sell),
                'potential_margin_fmt' => $this->rupiah($row->stock_value_sell - $row->stock_value_cost),
            ]);

        return $this->stockTableView($request, 'Stock Valuation Report', 'Nilai persediaan berdasarkan HPP dan harga jual', $rows, [
            ['key' => 'outlet_name', 'label' => 'Outlet'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'category_name', 'label' => 'Kategori'],
            ['key' => 'stock', 'label' => 'Stok', 'align' => 'end'],
            ['key' => 'stock_value_cost_fmt', 'label' => 'Nilai HPP', 'align' => 'end'],
            ['key' => 'stock_value_sell_fmt', 'label' => 'Nilai Jual', 'align' => 'end'],
            ['key' => 'potential_margin_fmt', 'label' => 'Potensi Margin', 'align' => 'end'],
        ], 'reports.stock-valuation.csv');
    }

    public function stockValuationCsv(Request $request): StreamedResponse
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);
        $rows = $this->stockQuery($request, 'all')->get();

        return $this->downloadCsv('stock-valuation-report-' . now()->format('Ymd-His') . '.csv', [
            'Outlet', 'SKU', 'Produk', 'Kategori', 'Stock', 'HPP', 'Harga Jual', 'Nilai HPP', 'Nilai Jual', 'Potensi Margin',
        ], $rows->map(fn($row) => [
            $row->outlet_name,
            $row->sku,
            $row->product_name,
            $row->category_name,
            $row->stock,
            $row->cost_price,
            $row->sell_price,
            $row->stock_value_cost,
            $row->stock_value_sell,
            $row->stock_value_sell - $row->stock_value_cost,
        ]));
    }

    public function stockDiscrepancy(Request $request)
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->discrepancyRows($request, $startDate, $endDate)
            ->map(fn($row) => [
                'opname_no' => $row->opname_no,
                'opname_date' => Carbon::parse($row->opname_date)->format('d/m/Y'),
                'outlet_name' => $row->outlet_name,
                'sku' => $row->sku,
                'product_name' => $row->product_name,
                'qty_system' => number_format($row->qty_system, 0, ',', '.'),
                'qty_counted' => number_format($row->qty_counted, 0, ',', '.'),
                'difference' => number_format($row->difference, 0, ',', '.'),
                'variance_value_fmt' => $this->rupiah($row->variance_value),
                'status' => ucfirst($row->status),
            ]);

        return $this->stockTableView($request, 'Stock Discrepancy Report', 'Selisih stok sistem vs fisik dari stock opname', $rows, [
            ['key' => 'opname_no', 'label' => 'Opname No'],
            ['key' => 'opname_date', 'label' => 'Tanggal'],
            ['key' => 'outlet_name', 'label' => 'Outlet'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'qty_system', 'label' => 'System', 'align' => 'end'],
            ['key' => 'qty_counted', 'label' => 'Fisik', 'align' => 'end'],
            ['key' => 'difference', 'label' => 'Selisih', 'align' => 'end'],
            ['key' => 'variance_value_fmt', 'label' => 'Nilai Selisih', 'align' => 'end'],
            ['key' => 'status', 'label' => 'Status'],
        ], 'reports.stock-discrepancy.csv', compact('startDate', 'endDate'));
    }

    public function stockDiscrepancyCsv(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);
        $rows = $this->discrepancyRows($request, $startDate, $endDate);

        return $this->downloadCsv('stock-discrepancy-report-' . $startDate . '-' . $endDate . '.csv', [
            'Opname No', 'Tanggal', 'Outlet', 'SKU', 'Produk', 'System', 'Fisik', 'Selisih', 'Nilai Selisih', 'Status',
        ], $rows->map(fn($row) => [
            $row->opname_no,
            $row->opname_date,
            $row->outlet_name,
            $row->sku,
            $row->product_name,
            $row->qty_system,
            $row->qty_counted,
            $row->difference,
            $row->variance_value,
            $row->status,
        ]));
    }

    public function transferPending(Request $request)
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->pendingTransferRows($request)->map(fn($row) => [
            'transfer_no' => $row->transfer_no,
            'transfer_date' => Carbon::parse($row->transfer_date)->format('d/m/Y'),
            'from_outlet_name' => $row->from_outlet_name,
            'to_outlet_name' => $row->to_outlet_name,
            'sku' => $row->sku,
            'product_name' => $row->product_name,
            'quantity' => number_format($row->quantity, 0, ',', '.'),
            'quantity_received' => number_format($row->quantity_received, 0, ',', '.'),
            'pending_qty' => number_format($row->pending_qty, 0, ',', '.'),
            'status' => ucfirst($row->status),
        ]);

        return $this->stockTableView($request, 'Transfer Pending Report', 'Transfer stok yang belum diterima penuh', $rows, [
            ['key' => 'transfer_no', 'label' => 'Transfer No'],
            ['key' => 'transfer_date', 'label' => 'Tanggal'],
            ['key' => 'from_outlet_name', 'label' => 'Dari'],
            ['key' => 'to_outlet_name', 'label' => 'Ke'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'quantity', 'label' => 'Qty', 'align' => 'end'],
            ['key' => 'quantity_received', 'label' => 'Received', 'align' => 'end'],
            ['key' => 'pending_qty', 'label' => 'Pending', 'align' => 'end'],
            ['key' => 'status', 'label' => 'Status'],
        ], 'reports.transfer-pending.csv');
    }

    public function transferPendingCsv(Request $request): StreamedResponse
    {
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);
        $rows = $this->pendingTransferRows($request);

        return $this->downloadCsv('transfer-pending-report-' . now()->format('Ymd-His') . '.csv', [
            'Transfer No', 'Tanggal', 'Dari', 'Ke', 'SKU', 'Produk', 'Qty', 'Received', 'Pending', 'Status',
        ], $rows->map(fn($row) => [
            $row->transfer_no,
            $row->transfer_date,
            $row->from_outlet_name,
            $row->to_outlet_name,
            $row->sku,
            $row->product_name,
            $row->quantity,
            $row->quantity_received,
            $row->pending_qty,
            $row->status,
        ]));
    }

    public function fastMoving(Request $request)
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);

        $rows = $this->fastMovingRows($request, $startDate, $endDate)
            ->map(fn($row) => [
                'outlet_name' => $row->outlet_name,
                'sku' => $row->sku,
                'product_name' => $row->product_name,
                'qty_sold' => number_format($row->qty_sold, 0, ',', '.'),
                'revenue_fmt' => $this->rupiah($row->revenue),
                'transaction_count' => number_format($row->transaction_count, 0, ',', '.'),
                'avg_daily_qty' => number_format($row->avg_daily_qty, 2, ',', '.'),
            ]);

        return $this->stockTableView($request, 'Fast Moving Product Report', 'Produk paling cepat keluar berdasarkan qty terjual', $rows, [
            ['key' => 'outlet_name', 'label' => 'Outlet'],
            ['key' => 'sku', 'label' => 'SKU'],
            ['key' => 'product_name', 'label' => 'Produk'],
            ['key' => 'qty_sold', 'label' => 'Qty Terjual', 'align' => 'end'],
            ['key' => 'revenue_fmt', 'label' => 'Revenue', 'align' => 'end'],
            ['key' => 'transaction_count', 'label' => 'Transaksi', 'align' => 'end'],
            ['key' => 'avg_daily_qty', 'label' => 'Avg/Hari', 'align' => 'end'],
        ], 'reports.fast-moving.csv', compact('startDate', 'endDate'));
    }

    public function fastMovingCsv(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $request->validate(['outlet_id' => 'nullable|exists:outlets,id']);
        $rows = $this->fastMovingRows($request, $startDate, $endDate);

        return $this->downloadCsv('fast-moving-report-' . $startDate . '-' . $endDate . '.csv', [
            'Outlet', 'SKU', 'Produk', 'Qty Terjual', 'Revenue', 'Transaksi', 'Avg Per Hari',
        ], $rows->map(fn($row) => [
            $row->outlet_name,
            $row->sku,
            $row->product_name,
            $row->qty_sold,
            $row->revenue,
            $row->transaction_count,
            $row->avg_daily_qty,
        ]));
    }

    public function stockCard(Request $request)
    {
        [$startDate, $endDate] = $this->dateRange($request);
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $outlets = $this->availableOutlets();
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        $movements = collect();
        $selectedProduct = null;
        $selectedOutlet = null;
        $summary = ['qty_in' => 0, 'qty_out' => 0, 'net_qty' => 0, 'ending_balance' => 0];

        if ($request->filled('product_id') && $request->filled('outlet_id')) {
            $selectedProduct = Product::find($request->product_id);
            $selectedOutlet = Outlet::find($request->outlet_id);
            $movements = $this->stockMovementQuery($request, $startDate, $endDate)
                ->where('product_id', $request->product_id)
                ->oldest('movement_date')
                ->oldest('id')
                ->get();

            $summary = [
                'qty_in' => $movements->sum('qty_in'),
                'qty_out' => $movements->sum('qty_out'),
                'net_qty' => $movements->sum('qty_in') - $movements->sum('qty_out'),
                'ending_balance' => $movements->last()?->balance ?? 0,
            ];
        }

        return view('reports.stock-card', compact(
            'outlets',
            'products',
            'movements',
            'selectedProduct',
            'selectedOutlet',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    private function salesQuery(Request $request, string $startDate, string $endDate)
    {
        $user = Auth::user();

        return Sale::query()
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->when($user?->outlet_id, fn($query) => $query->where('outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('outlet_id', $request->outlet_id))
            ->when($request->filled('payment_method'), fn($query) => $query->where('payment_method', $request->payment_method));
    }

    private function profitQuery(Request $request, string $startDate, string $endDate)
    {
        $user = Auth::user();

        return SaleDetail::query()
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', 'paid')
            ->when($user?->outlet_id, fn($query) => $query->where('sales.outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('sales.outlet_id', $request->outlet_id))
            ->select([
                'sale_details.product_id',
                'sales.invoice_number',
                'sales.sale_date',
                'outlets.name as outlet_name',
                'sale_details.sku',
                'sale_details.product_name',
                'sale_details.qty',
                'sale_details.price as sell_price',
                'products.cost_price',
                DB::raw('sale_details.subtotal as revenue'),
                DB::raw('(sale_details.qty * products.cost_price) as cost_total'),
                DB::raw('(sale_details.subtotal - (sale_details.qty * products.cost_price)) as gross_profit'),
            ]);
    }

    private function stockQuery(Request $request, string $stockStatus)
    {
        $user = Auth::user();

        return ProductOutlet::query()
            ->join('products', 'product_outlets.product_id', '=', 'products.id')
            ->join('outlets', 'product_outlets.outlet_id', '=', 'outlets.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->where('products.is_active', 1)
            ->when($user?->outlet_id, fn($query) => $query->where('product_outlets.outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('product_outlets.outlet_id', $request->outlet_id))
            ->when($stockStatus === 'available', fn($query) => $query->where('product_outlets.stock', '>', 0))
            ->when($stockStatus === 'low', fn($query) => $query
                ->where('product_outlets.stock', '>', 0)
                ->whereColumn('product_outlets.stock', '<=', 'product_outlets.reorder_point'))
            ->when($stockStatus === 'empty', fn($query) => $query->where('product_outlets.stock', '<=', 0))
            ->select([
                'product_outlets.stock',
                'product_outlets.reorder_point',
                'products.sku',
                'products.name as product_name',
                'products.cost_price',
                'products.sell_price',
                'outlets.name as outlet_name',
                'product_categories.name as category_name',
                'brands.name as brand_name',
                'units.name as unit_name',
                DB::raw('(product_outlets.stock * products.cost_price) as stock_value_cost'),
                DB::raw('(product_outlets.stock * products.sell_price) as stock_value_sell'),
            ]);
    }

    private function stockMovementQuery(Request $request, string $startDate, string $endDate)
    {
        $user = Auth::user();

        return StockMovement::query()
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->when($user?->outlet_id, fn($query) => $query->where('outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('outlet_id', $request->outlet_id))
            ->when($request->filled('type'), fn($query) => $query->where('type', $request->type))
            ->when($request->filled('source_type'), fn($query) => $query->where('source_type', $request->source_type));
    }

    private function movingStockRows(Request $request, int $days)
    {
        $user = Auth::user();
        $cutoffDate = now()->subDays($days)->toDateString();

        return ProductOutlet::query()
            ->join('products', 'product_outlets.product_id', '=', 'products.id')
            ->join('outlets', 'product_outlets.outlet_id', '=', 'outlets.id')
            ->leftJoin('stock_movements', function ($join) use ($cutoffDate) {
                $join->on('stock_movements.product_id', '=', 'product_outlets.product_id')
                    ->on('stock_movements.outlet_id', '=', 'product_outlets.outlet_id')
                    ->where('stock_movements.source_type', '=', 'SALES')
                    ->where('stock_movements.movement_date', '>=', $cutoffDate);
            })
            ->where('products.is_active', 1)
            ->when($user?->outlet_id, fn($query) => $query->where('product_outlets.outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('product_outlets.outlet_id', $request->outlet_id))
            ->groupBy([
                'product_outlets.product_id',
                'product_outlets.outlet_id',
                'product_outlets.stock',
                'products.sku',
                'products.name',
                'products.cost_price',
                'outlets.name',
            ])
            ->select([
                'product_outlets.stock',
                'products.sku',
                'products.name as product_name',
                'products.cost_price',
                'outlets.name as outlet_name',
                DB::raw('COALESCE(SUM(stock_movements.qty_out), 0) as sold_qty'),
                DB::raw('MAX(stock_movements.movement_date) as last_sale_date'),
                DB::raw('(product_outlets.stock * products.cost_price) as stock_value_cost'),
            ])
            ->orderBy('sold_qty')
            ->orderByDesc('product_outlets.stock')
            ->get()
            ->map(function ($row) {
                $row->days_since_last_sale = $row->last_sale_date
                    ? Carbon::parse($row->last_sale_date)->diffInDays(now())
                    : null;

                return $row;
            });
    }

    private function agingRows(Request $request)
    {
        $user = Auth::user();

        return ProductOutlet::query()
            ->join('products', 'product_outlets.product_id', '=', 'products.id')
            ->join('outlets', 'product_outlets.outlet_id', '=', 'outlets.id')
            ->leftJoin('stock_movements', function ($join) {
                $join->on('stock_movements.product_id', '=', 'product_outlets.product_id')
                    ->on('stock_movements.outlet_id', '=', 'product_outlets.outlet_id')
                    ->where('stock_movements.type', '=', 'IN');
            })
            ->where('products.is_active', 1)
            ->where('product_outlets.stock', '>', 0)
            ->when($user?->outlet_id, fn($query) => $query->where('product_outlets.outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('product_outlets.outlet_id', $request->outlet_id))
            ->groupBy([
                'product_outlets.product_id',
                'product_outlets.outlet_id',
                'product_outlets.stock',
                'product_outlets.created_at',
                'products.sku',
                'products.name',
                'products.cost_price',
                'outlets.name',
            ])
            ->select([
                'product_outlets.stock',
                'product_outlets.created_at',
                'products.sku',
                'products.name as product_name',
                'products.cost_price',
                'outlets.name as outlet_name',
                DB::raw('MAX(stock_movements.movement_date) as last_in_date'),
                DB::raw('(product_outlets.stock * products.cost_price) as stock_value_cost'),
            ])
            ->get()
            ->map(function ($row) {
                $baseDate = $row->last_in_date ?: $row->created_at;
                $row->age_days = $baseDate ? Carbon::parse($baseDate)->diffInDays(now()) : null;

                return $row;
            })
            ->sortByDesc('age_days')
            ->values();
    }

    private function discrepancyRows(Request $request, string $startDate, string $endDate)
    {
        $user = Auth::user();

        return StockOpnameDetail::query()
            ->join('stock_opnames', 'stock_opname_details.stock_opname_id', '=', 'stock_opnames.id')
            ->join('products', 'stock_opname_details.product_id', '=', 'products.id')
            ->join('outlets', 'stock_opnames.outlet_id', '=', 'outlets.id')
            ->whereBetween('stock_opnames.opname_date', [$startDate, $endDate])
            ->where('stock_opname_details.difference', '!=', 0)
            ->when($user?->outlet_id, fn($query) => $query->where('stock_opnames.outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('stock_opnames.outlet_id', $request->outlet_id))
            ->select([
                'stock_opnames.opname_no',
                'stock_opnames.opname_date',
                'stock_opnames.status',
                'outlets.name as outlet_name',
                'products.sku',
                'products.name as product_name',
                'products.cost_price',
                'stock_opname_details.qty_system',
                'stock_opname_details.qty_counted',
                'stock_opname_details.difference',
                DB::raw('(stock_opname_details.difference * products.cost_price) as variance_value'),
            ])
            ->orderByDesc('stock_opnames.opname_date')
            ->orderByDesc(DB::raw('ABS(stock_opname_details.difference)'))
            ->get();
    }

    private function pendingTransferRows(Request $request)
    {
        $user = Auth::user();

        return StockTransferDetail::query()
            ->join('stock_transfers', 'stock_transfer_details.stock_transfer_id', '=', 'stock_transfers.id')
            ->join('products', 'stock_transfer_details.product_id', '=', 'products.id')
            ->join('outlets as from_outlets', 'stock_transfers.from_outlet_id', '=', 'from_outlets.id')
            ->join('outlets as to_outlets', 'stock_transfers.to_outlet_id', '=', 'to_outlets.id')
            ->where(function ($query) {
                $query->where('stock_transfers.status', '!=', 'received')
                    ->orWhereColumn('stock_transfer_details.quantity_received', '<', 'stock_transfer_details.quantity');
            })
            ->when($user?->outlet_id, function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('stock_transfers.from_outlet_id', $user->outlet_id)
                        ->orWhere('stock_transfers.to_outlet_id', $user->outlet_id);
                });
            })
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('stock_transfers.from_outlet_id', $request->outlet_id)
                        ->orWhere('stock_transfers.to_outlet_id', $request->outlet_id);
                });
            })
            ->select([
                'stock_transfers.transfer_no',
                'stock_transfers.transfer_date',
                'stock_transfers.status',
                'from_outlets.name as from_outlet_name',
                'to_outlets.name as to_outlet_name',
                'products.sku',
                'products.name as product_name',
                'stock_transfer_details.quantity',
                'stock_transfer_details.quantity_received',
                DB::raw('(stock_transfer_details.quantity - stock_transfer_details.quantity_received) as pending_qty'),
            ])
            ->orderBy('stock_transfers.transfer_date')
            ->orderBy('stock_transfers.transfer_no')
            ->get();
    }

    private function fastMovingRows(Request $request, string $startDate, string $endDate)
    {
        $user = Auth::user();
        $days = max(Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1, 1);

        return SaleDetail::query()
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', 'paid')
            ->when($user?->outlet_id, fn($query) => $query->where('sales.outlet_id', $user->outlet_id))
            ->when(!$user?->outlet_id && $request->filled('outlet_id'), fn($query) => $query->where('sales.outlet_id', $request->outlet_id))
            ->groupBy([
                'sales.outlet_id',
                'outlets.name',
                'sale_details.product_id',
                'sale_details.sku',
                'sale_details.product_name',
            ])
            ->select([
                'outlets.name as outlet_name',
                'sale_details.sku',
                'sale_details.product_name',
                DB::raw('SUM(sale_details.qty) as qty_sold'),
                DB::raw('SUM(sale_details.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as transaction_count'),
                DB::raw('SUM(sale_details.qty) / ' . $days . ' as avg_daily_qty'),
            ])
            ->orderByDesc('qty_sold')
            ->limit(100)
            ->get();
    }

    private function stockTableView(Request $request, string $title, string $subtitle, $rows, array $columns, string $csvRoute, array $extra = [])
    {
        $outlets = $this->availableOutlets();
        $summary = [
            'rows' => $rows->count(),
        ];

        return view('reports.stock-table', array_merge(compact(
            'title',
            'subtitle',
            'rows',
            'columns',
            'csvRoute',
            'outlets',
            'summary'
        ), $extra));
    }

    private function stockRow($row): array
    {
        return [
            'outlet_name' => $row->outlet_name,
            'sku' => $row->sku,
            'product_name' => $row->product_name,
            'category_name' => $row->category_name ?? '-',
            'stock' => number_format($row->stock, 0, ',', '.'),
            'reorder_point' => number_format($row->reorder_point, 0, ',', '.'),
            'shortage_qty' => number_format(max((int) $row->reorder_point - (int) $row->stock, 0), 0, ',', '.'),
            'stock_value_cost_fmt' => $this->rupiah($row->stock_value_cost),
        ];
    }

    private function rupiah($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }

    private function stockStatusLabel(int $stock, int $reorderPoint): string
    {
        if ($stock <= 0) {
            return 'Empty';
        }

        if ($stock <= $reorderPoint) {
            return 'Low Stock';
        }

        return 'Available';
    }

    private function availableOutlets()
    {
        $user = Auth::user();

        return Outlet::where('is_active', 1)
            ->when($user?->outlet_id, fn($query) => $query->where('id', $user->outlet_id))
            ->orderBy('name')
            ->get();
    }

    private function dateRange(Request $request): array
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'outlet_id' => 'nullable|exists:outlets,id',
            'payment_method' => 'nullable|in:cash,transfer,qris,debit,credit',
        ]);

        return [
            $request->input('start_date', now()->startOfMonth()->toDateString()),
            $request->input('end_date', now()->toDateString()),
        ];
    }

    private function downloadCsv(string $filename, array $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
