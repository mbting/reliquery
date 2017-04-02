<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelicSource extends Model
{
    protected $table = 'relics_sources';
    public $timestamps = true;

    public function rotations()
    {
        return $this->hasMany('App\Models\Rotation', 'relic_source_id', 'id');
    }
}