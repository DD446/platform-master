<?php
/**
 * User: fabio
 * Date: 25.08.20
 * Time: 20:14
 */

namespace Tests\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AmazonController;
use Tests\TestCase;

class AmazonControllerTest extends TestCase
{
    const AMAZON_UPDATE_ROUTE = '/api/amazon/get-updated-shows';

    public function testFetchFailWithoutParameters()
    {
        $response = $this->get(self::AMAZON_UPDATE_ROUTE);
        $response->assertStatus(422);
    }

    public function testFetchWithCorrectParameters()
    {
        $response = $this->json('GET', self::AMAZON_UPDATE_ROUTE,
            [
                'token' => AmazonController::AMAZON_ACCESS_TOKEN,
                'last' => now()->timestamp,
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'count' => 0,
                'shows' => [],
            ]);
    }
}
