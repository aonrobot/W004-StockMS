<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $fillable = ['document_id', 'lineitem_id', 'type', 'status', 'source_wh_id', 'target_wh_id', 'balance'];
}
