<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

use Modules\Excon\Traits\HasTablePrefix;
use Modules\Excon\Database\Factories\EntityNumberFactory;

// use Modules\Excon\Database\Factories\SideFactory;

class EntityNumber extends Model
{
    use HasFactory;
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "id"
    ];

    protected $casts = [
    ];

    protected static function newFactory(): EntityNumberFactory
    {
        return EntityNumberFactory::new();
    }

    public static function getNewEntityNumber()
    {
        $en = static::factory()->create();
        return $en->id;
    }

    public static function getSeveralEntityNumbers($count)
    {
        $entity_numbers = [];
        for ($i=0; $i<$count; $i++){
            $entity_numbers[] = static::getNewEntityNumber();
        }
        return $entity_numbers;
    }

}
