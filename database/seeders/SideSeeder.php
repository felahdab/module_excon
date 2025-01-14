<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Side;


class SideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Side::firstOrCreate(
            ["name" => "blue"], 
            [
            ]);

        Side::firstOrCreate(
            ["name" => "red"], 
            [
            ]);
    }
}
