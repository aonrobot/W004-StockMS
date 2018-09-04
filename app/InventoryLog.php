<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventoryLog';
    protected $fillable = ['inventory_id', 'type', 'amount', 'remark', 'log_date', 'log_time'];

    /**
     * Get the phone record associated with the user.
     */
    public function inventory()
    {
        return $this->hasOne('App\Inventory');
    }
}
