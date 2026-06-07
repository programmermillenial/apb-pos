<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceiptDetails()
    {
        return $this->hasMany(GoodsReceiptDetail::class, 'purchase_order_detail_id');
    }

    public function getReceivedQtyAttribute()
    {
        return $this->goodsReceiptDetails()->sum('received_qty');
    }

    public function getRemainingQtyAttribute()
    {
        return max(0, $this->qty - $this->received_qty);
    }
}
