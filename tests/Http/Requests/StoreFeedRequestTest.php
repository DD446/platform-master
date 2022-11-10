<?php
/**
 * User: fabio
 * Date: 02.11.21
 * Time: 10:52
 */

namespace Tests\Http\Requests;

use Illuminate\Support\Str;
use Tests\TestCase;

class StoreFeedRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /*
     * @test
     * @dataProvider invalidFields
     */
    public function testFieldsRules()
    {
        $this
            ->postJson('/api/feeds', ['feed_id' => Str::random(101)])
            ->assertSessionHasErrors(['feed_id'])
            ->assertStatus(302);
    }

    public function invalidFields()
    {
        return [
            [
                ['feed_id' => Str::random(101)],
                ['feed_id']
            ]
        ];
    }
}
