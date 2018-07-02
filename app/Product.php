<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = ['category_id', 'code', 'name', 'unitName'];

    /**
     * Get the inventory record associated with the product.
     */
    public function inventory()
    {
        return $this->hasMany('App\Inventory', 'product_id');
    }

    /**
     * Get the detail for the blog product.
     */
    public function details()
    {
        return $this->hasMany('App\ProductDetail', 'product_id');
    }
    
    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo('App\ProductCategory', 'product_id');
    }

    /**
     * The warehouse that belong to the product.
     */
    public function warehouse()
    {
        return $this->belongsToMany('App\Warehouse', 'product_has_warehouse', 'product_id', 'warehouse_id');
    }
}
