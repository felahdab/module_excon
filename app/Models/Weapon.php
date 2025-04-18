<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Excon\Traits\HasTablePrefix;

// use Modules\Excon\Database\Factories\WeaponFactory;

class Weapon extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "name",
        "kind",
        "country",
        "category",
        "subcategory",
        "extra",
        "specific",
        "speed",
        "maxrange",
        "data"

    ];

    protected $casts = [
        "data" => "json"
    ];

    // protected static function newFactory(): WeaponFactory
    // {
    //     // return WeaponFactory::new();
    // }
}
