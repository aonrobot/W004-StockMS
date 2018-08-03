<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory';
    protected $fillable = ['product_id', 'warehouse_id', 'quantity', 'minLevel', 'maxLevel', 'costPrice', 'salePrice'];

    /**
     * 
     */
    public function product()
    {
        return $this->hasOne('App\Product');
    }

    public function inventoryLog()
    {
        return $this->hasMany('App\InventoryLog');
    }
}
