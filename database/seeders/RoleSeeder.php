<?php

namespace Modules\Excon\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Excon\Models\Identifier;

use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exdir_role = Role::firstOrCreate(
            [
            "name" => "excon::exdir",
            "guard_name" => "web",
        ]);

        $exdir_role->syncPermissions(["excon::view_all_units_dashboard", 
                                    "excon::view_all_sides_dashboard",
                                    "excon::record_snipe_report",
                                    "excon::load_weapons_into_units"]);

        $player_role = Role::firstOrCreate(
            [
            "name" => "excon::participant",
            "guard_name" => "web",
        ]);

        $player_role->syncPermissions(["excon::report_snipe_for_own_unit"]);
        
    }
}
