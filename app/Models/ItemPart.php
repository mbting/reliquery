<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPart extends Model
{
    protected $table = 'item_parts';
    public $timestamps = true;

    public function chances()
    {
        return $this->hasMany('App\Models\RelicReward', 'item_part_id', 'id');
    }
}