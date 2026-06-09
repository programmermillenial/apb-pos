<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductOutlet;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Get or create product outlet record
     */
    private function getProductOutlet($productId, $outletId)
    {
        $productOutlet = ProductOutlet::where('product_id', $productId)
            ->where('outlet_id', $outletId)
            ->first();

        if (!$productOutlet) {
            $productOutlet = ProductOutlet::create([
                'product_id' => $productId,
                'outlet_id' => $outletId,
                'stock' => 0,
            ]);
        }

        return $productOutlet;
    }

    public function stockIn(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $productOutlet = $this->getProductOutlet($data['product_id'], $data['outlet_id']);

            $qty = (int) $data['qty'];

            $productOutlet->stock += $qty;
            $productOutlet->save();

            $balance = $productOutlet->stock;

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $productOutlet->product_id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'IN',
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => $qty,
                'qty_out' => 0,
                'balance' => $balance,
                'cost_price' => $data['cost_price'] ?? 0,
                'sell_price' => $data['sell_price'] ?? 0,
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });
    }

    public function stockOut(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $productOutlet = $this->getProductOutlet($data['product_id'], $data['outlet_id']);

            $qty = (int) $data['qty'];

            if ($productOutlet->stock < $qty) {
                $product = Product::findOrFail($data['product_id']);
                throw new \Exception("Stok produk {$product->name} di outlet tidak mencukupi. Stok tersedia: {$productOutlet->stock}");
            }

            $productOutlet->stock -= $qty;
            $productOutlet->save();

            $balance = $productOutlet->stock;

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $productOutlet->product_id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'OUT',
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => 0,
                'qty_out' => $qty,
                'balance' => $balance,
                'cost_price' => $data['cost_price'] ?? 0,
                'sell_price' => $data['sell_price'] ?? 0,
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });
    }

    public function adjustment(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $productOutlet = $this->getProductOutlet($data['product_id'], $data['outlet_id']);

            $oldStock = (int) $productOutlet->stock;
            $newStock = (int) $data['new_stock'];
            $difference = $newStock - $oldStock;

            $productOutlet->stock = $newStock;
            $productOutlet->save();

            $balance = $productOutlet->stock;

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $productOutlet->product_id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'ADJUSTMENT',
                'source_type' => $data['source_type'] ?? 'ADJUSTMENT',
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => $difference > 0 ? abs($difference) : 0,
                'qty_out' => $difference < 0 ? abs($difference) : 0,
                'balance' => $balance,
                'cost_price' => $data['cost_price'] ?? 0,
                'sell_price' => $data['sell_price'] ?? 0,
                'note' => $data['note'] ?? 'Stock adjustment',
                'created_by' => Auth::id(),
            ]);
        });
    }

    public function transfer(array $data): StockMovement
    {
        $productOutlet = $this->getProductOutlet($data['product_id'], $data['outlet_id']);

        $qty = (int) $data['quantity'];

        // Jika quantity negatif = OUT (kurangi dari outlet asal)
        if ($qty < 0) {
            $qty = abs($qty);

            if ($productOutlet->stock < $qty) {
                $product = Product::findOrFail($data['product_id']);
                throw new \Exception("Stok produk {$product->name} di outlet tidak mencukupi untuk transfer. Stok tersedia: {$productOutlet->stock}");
            }

            $productOutlet->stock -= $qty;
            $productOutlet->save();

            $balance = $productOutlet->stock;

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $productOutlet->product_id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'OUT',
                'source_type' => $data['source_type'] ?? 'STOCK_TRANSFER_OUT',
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => 0,
                'qty_out' => $qty,
                'balance' => $balance,
                'cost_price' => $data['cost_price'] ?? 0,
                'sell_price' => $data['sell_price'] ?? 0,
                'note' => $data['note'] ?? 'Stock transfer out',
                'created_by' => Auth::id(),
            ]);
        }
        // Jika quantity positif = IN (tambah ke outlet tujuan)
        else {
            $productOutlet->stock += $qty;
            $productOutlet->save();

            $balance = $productOutlet->stock;

            return StockMovement::create([
                'outlet_id' => $data['outlet_id'],
                'product_id' => $productOutlet->product_id,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'type' => 'IN',
                'source_type' => $data['source_type'] ?? 'STOCK_TRANSFER_IN',
                'source_id' => $data['source_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'qty_in' => $qty,
                'qty_out' => 0,
                'balance' => $balance,
                'cost_price' => $data['cost_price'] ?? 0,
                'sell_price' => $data['sell_price'] ?? 0,
                'note' => $data['note'] ?? 'Stock transfer in',
                'created_by' => Auth::id(),
            ]);
        }
    }
}
