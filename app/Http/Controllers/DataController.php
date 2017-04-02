<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPart;
use App\Models\Rotation;
use App\Models\Source;
use App\Models\Mission;
use App\Models\Relic;
use App\Models\RelicSource;
use App\Models\RelicReward;

class DataController extends Controller
{
    public function update()
    {
        $items = json_decode(file_get_contents(storage_path('app') . DIRECTORY_SEPARATOR . 'itemlist.json'));
        $table = json_decode(file_get_contents(storage_path('app') . DIRECTORY_SEPARATOR . 'relictable.json'));

        $relics = $table->relics;
        $sources = $table->sources;

        // insert items and parts
        foreach ($items as $item) {
            $name = $item->name;
            $parts = property_exists($item,'parts');
            $vaulted = (property_exists($item,'vaulted') ? ($item->vaulted == '1') ? 1 : 0 : 0);
            $i = Item::where('name', $name)->first();
            $i = ($i === null) ? new Item() : $i;
            $i->name = $name;
            $i->vaulted = $vaulted;
            $i->save();
            if ($parts) {
                foreach ($item->parts as $part) {
                    $p = ItemPart::where('name', $part->name)->first();
                    $p = ($p === null) ? new ItemPart() : $p;
                    $p->item_id = $i->id;
                    $p->name = $part->name;
                    $p->save();
                }
            } else {
                $p = ItemPart::where('name', $name)->first();
                $p = ($p === null) ? new ItemPart() : $p;
                $p->item_id = $i->id;
                $p->name = $name;
                $p->save();
            }
        }

        // insert sources and missions
        foreach ($sources as $source => $missions) {
            $s = Source::where('name', $source)->first();
            $s = ($s === null) ? new Source() : $s;
            $s->name = $source;
            $s->save();
            foreach ($missions as $mission) {
                $name = $mission->name;
                $planet = $mission->planet;
                $type = $mission->modeName;
                $faction = $mission->factionName;
                $m = Mission::where('name', $name)->first();
                $m = ($m === null) ? new Mission() : $m;
                $m->source_id = $s->id;
                $m->name = $name;
                $m->planet = $planet;
                $m->faction = $faction;
                $m->type = $type;
                $m->save();
            }
        }

        // insert relics and rewards with sources
        foreach ($relics as $relic) {
            $name = $relic->name;
            $type = explode(' ', $relic->name)[0];
            $vaulted = (property_exists($relic,'vaulted') ? ($relic->vaulted == '1') ? 1 : 0 : 0);
            $r = Relic::where('name', $relic->name)->first();
            $r = ($r === null) ? new Relic() : $r;
            $r->name = $name;
            $r->type = $type;
            $r->vaulted = $vaulted;
            $r->save();
            foreach ($relic->rewards as $reward) {
                $p = ItemPart::where('name', $reward->name)->first();
                $rr = RelicReward::where('relic_id', $r->id)
                                 ->where('item_part_id',$p->id)->first();
                $rr = ($rr === null) ? new RelicReward() : $rr;
                $rr->relic_id = $r->id;
                $rr->item_part_id = $p->id;
                $rr->exceptional = $reward->chance->exceptional;
                $rr->flawless = $reward->chance->flawless;
                $rr->intact = $reward->chance->intact;
                $rr->radiant = $reward->chance->radiant;
                $rr->save();
            }
            foreach ($relic->sources as $mission => $source) {
                $s = Source::where('name', $mission)->first();
                $rs = RelicSource::where('relic_id', $r->id)->where('source_id', $s->id)->first();
                $rs = ($rs === null) ? new RelicSource() : $rs;
                $rs->relic_id = $r->id;
                $rs->source_id = $s->id;
                $rs->save();
                foreach ($source->rotations as $name => $chance) {
                    $ro = Rotation::where('relic_source_id', $rs->id)->where('name', $name)->first();
                    $ro = ($ro === null) ? new Rotation() : $ro;
                    $ro->name = $name;
                    $ro->chance = $chance;
                    $ro->relic_source_id = $rs->id;
                    $ro->save();
                }
            }
        }
        return 'updated';
    }
}