<?php

namespace App;

// use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
// use Nicolaslopezj\Searchable\SearchableTrait;

class Product extends Model
{
    // use SearchableTrait;
    // use Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = ['user_id', 'category_id', 'code', 'name', 'unitName'];

    // /**
    //  * Searchable rules.
    //  *
    //  * @var array
    //  */
    // protected $searchable = [
    //     'columns' => [
    //         'products.code' => 100
    //     ],
    //     'joins' => [
    //         'inventory' => ['products.product_id','inventory.product_id'],
    //     ],
    // ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    // public function toSearchableArray()
    // {
    //     $array = $this->toArray();

    //     // Customize array...

    //     return array_merge($array);
    // }

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
