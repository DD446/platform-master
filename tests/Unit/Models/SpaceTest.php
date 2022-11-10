<?php
/**
 * User: fabio
 * Date: 04.07.20
 * Time: 00:23
 */

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Space;
use Tests\TestCase;

class SpaceTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->seed('PackageSeeder');
    }

    public function test_space_can_be_created()
    {
        $space = Space::factory()->create();

        $this->assertDatabaseHas('spaces', [
            'user_id' => $space->user_id,
            'user_accounting_id' => $space->user_accounting_id,
            'space' => $space->space,
            'space_available' => $space->space_available,
            'type' => $space->type,
            'is_available' => $space->is_available,
            'is_free' => $space->is_free,
        ]);
    }

    public function testCreateSpace()
    {
        $space = Space::create([
            'user_id' => 1,
            'user_accounting_id' => 1,
            'space' => 1,
            'space_available' => 1,
            'type' => Space::TYPE_REGULAR,
            'is_available' => true,
            'is_free' => true,
        ]);
        $this->assertTrue($space->exists);
    }

    public function testSaveSpace()
    {
        $space = new Space();
        $space->user_id = 1;
        $space->user_accounting_id = 1;
        $space->space = 1;
        $space->space_available = 1;
        $space->type = Space::TYPE_REGULAR;
        $space->is_available = true;
        $space->is_free = true;
        $res = $space->save();

        $this->assertTrue($res);
    }

    public function testDeleteSpace()
    {
        $space = new Space();
        $space->user_id = 1;
        $space->user_accounting_id = 1;
        $space->space = 1;
        $space->space_available = 1;
        $space->type = Space::TYPE_REGULAR;
        $space->is_available = true;
        $space->is_free = true;
        $space->save();
        $res = $space->delete();

        $this->assertTrue($res);
    }
}
