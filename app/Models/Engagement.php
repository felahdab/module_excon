<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

use Modules\Excon\Traits\HasTablePrefix;

// use Modules\Excon\Database\Factories\TirFactory;

class Engagement extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "timestamp",
        "unit_id",
        "amount",
        "weapon_id",
        "data"
    ];

    protected $casts = [
        "data" => "json",
        "timestamp" => "datetime"
    ];

    // protected static function newFactory(): TirFactory
    // {
    //     // return TirFactory::new();
    // }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function weapon()
    {
        return $this->belongsTo(Weapon::class);
    }

    public function target()
    {
        $data = $this->data;
        $engagement_type = Arr::get($data, "engagement_type");
        if ($engagement_type == "track_number")
        {
            $track_number = Arr::get($data, "track_number");
            # Là, il faut trouver la position de la piste désignée par ce track number, dans le référentiel du tireur (donc la SITAC
            # de son "side")
            # l'objectif étant ensuite d'en déduire un azimut de tir, nécessaire pour les missiles simulés
            $side = $this->unit->side;
            $sources = $side->sources;
            $position = Position::whereIn("identifier_id", 
                                        Identifier::query()
                                            ->whereIn("source", $sources)
                                            ->where("identifier", $track_number)
                                            ->get()
                                            ->pluck("id")
                                            )
                                        ->get();

        }
        elseif ($engagement_type == "absolute_position")
        {

        }

    }
}
