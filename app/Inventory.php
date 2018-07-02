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

    /**
     * Get the phone record associated with the user.
     */
    public function product()
    {
        return $this->hasOne('App\Phone');
    }
}
