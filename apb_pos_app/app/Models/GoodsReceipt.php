<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function getReceiptNoteAttribute()
    {
        return $this->notes;
    }
}
