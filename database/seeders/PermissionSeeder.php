<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Identifier;

use App\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(
            [
            "name" => "excon::affect_users",
            "guard_name" => "web",
        ]);
    }
}
