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
        "identifier_id",
        "latitude",
        "longitude",
        "timestamp",
        "data"
    ];

    protected $casts = [
        "data" => "json",
        "timestamp" => "datetime"
    ];

    // protected static function newFactory(): PositionFactory
    // {
    //     // return PositionFactory::new();
    // }

    public function identifier()
    {
        return $this->belongsTo(Identifier::class);
    }

    public function getUnitAttribute()
    {
        return $this->identifier->unit;
    }

}
