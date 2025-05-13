<?php

namespace Modules\Excon\Models;

use Exception;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\User as Authenticatable;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasMany;

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
        "data",
        "entity_number"
    ];

    protected $casts = [
        "data" => "json",
        "timestamp" => "datetime"
    ];

    // protected static function newFactory(): TirFactory
    // {
    //     // return TirFactory::new();
    // }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function weapon(): BelongsTo
    {
        return $this->belongsTo(Weapon::class);
    }

    public function scopeForCurrentUser(Builder $query)
    {
        if (! auth()->check())
            return $query;

        return $query->whereNotIn('id', Engagement::whereJsonContains('data->acknowleged_by', auth()->user()->uuid)->pluck('id'));
    }

    public function scopeOfType(Builder $query, string $type)
    {
        $weapon_list = Weapon::where("type", $type)->get()->pluck('id');
        return $query->whereIn("weapon_id", $weapon_list);
    }

    public function getTargetAttribute()
    {
        if (Arr::get($this->data, "engagement_type")=="absolute_position"){
            return "Position: " . Arr::get($this->data,"target_latitude") . "/" . Arr::get($this->data,'target_longitude');
        }
        if (Arr::get($this->data, "engagement_type")=="track_number"){
            return "Track number: " . Arr::get($this->data, "track_number");
        }
    }

    public function acknowlegeForUser(Authenticatable $user)
    {
        /**
         * $user can either be a regular user, or a remote system within the Skeletor context
         */

         $data = $this->data;
         $acknowledged = Arr::get($data, "acknowleged_by", []);
         if ( ! in_array($user->uuid, $acknowledged))
         {
            $acknowledged[] = $user->uuid;
         }

         Arr::set($data, "acknowleged_by", $acknowledged);
         $this->data = $data;

         $this->save();

    }

    public function getIsValidAttribute()
    {
        try{
            $description = $this->description_for_dis();
        }
        catch (Exception $e) 
            {
                return false;
            }

        return true;
    }

    public function description_for_dis()
    {
        return Cache::remember($this->cacheKey(), 600, function () {
            return $this->calculate_description_for_dis();
        });
    }

    public function calculate_description_for_dis()
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
            $track_number = Arr::get($this->data, "track_number");
            $identifier = Identifier::where('identifier', $track_number)->first();
            [$target_latitude, $target_longitude] = $identifier->extrapolatePositionForTimestamp($timestamp);

            $course = \GeometryLibrary\SphericalUtil::computeHeading(
                ['lat' => $latitude, 'lng' => $longitude],
                ['lat' => $target_latitude, 'lng' => $target_longitude]);

            $course = \GeometryLibrary\MathUtil::wrap($course, 0, 360);

            $distance = \GeometryLibrary\SphericalUtil::computeDistanceBetween(
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

            $course = \GeometryLibrary\MathUtil::wrap($course, 0, 360);

            $distance = \GeometryLibrary\SphericalUtil::computeDistanceBetween(
                    ['lat' => $latitude, 'lng' => $longitude],
                    ['lat' => $target_latitude, 'lng' => $target_longitude]);
        }


        return [
            "id" => $this->id,
            "current_time" => now()->timestamp,
            "timestamp" => $timestamp->timestamp, // Le timestamp en secondes
            "weapon_flight_time" => $weapon->flight_time,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "target_latitude" => $target_latitude,
            "target_longitude" => $target_longitude,
            "AN" => $unit->id,
            "EN" => $this->entity_number,
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
            "maxrange" => floatval($weapon->maxrange),
            "course" => $course,
            "distance" => $distance
        ];
    }

    public function cacheKey()
    {
        return sprintf(
            "%s/%s-%s",
            $this->getTable(),
            $this->getKey(),
            $this->updated_at->timestamp
        );
    }
}
