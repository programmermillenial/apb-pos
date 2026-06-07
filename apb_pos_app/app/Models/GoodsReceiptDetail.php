<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptDetail extends Model
{
    protected $guarded = ['id'];

    function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }

    function product()
    {
        return $this->belongsTo(Product::class);
    }
}
