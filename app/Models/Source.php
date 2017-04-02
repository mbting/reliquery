<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'sources';
    public $timestamps = true;

    public function missions()
    {
        return $this->hasMany('App\Models\Mission', 'source_id', 'id');
    }

    public function relics()
    {
        return $this->belongsToMany('App\Models\Relics', 'relics_sources');
    }
}