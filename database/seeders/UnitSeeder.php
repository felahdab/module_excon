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

        foreach (["Dixmude", "Tonnerre", "Cristobal Colon", "Galicia", "Argus", "Lyme Bay", "Bretagne", "Auvergne", "Van Amstel",
        "Aconit", "Thetis", "Sapeur", "Somme", "Sous-marin"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $blue_side->id,
                ]);
    
        }
        foreach (["De Ruyter", "Bartolomeu Dias", "Rhon", "Normandie", "Giovannidelle Bande Nere"] as $unitname){
            Unit::firstOrCreate(
                ["name" => $unitname], 
                [
                    "side_id" => $red_side->id,
                ]);
    
        }
    }
}
