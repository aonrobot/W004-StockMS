<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'warehouse';
    protected $primaryKey = 'warehouse_id';

    /**
     * The product that belong to the warehouse.
     */
    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_has_warehouse', 'product_id', 'warehouse_id');
    }
}
