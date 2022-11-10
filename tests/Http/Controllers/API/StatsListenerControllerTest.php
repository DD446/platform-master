<?php
/**
 * User: fabio
 * Date: 19.11.20
 * Time: 21:51
 */

namespace Tests\Http\Controllers\API;

use App\Http\Controllers\API\StatsListenerController;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StatsListenerControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $user = User::factory()->create(['package_id' => 4]);
        Passport::actingAs(
            $user,
            ['stats']
        );
        $response = $this->postJson('/api/stats/listeners', []);
        $response->assertStatus(200);
    }
}
