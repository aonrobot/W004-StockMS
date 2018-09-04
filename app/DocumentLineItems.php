<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentLineItems extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documentLineItems';
    protected $fillable = ['document_id', 'product_id', 'amount', 'price', 'discount', 'total'];

    public function DocumentDetail()
    {
        return $this->hasOne('App\DocumentDetail');
    }
}
