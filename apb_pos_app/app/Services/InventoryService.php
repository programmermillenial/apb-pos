<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function stockIn(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $qty = (int) $data['qty'];

            $product->stock += $qty;
            $product->save();

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $product->id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'IN',
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => $qty,
                'qty_out' => 0,
                'balance' => $product->stock,
                'cost_price' => $data['cost_price'] ?? $product->cost_price ?? 0,
                'sell_price' => $data['sell_price'] ?? $product->sell_price ?? 0,
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });
    }

    public function stockOut(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $qty = (int) $data['qty'];

            if ($product->stock < $qty) {
                throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
            }

            $product->stock -= $qty;
            $product->save();

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $product->id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'OUT',
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => 0,
                'qty_out' => $qty,
                'balance' => $product->stock,
                'cost_price' => $data['cost_price'] ?? $product->cost_price ?? 0,
                'sell_price' => $data['sell_price'] ?? $product->sell_price ?? 0,
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });
    }

    public function adjustment(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $oldStock = (int) $product->stock;
            $newStock = (int) $data['new_stock'];
            $difference = $newStock - $oldStock;

            $product->stock = $newStock;
            $product->save();

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $product->id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'ADJUSTMENT',
                'source_type' => 'ADJUSTMENT',
                'source_id' => null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => $difference > 0 ? abs($difference) : 0,
                'qty_out' => $difference < 0 ? abs($difference) : 0,
                'balance' => $product->stock,
                'cost_price' => $data['cost_price'] ?? $product->cost_price ?? 0,
                'sell_price' => $data['sell_price'] ?? $product->sell_price ?? 0,
                'note' => $data['note'] ?? 'Stock adjustment',
                'created_by' => Auth::id(),
            ]);
        });
    }
}
