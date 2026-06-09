<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferDetail extends Model
{
    protected $guarded = ['id'];

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
