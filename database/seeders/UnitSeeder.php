<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Side;
use Modules\Excon\Enums\UnitTypes;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blue_side = Side::where("name","blue")->first();
        $red_side = Side::where("name","red")->first();

        foreach (["Dixmude", "Tonnerre", "Cristobal Colon", "Galicia", "Argus", "Lyme Bay", "Bretagne", "Auvergne", "Van Amstel",
        "Aconit", "Thetis", "Sapeur", "Somme", "San Georgio"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $blue_side->id,
                    "type" => UnitTypes::SURFACE_SHIP->value
                ]);
        }
        foreach (["FR submarine"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $blue_side->id,
                    "type" => UnitTypes::SUBMARINE->value
                ]);
        }
        foreach (["CTF 471"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $blue_side->id,
                    "type" => UnitTypes::STAFF->value
                ]);
        }
        foreach (["De Ruyter", "Bartolomeu Dias", "Rhon", "Normandie", "Giovannidelle Bande Nere"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $red_side->id,
                    "type" => UnitTypes::SURFACE_SHIP->value
                ]);
        }
        foreach (["SNMG1 Staff"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $red_side->id,
                    "type" => UnitTypes::STAFF->value
                ]);
        }
    }
}
