<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    public function run() {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            RangesTableSeeder::class,
            NumberOfPumpsTableSeeder::class,
            PowerRatingsTableSeeder::class,
            ApplicationsTableSeeder::class,
            IpRatingsTableSeeder::class,
            StarterTypesTableSeeder::class,
            VoltagesTableSeeder::class,
            StarterTypesTableSeeder::class,
            EnclousresTableSeeder::class,
            ComponentsTableSeeder::class,
            CommunicationProtocolsTableSeeder::class,
        ]);
    }

}
