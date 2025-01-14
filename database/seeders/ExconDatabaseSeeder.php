<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

class ExconDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            WeaponsSeeder::class,
            SideSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
