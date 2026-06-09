<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function productOutlets()
    {
        return $this->hasMany(ProductOutlet::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'product_outlets')
            ->withPivot('stock', 'reorder_point')
            ->withTimestamps();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get stock untuk outlet tertentu
     */
    public function getStockForOutlet($outletId)
    {
        return $this->productOutlets()
            ->where('outlet_id', $outletId)
            ->value('stock') ?? 0;
    }

    /**
     * Get total stock dari semua outlet
     */
    public function getTotalStock()
    {
        return $this->productOutlets()->sum('stock') ?? 0;
    }
}
