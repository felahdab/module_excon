<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Weapon;


class WeaponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Weapon::firstOrCreate(
            ["name" => "MM 40"], 
            [
                "kind" => 2,
                "domain" => 6,
                "country" => 71,
                "category" => 1,
                "subcategory" => 1,
                "specific" => 4,
                "extra" => 0,
            ]);

        Weapon::firstOrCreate(
            ["name" => "MM 38"], 
            [
                "kind" => 2,
                "domain" => 6,
                "country" => 71,
                "category" => 1,
                "subcategory" => 1,
                "specific" => 1,
                "extra" => 0,
            ]);
    }
}
