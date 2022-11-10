<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('package')->delete();

        $packages = [
            [
                'name' => 'onhold',
                'monthly_cost' => '0',
                'paying_rhythm' => '1',
                'package_available' => '0',
                'is_hidden' => '1',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'private',
                'monthly_cost' => '1',
                'paying_rhythm' => '1',
                'package_available' => '1',
                'is_hidden' => '0',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'standard',
                'monthly_cost' => '5',
                'paying_rhythm' => '1',
                'package_available' => '1',
                'is_hidden' => '0',
                'is_default' => '1',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'professional',
                'monthly_cost' => '10',
                'paying_rhythm' => '1',
                'package_available' => '1',
                'is_hidden' => '0',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'maxi',
                'monthly_cost' => '20',
                'paying_rhythm' => '1',
                'package_available' => '1',
                'is_hidden' => '0',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'press',
                'monthly_cost' => '0',
                'paying_rhythm' => '12',
                'package_available' => '0',
                'is_hidden' => '1',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'corporate',
                'monthly_cost' => '100',
                'paying_rhythm' => '1',
                'package_available' => '1',
                'is_hidden' => '0',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
            [
                'name' => 'agency',
                'monthly_cost' => '500',
                'paying_rhythm' => '1',
                'package_available' => '1',
                'is_hidden' => '0',
                'is_default' => '0',
                'tld' => config('app.domain'),
            ],
        ];

        foreach ($packages as $packageId => $package) {
            DB::table('package')->insert([
                'package_id' => $packageId,
                'package_name' => $package['name'],
                'monthly_cost' => $package['monthly_cost'],
                'paying_rhythm' => $package['paying_rhythm'],
                'package_available' => $package['package_available'],
                'is_hidden' => $package['is_hidden'],
                'is_default' => $package['is_default'],
                'tld' => $package['tld'],
            ]);
        }
    }
}
