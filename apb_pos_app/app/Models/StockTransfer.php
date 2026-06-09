<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'transfer_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function fromOutlet()
    {
        return $this->belongsTo(Outlet::class, 'from_outlet_id');
    }

    public function toOutlet()
    {
        return $this->belongsTo(Outlet::class, 'to_outlet_id');
    }

    public function details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
