<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_detail';

    /**
     * Get the products that owns the product detail.
     */
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
