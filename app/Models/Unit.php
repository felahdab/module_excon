<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;

use Modules\Excon\Traits\HasTablePrefix;
use Modules\Excon\Models\User;
use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\Position;

use Modules\Excon\Services\PositionEstimationService;

// use Modules\Excon\Database\Factories\UnitFactory;

class Unit extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "name",
        "type",
        "side_id",
        "data",
    ];

    protected $casts = [
        "data" => "json"
    ];

    // protected static function newFactory(): UnitFactory
    // {
    //     // return UnitFactory::new();
    // }

    public function side()
    {
        return $this->belongsTo(Side::class);   
    }

    public function weapons()
    {
        return $this->belongsToMany(Weapon::class, "excon_unit_weapon")
            ->withPivot("amount", "timestamp", "data");
    }

    /**
     * Définit l'attribut ammunition_load qui liste toutes les armes ayant déjà été présentes sur l'unité, et le nombre de
     * munitions disponibles tous comptes faits.
     */
    public function getAmmunitionLoadAttribute()
    {
        return Cache::remember($this->cacheKey() . ".ammunition_load", 600, function()
        {
            return $this->calculateAmmunitionLoad();
        });
    }

    public function calculateAmmunitionLoad()
    {
        $ammunition_load = [];
        
        foreach ($this->weapons as $weapon)
        {
            $ammunition_load[$weapon->id] = array_key_exists($weapon->id, $ammunition_load) ? 
                $ammunition_load[$weapon->id] + $weapon->pivot->amount 
                : $weapon->pivot->amount;
        }

        foreach($this->engagements as $engagement)
        {
            $ammunition_load[$engagement->weapon_id] = array_key_exists($engagement->weapon_id, $ammunition_load) ? 
                $ammunition_load[$engagement->weapon_id] - $engagement->amount : - $engagement->amount;
        }
        return $ammunition_load;
    }

    /**
     * Définit l'attribut available_weapons qui liste toutes les armes pour lesquelles le nombre de munitions actuelles
     * est strictement positif.
     * Sous la forme d'une chaine de caractères: Nom de l'arme : nombre de munitions disponibles.
     * Example: Exocet MM 40 : 4
     */
    public function getAvailableWeaponsAttribute()
    {
        $ammunition_load = $this->ammunition_load;
        $available_weapons_list = Arr::where($ammunition_load, function($value, $key) {
            return $value > 0;
        });
        return Arr::map($available_weapons_list, function($value, $key)
        {
            return Weapon::find($key)->name . " : " . str($value);
        });
    }

    /**
     * Renvoie le nombre de munitions restantes pour le type d'arme indiquée
     */

    public function remaining_ammunitions(Weapon $weapon)
    {
        $id = $weapon->id;
        return Arr::get($this->ammunition_load, $id);
    }


    public function getWeaponsLoadsAttribute()
    {
        return Cache::remember($this->cacheKey() . ".weapons_loads", 600, function()
        {
            return $this->calculateWeaponsLoads();
        });
    }

    /**
     * Renvoie la liste des armes et le nombre de munitions restantes pour chaque arme
     */
    public function calculateWeaponsLoads()
    {
        $ammo_load = $this->ammunition_load;

        $schema = [];
        foreach($ammo_load as $weaponid => $amount){
            $weapon = Weapon::find($weaponid);
            $schema[]= (object) [
                "name" => $weapon->name,
                "amount" => $amount
            ];
        }
        return $schema;
    }

    public function getEngagementsHistoryAttribute()
    {
        return Cache::remember($this->cacheKey() . ".engagements_history", 600, function()
        {
            return $this->calculateEngagementsHistory();
        });
    }

    /**
     * Renvoie la liste des engagements de l'unité
     */
    public function calculateEngagementsHistory()
    {
        $engagements = Engagement::where('unit_id', $this->id)
            ->with('weapon')
            ->orderBy('timestamp', 'desc')
            ->get();

        $engs = [];
        
        foreach($engagements as $engagement)
        {
            $engs[] = (object) [
                "timestamp" => $engagement->timestamp,
                "weapon" => $engagement->weapon->name,
                "amount" => $engagement->amount,
                "target" => $engagement->target
            ];
        }

        return $engs;
    }

    public function engagements()
    {
        return $this->hasMany(Engagement::class);

    }

    public function positions(string | array | null $sources = [])
    {
        # Là, il faut coder le nécessaire pour
        # - trouver les identifiants de l'unité dans la table des identifiants
        # Prévoir de pouvoir restreindre les positions à certaines sources uniquement

        $identifiers = null;

        $sources = Arr::wrap($sources);
        if (empty($sources)){
            $identifiers = $this->identifiers()->get();
        }
        else {
            $identifiers = $this->identifiers()
                    ->whereIn("source", $sources)
                    ->get();
        }
        # - trouver toutes les positions reportées pour ces identifiants
        # - merger le tout dans une série chronologique

        $positions = Position::whereIn("identifier_id", $identifiers->pluck("id"))
            ->orderBy("timestamp", "asc");

        return $positions;
        
    }

    public function identifiers()
    {
        return $this->hasMany(Identifier::class);
    }

    public function extrapolatePositionForTimestamp(Carbon | null $timestamp = null)
    {
        $service = new PositionEstimationService;

        $positions = $this->positions()->get();

        [$latitude, $longitude ] = $service->extrapolatePositionForTimestamp( $positions, $timestamp);
        # Là, il faut trouver toutes les positions et notamment celle juste avant et celle juste après le timetamp.
        # puis extrapoler si nécessaire (écart entre les 2 positions supérieure à un seuil à définir)
        return [$latitude, $longitude ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "excon_user_units");
    }

    public function getWeaponsHistoryAttribute()
    {
        return Cache::remember($this->cacheKey() . ".weapons_history", 600, function()
        {
            return $this->calculateWeaponsHistory();
        });
    }
    /**
     * Renvoie la liste des mouvements d'armes de l'unité
     *
     * @return array
     */
    public function calculateWeaponsHistory()
    {
        $weapons_loads = $this->weapons->map(function ($item)
        {
            return (object) [
                "weapon" => $item->name,
                "amount" => $item->pivot->amount,
                "timestamp" => Carbon::parse($item->pivot->timestamp),
            ];
        });

        $weapons_consumptions = $this->engagements->map(function ($item)
        {
            return (object) [
                "weapon" => $item->weapon->name,
                "amount" => - $item->amount,
                "timestamp" => $item->timestamp,
            ];
        });

        $history = $weapons_loads->concat($weapons_consumptions);
        return $history->sortBy(function($value, $key) 
            { 
                $tt = Carbon::parse($value->timestamp);
                return $tt->getTimestamp();
            }
        );
    }

    public function getWeaponsHistoryForWidgetAttribute()
    {
        $weapons_history = $this->weapons_history;

        $timestamps = [];

        $last_timestamp = Carbon::now();
        if ($weapons_history->count())
        {
            $first_timestamp = (clone $weapons_history->pluck('timestamp')->first())->subDays(1);
            $timestamps = array_merge([$first_timestamp], $weapons_history->pluck('timestamp')->toArray(), [$last_timestamp]);
        }
        else{
            $timestamps = [$last_timestamp];
        }
        
        $datasets_names = $weapons_history->pluck('weapon')->unique()->toArray();

        $datasets = [];

        foreach($datasets_names as $name)
        { 
            $datasets[$name] = [0];
        }

        foreach($weapons_history as $weapon_mouvement)
        {
            foreach($datasets_names as $name)
            {
                if ($name == $weapon_mouvement->weapon)
                {
                    $datasets[$name][] = end($datasets[$name]) + $weapon_mouvement->amount;
                }
                else{
                    $datasets[$name][] = end($datasets[$name]);
                }
            }
        }

        foreach($datasets_names as $name)
        {
            $datasets[$name][] = end($datasets[$name]);
        }

        $datasets = array_values(Arr::map($datasets, function ($item, $key)
        {
            return [
                "label" => $key,
                "data" => $item
            ];
        }));

        $labels = Arr::map($timestamps, function ($item, $key){
            return $item->toString();
        });

        return [
            "datasets" => $datasets,
            "labels" => $labels,
        ];

    }

    public function getPositionIsValidAttribute(?Carbon $timestamp = null)
    {
        if ($timestamp == null)
        {
            $timestamp = Carbon::now();
        }
        
        $rightbefore = $timestamp->copy()->subSeconds(config("excon.limite_validite"));
        
        $position = $this->positions(["LDT, COT"])
            ->where('timestamp', '>=', $rightbefore)
            ->where('timestamp', '<=', $timestamp)
            ->first();

        return $position != null ;

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

    public function scopeOfType(Builder $query, string $type)
    {
        $query->where('type', $type);
    }

    public function scopeNotOfType(Builder $query, string $type)
    {
        $query->where('type', '<>', $type);
    }
}
