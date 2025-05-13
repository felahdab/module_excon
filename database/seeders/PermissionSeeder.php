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
        Permission::firstOrCreate(
            [
            "name" => "excon::view_all_units_dashboard",
            "guard_name" => "web",
        ]);
        Permission::firstOrCreate(
            [
            "name" => "excon::view_all_sides_dashboard",
            "guard_name" => "web",
        ]);
        Permission::firstOrCreate(
            [
            "name" => "excon::report_engagement_for_own_unit",
            "guard_name" => "web",
        ]);
        Permission::firstOrCreate(
            [
            "name" => "excon::export_antares_reports",
            "guard_name" => "web",
        ]);

        
    }
}
