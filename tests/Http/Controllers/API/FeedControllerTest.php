<?php
/**
 * User: fabio
 * Date: 16.11.20
 * Time: 15:12
 */

namespace Tests\Http\Controllers\API;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FeedControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testIndex()
    {

    }
    public function testShow()
    {

    }

    public function testCopy()
    {

    }

    public function testStore()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        Passport::actingAs(
            $user,
            ['feeds']
        );
        $response = $this->postJson('/api/feeds', [
            'feed_id' => 'hier-fehlt-was',
            'title' => 'Hier fehlt was',
            'author' => 'Ich bin der Autor',
            'description' => 'Das ist meine Beschreibung.',
            'copyright' => 'Hier steht ein Copyright.',
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'username' => $user->username,
            ]);
    }

    public function testUpdate()
    {

    }

    public function testDestroyNonExistingPodcast()
    {
        $user = User::factory()->create(['package_id' => 4]);
        $response = $this->actingAs($user, 'api')->deleteJson('/api/feeds/test');
        $response->assertStatus(404);
    }

}
