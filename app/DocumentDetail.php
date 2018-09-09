<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documentDetail';
    protected $fillable = ['user_id', 'number', 'customer_id', 'ref_id', 'source_wh_id', 'target_wh_id', 'type', 'tax_type', 'comment', 'status', 'date'];

    public function documentLineItems()
    {
        return $this->hasMany('App\DocumentLineItems', 'document_id');
    }
}
