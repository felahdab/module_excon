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

    public function description_for_dis()
    {
        $timestamp = $this->timestamp;
        $unit = $this->unit;
        [$latitude, $longitude] = $unit->extrapolatePositionForTimestamp($timestamp);

        $weapon = $this->weapon;
        
        $engagement_type = Arr::get($this->data, 'engagement_type');
        $course = 0;
        $target_latitude = 0;
        $target_longitude = 0;

        if ($engagement_type == 'track_number') 
        {
            /**
             * $track_number = Arr::get($this->data, "track_number")
             * $identifier = Identifier::where('identifier', $track_number)->first();
             * 
             */
            $track_number = Arr::get($this->data, "track_number");
            $identifier = Identifier::where('identifier', $track_number)->first();
            [$target_latitude, $target_longitude] = $identifier->extrapolatePositionForTimestamp($timestamp);

            $course = \GeometryLibrary\SphericalUtil::computeHeading(
                ['lat' => $latitude, 'lng' => $longitude],
                ['lat' => $target_latitude, 'lng' => $target_longitude]);

        }
        elseif($engagement_type == 'absolute_position')
        {
            $target_latitude = floatval(Arr::get($this->data, 'target_latitude'));
            $target_longitude = floatval(Arr::get($this->data, 'target_longitude'));

            $course = \GeometryLibrary\SphericalUtil::computeHeading(
                ['lat' => $latitude, 'lng' => $longitude],
                ['lat' => $target_latitude, 'lng' => $target_longitude]);
        }
        else {

        };


        return [
            "timestamp" => $timestamp,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "target_latitude" => $target_latitude,
            "target_longitude" => $target_longitude,
            "entity_type" => [
                "kind" => $weapon->kind,
                "domain" => $weapon->domain,
                "country" => $weapon->country,
                "category" => $weapon->category,
                "subcategory" => $weapon->subcategory,
                "specific" => $weapon->specific,
                "extra" => $weapon->extra,
            ],
            "speed" => floatval($weapon->speed),
            "course" => $course
        ];
    }
}
