<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

use Modules\Excon\Traits\HasTablePrefix;

// use Modules\Excon\Database\Factories\SideFactory;

class Side extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "name", 
        "data"
    ];

    protected $casts = [
        "data" => "json",
    ];

    // protected static function newFactory(): SideFactory
    // {
    //     // return SideFactory::new();
    // }

    public function getSourcesAttribute()
    {
        $data = $this->data;
        $sources = Arr::get($data, "sources");
        if ($sources == null) return [];
        return Arr::flatten($sources);
    }

    public static function getSideForSource($source)
    {
        $ret= [];
        foreach(static::all() as $side)
        {
            if (in_array($source, $side->sources))
            {
                $ret[] = $side;
            }
        }
        return $ret;
    }
}
