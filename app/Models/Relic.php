<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relic extends Model
{
    protected $table = 'relics';
    public $timestamps = true;

    public function rewards()
    {
        return $this->hasMany('App\Models\RelicReward', 'relic_id', 'id');
    }

    public function sources()
    {
        return $this->belongsToMany('App\Models\Source', 'relics_sources');
    }
}