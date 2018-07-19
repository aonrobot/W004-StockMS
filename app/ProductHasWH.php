<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductHasWH extends Model
{
    protected $table = 'product_has_warehouse';
    protected $fillable = ['product_id', 'warehouse_id'];
    public $timestamps = false;
}
