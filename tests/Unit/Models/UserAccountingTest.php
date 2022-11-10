<?php
/**
 * User: fabio
 * Date: 13.07.20
 * Time: 14:48
 */

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\UserAccounting;
use Tests\TestCase;

class UserAccountingTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->seed('PackageSeeder');
    }

    public function test_user_accounting_can_be_created()
    {
        $this->withoutNotifications();

        $ua = UserAccounting::factory()->create();

        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $ua->usr_id,
            'activity_type' => $ua->activity_type,
            'activity_characteristic' => $ua->activity_characteristic,
            'activity_description' => $ua->activity_description,
            'amount' => $ua->amount,
            'currency' => $ua->currency,
/*            'date_created' => $ua->date_created,
            'date_start' => $ua->date_start,
            'date_end' => $ua->date_end,*/
            //'procedure' => $ua->procedure,
            //'status' => $ua->status,
        ]);
    }
}
