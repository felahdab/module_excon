<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Side;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blue_side = Side::where("name","blue")->first();
        $red_side = Side::where("name","red")->first();

        Unit::firstOrCreate(
            ["name" => "Warship 1"], 
            [
                "side_id" => $blue_side->id,
            ]);

        Unit::firstOrCreate(
            ["name" => "Warship 2"], 
            [
                "side_id" => $red_side->id,
            ]);
    }
}
