<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Excon\Traits\HasTablePrefix;

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
        "side_id",
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

    public function positions()
    {
        # Là, il faut coder le nécessaire pour
        # - trouver les identifiants de l'unité dans la table des identifiants
        # - trouver toutes les positions reportées pour ces identifiants
        # - merger le tout dans une série chronologique
        # Prévoir de pouvoir restreindre les positions à certaines sources uniquement
        return [];
    }

    public function identifiers()
    {
        return $this->hasMany(Identifier::class);
    }

    public function extrapolatePositionForTimestamp($timestamp)
    {
        # Là, il faut trouver toutes les positions et notamment celle juste avant et celle juste après le timetamp.
        # puis extrapoler si nécessaire (écart entre les 2 positions supérieure à un seuil à définir)
        return [43.2, 005.0];
    }
}
