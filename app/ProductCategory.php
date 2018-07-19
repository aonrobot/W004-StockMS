<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_category';
    protected $fillable = ['name', 'description'];

    /**
     * Get the products for the blog Category.
     */
    public function products()
    {
        return $this->hasMany('App\Product', 'product_id');
    }
}
