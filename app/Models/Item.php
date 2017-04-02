<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    public $timestamps = true;

    public function parts()
    {
        return $this->hasMany('App\Models\ItemPart', 'item_id', 'id');
    }
}