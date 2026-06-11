<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductOutlet;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $salesByOutlet = Sale::selectRaw('outlet_id, COUNT(*) as transactions, COALESCE(SUM(grand_total), 0) as revenue')
            ->whereBetween('sale_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->groupBy('outlet_id')
            ->get()
            ->keyBy('outlet_id');

        $dashboardStats = [
            'active_outlets' => Outlet::where('is_active', 1)->count(),
            'products' => Product::where('is_active', 1)->count(),
            'today_transactions' => Sale::whereDate('sale_date', today())->count(),
            'today_revenue' => Sale::whereDate('sale_date', today())->sum('grand_total'),
            'low_stock' => ProductOutlet::where('stock', '>', 0)
                ->whereColumn('stock', '<=', 'reorder_point')
                ->count(),
        ];

        $lowStockByOutlet = ProductOutlet::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'reorder_point')
            ->selectRaw('outlet_id, COUNT(*) as total')
            ->groupBy('outlet_id')
            ->pluck('total', 'outlet_id');

        $outlets = Outlet::withSum('productOutlets as total_stock', 'stock')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($outlet) use ($lowStockByOutlet, $salesByOutlet) {
                $coordinate = $this->resolveCoordinate($outlet);
                $sales = $salesByOutlet->get($outlet->id);

                return [
                    'id' => $outlet->id,
                    'code' => $outlet->code,
                    'name' => $outlet->name,
                    'address' => $outlet->address ?: '-',
                    'stock' => (int) ($outlet->total_stock ?? 0),
                    'low_stock_count' => (int) ($lowStockByOutlet[$outlet->id] ?? 0),
                    'sales_revenue' => (float) ($sales?->revenue ?? 0),
                    'sales_transactions' => (int) ($sales?->transactions ?? 0),
                    'lat' => $coordinate['lat'],
                    'lng' => $coordinate['lng'],
                ];
            })
            ->values();

        $salesChart = [
            'labels' => $outlets->pluck('name')->all(),
            'revenue' => $outlets->pluck('sales_revenue')->all(),
            'transactions' => $outlets->pluck('sales_transactions')->all(),
        ];

        return view('dashboard', compact('dashboardStats', 'outlets', 'salesChart'));
    }

    private function resolveCoordinate(Outlet $outlet): array
    {
        if ($outlet->latitude !== null && $outlet->longitude !== null) {
            return [
                'lat' => (float) $outlet->latitude,
                'lng' => (float) $outlet->longitude,
            ];
        }

        $cityCoordinates = [
            'JKT' => ['lat' => -6.2088, 'lng' => 106.8456],
            'JAKARTA' => ['lat' => -6.2088, 'lng' => 106.8456],
            'BDG' => ['lat' => -6.9175, 'lng' => 107.6191],
            'BANDUNG' => ['lat' => -6.9175, 'lng' => 107.6191],
            'SBY' => ['lat' => -7.2575, 'lng' => 112.7521],
            'SURABAYA' => ['lat' => -7.2575, 'lng' => 112.7521],
            'MDN' => ['lat' => 3.5952, 'lng' => 98.6722],
            'MEDAN' => ['lat' => 3.5952, 'lng' => 98.6722],
            'MKS' => ['lat' => -5.1477, 'lng' => 119.4327],
            'MAKASSAR' => ['lat' => -5.1477, 'lng' => 119.4327],
            'SMG' => ['lat' => -6.9667, 'lng' => 110.4167],
            'SEMARANG' => ['lat' => -6.9667, 'lng' => 110.4167],
            'DPS' => ['lat' => -8.6705, 'lng' => 115.2126],
            'DENPASAR' => ['lat' => -8.6705, 'lng' => 115.2126],
            'YGY' => ['lat' => -7.7956, 'lng' => 110.3695],
            'YOGYAKARTA' => ['lat' => -7.7956, 'lng' => 110.3695],
            'PLB' => ['lat' => -2.9761, 'lng' => 104.7754],
            'PALEMBANG' => ['lat' => -2.9761, 'lng' => 104.7754],
            'BPN' => ['lat' => -1.2379, 'lng' => 116.8529],
            'BALIKPAPAN' => ['lat' => -1.2379, 'lng' => 116.8529],
        ];

        $searchText = strtoupper($outlet->code . ' ' . $outlet->name . ' ' . $outlet->address);

        foreach ($cityCoordinates as $keyword => $coordinate) {
            if (str_contains($searchText, $keyword)) {
                return $coordinate;
            }
        }

        return ['lat' => -2.5489, 'lng' => 118.0149];
    }
}
