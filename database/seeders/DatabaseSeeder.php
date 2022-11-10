<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        //Seed the countries
        $this->call(CountriesSeeder::class);
        $this->command->info('Seeded the countries!');

        $this->call(PackageFeatureSeeder::class);
        $this->command->info('Seeded the package features!');

        $this->call(PackageSeeder::class);
        $this->command->info('Seeded the packages!');

        $this->call(PackageFeatureMappingSeeder::class);
        $this->command->info('Seeded the package feature mappings!');
    }
}
