<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Weapon;
use Modules\Excon\Enums\WeaponTypes;


class WeaponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Weapon::firstOrCreate(
            ["name" => "MM 40 Exocet"], 
            [
                "type" => WeaponTypes::SURFACE_TO_SURFACE,
                "kind" => 2,
                "domain" => 6,
                "country" => 71,
                "category" => 1,
                "subcategory" => 1,
                "specific" => 4,
                "extra" => 0,
                "speed" => 318,
                "range" => 200
            ]);

        Weapon::firstOrCreate(
            ["name" => "MM 38 Exocet"], 
            [
                "type" => WeaponTypes::SURFACE_TO_SURFACE,
                "kind" => 2,
                "domain" => 6,
                "country" => 71,
                "category" => 1,
                "subcategory" => 1,
                "specific" => 1,
                "extra" => 0,
                "speed" => 318,
                "range" => 40
            ]);

        Weapon::firstOrCreate(
                ["name" => "SM 39 Exocet"], 
                [
                    "type" => WeaponTypes::SURFACE_TO_SURFACE,
                    "kind" => 2,
                    "domain" => 6,
                    "country" => 71,
                    "category" => 1,
                    "subcategory" => 1,
                    "specific" => 3,
                    "extra" => 0,
                    "speed" => 318,
                    "range" => 50
                ]);

        Weapon::firstOrCreate(
                ["name" => "RGM-84 Harpoon"], 
                [
                    "type" => WeaponTypes::SURFACE_TO_SURFACE,
                    "kind" => 2,
                    "domain" => 6,
                    "country" => 225,
                    "category" => 1,
                    "subcategory" => 1,
                    "specific" => 0,
                    "extra" => 0,
                    "speed" => 240,
                    "range" => 200
                ]);

        Weapon::firstOrCreate(
                    ["name" => "Otomat Mk 2"], 
                    [
                        "type" => WeaponTypes::SURFACE_TO_SURFACE,
                        "kind" => 2,
                        "domain" => 6,
                        "country" => 71,
                        "category" => 1,
                        "subcategory" => 8,
                        "specific" => 2,
                        "extra" => 0,
                        "speed" => 310,
                        "range" => 180
                    ]);
    }
}
