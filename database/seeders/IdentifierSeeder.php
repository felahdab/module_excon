<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Identifier;


class IdentifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Identifier::create(
            [
            "unit_id" => 1,
            "source" => "COT",
            "identifier" => "COT_1"
        ]);
    }
}
