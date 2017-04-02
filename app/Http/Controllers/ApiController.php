<?php

namespace App\Http\Controllers;

use App\Models\Item;

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
}