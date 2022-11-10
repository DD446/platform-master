<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('package_feature')->delete();

        $packageFeatures = [
            ['package_feature_id' => '1','feature_name' => 'feeds'],
            ['package_feature_id' => '2','feature_name' => 'feeds_extra'],
            ['package_feature_id' => '3','feature_name' => 'storage'],
            ['package_feature_id' => '4','feature_name' => 'storage_extra'],
            ['package_feature_id' => '5','feature_name' => 'statistics'],
            ['package_feature_id' => '8','feature_name' => 'api'],
            ['package_feature_id' => '9','feature_name' => 'bill_online'],
            ['package_feature_id' => '10','feature_name' => 'bill_print'],
            ['package_feature_id' => '11','feature_name' => 'blogs'],
            ['package_feature_id' => '12','feature_name' => 'domains'],
            ['package_feature_id' => '13','feature_name' => 'subdomains'],
            ['package_feature_id' => '14','feature_name' => 'subdomains_premium'],
            ['package_feature_id' => '15','feature_name' => 'transcoding'],
            ['package_feature_id' => '16','feature_name' => 'protection'],
            ['package_feature_id' => '17','feature_name' => 'scheduler'],
            ['package_feature_id' => '18','feature_name' => 'auphonic'],
            ['package_feature_id' => '19','feature_name' => 'bandwidth'],
            ['package_feature_id' => '20','feature_name' => 'multiuser'],
            ['package_feature_id' => '21','feature_name' => 'support'],
            ['package_feature_id' => '22','feature_name' => 'protection_user'],
            ['package_feature_id' => '23','feature_name' => 'player'],
            ['package_feature_id' => '24','feature_name' => 'player_configuration'],
            ['package_feature_id' => '25','feature_name' => 'ads'],
            ['package_feature_id' => '26','feature_name' => 'player_customstyles'],
            ['package_feature_id' => '27','feature_name' => 'teams'],
            ['package_feature_id' => '28','feature_name' => 'members']
        ];

        foreach ($packageFeatures as $feature) {
            DB::table('package_feature')->insert([
                'package_feature_id' => $feature['package_feature_id'],
                'feature_name' => $feature['feature_name'],
            ]);
        }
    }
}
