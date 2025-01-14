<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
