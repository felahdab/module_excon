<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Excon\Traits\HasTablePrefix;

// use Modules\Excon\Database\Factories\IdentifierFactory;

class Identifier extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "unit_id",
        "source",
        "identifier",
        "data"
    ];

    protected $casts = [
        "data" => "array"
    ];

    // protected static function newFactory(): IdentifierFactory
    // {
    //     // return IdentifierFactory::new();
    // }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
