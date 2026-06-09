<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $guarded = ['id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_outlets')
            ->withPivot('stock', 'reorder_point')
            ->withTimestamps();
    }

    public function productOutlets()
    {
        return $this->hasMany(ProductOutlet::class);
    }
}
