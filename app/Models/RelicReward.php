<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelicReward extends Model
{
    protected $table = 'relics_rewards';
    public $timestamps = true;

    public function relic() {
        return $this->belongsTo('App\Models\Relic', 'relic_id', 'id');
    }
}