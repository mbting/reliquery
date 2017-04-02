<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RelicSource;

class ApiController extends Controller
{
    public function all()
    {
        $list = [];
        $items = Item::
            with('parts')
            ->with('parts.chances')
            ->get();

        foreach ($items as $i) {
            $item = [];
            $item['id'] = $i->id;
            $item['name'] = trim(str_replace('Prime', '', $i->name));
            $item['vaulted'] = ($i->vaulted == 0) ? false : true;
            foreach ($i->parts as $p) {
                $part['id'] = $p->id;
                $part['name'] = trim(str_replace($i->name, '', $p->name));
                $part['name'] = trim(str_replace(' Blueprint', '', $part['name']));
                $count = count($p->chances);
                $top = 0;
                $bottom = 0;
                foreach ($p->chances as $chance) {
                    $min = min($chance->exceptional, $chance->flawless, $chance->intact, $chance->radiant);
                    $max = max($chance->exceptional, $chance->flawless, $chance->intact, $chance->radiant);
                    $top += $max;
                    $bottom += $min;
                }
                $top = (int) ($top / $count);
                $bottom = (int) ($bottom / $count);

                $part['chances'] = $bottom . '% - ' . $top . '%';
                $item['parts'][] = $part;
            }
            $list[] = $item;
        }
        return $list;
    }

    public function prime($id)
    {
        $i = Item::with('parts.chances.relic.sources.missions')->find($id);
        if ($i === null) abort(404);
        $item = [];
        $item['id'] = $i->id;
        $item['name'] = $i->name;
        $item['vaulted'] = ($i->vaulted == 0) ? false : true;
        foreach ($i->parts as $p) {
            $part = [];
            $part['id'] = $p->id;
            $part['name'] = $p->name;
            $part['relics'] = [];
            foreach ($p->chances as $c) {
                $relic = [];
                $relic['id'] = $c->relic->id;
                $relic['name'] = $c->relic->name;
                $relic['type'] = $c->relic->type;
                $relic['vaulted'] = $c->relic->vaulted;
                $relic['intact'] = $c->intact;
                $relic['exceptional'] = $c->exceptional;
                $relic['flawless'] = $c->flawless;
                $relic['radiant'] = $c->radiant;
                $relic['sources'] = [];
                foreach ($c->relic->sources as $s) {
                    $source = [];
                    $source['id'] = $s->id;
                    $source['name'] = $s->name;
                    $source['rotations'] = [];
                    $ro = RelicSource::with('rotations')->where([
                                    ['relic_id', '=', $c->relic->id],
                                    ['source_id', '=', $s->id]])->first();
                    foreach ($ro->rotations as $r) {
                        $rotation = [];
                        $rotation['name'] = $r->name;
                        $rotation['chance'] = $r->chance;
                        $source['rotations'][] = $rotation;
                    }
                    $source['missions'] = [];
                    foreach ($s->missions as $m) {
                        $mission = [];
                        $mission['name'] = $m->name;
                        $mission['planet'] = $m->planet;
                        $mission['faction'] = $m->faction;
                        $mission['type'] = $m->type;
                        $source['missions'][] = $mission;
                    }
                    $relic['sources'][] = $source;
                }
                $part['relics'][] = $relic;
            }
            $item['parts'][] = $part;
        }
        return $item;
    }
}