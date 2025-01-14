<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Excon\Traits\HasTablePrefix;

// use Modules\Excon\Database\Factories\PositionFactory;

class Position extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "unit_id",
        "source",
        "latitude",
        "longitude",
        "data"
    ];

    protected $casts = [
        "data" => "array",
    ];

    // protected static function newFactory(): PositionFactory
    // {
    //     // return PositionFactory::new();
    // }
}
