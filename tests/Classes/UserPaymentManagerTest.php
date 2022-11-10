<?php
/**
 * User: fabio
 * Date: 18.08.20
 * Time: 15:59
 */

namespace Tests\Classes;

use App\Notifications\UserPaymentNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Classes\UserPaymentManager;
use App\Models\User;
use App\Models\UserPayment;
use Tests\TestCase;

class UserPaymentManagerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testAdd()
    {
        //$this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 1,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'is_trial' => User::IS_TRIAL,
                'is_blocked' => 0,
                'date_created' => now()->subDays(15),
                'date_trialend' => now()->addDays(15),
            ]
        );

        $this->assertDatabaseHas('usr', [
            'usr_id' => $user->user_id,
        ]);

        $this->expectsNotification($user, UserPaymentNotification::class);

        $upm = new UserPaymentManager();
        $up = $upm->add($user, $user->user_id, 15, UserPayment::CURRENCY_DEFAULT, UserPayment::PAYMENT_METHOD_LOCALBANK);

        $this->assertIsObject($up);
        $this->assertDatabaseHas('user_payments', [
            'payer_id' => $user->user_id,
            'receiver_id' => $user->user_id,
            'amount' => 15,
            'currency' => UserPayment::CURRENCY_DEFAULT,
        ]);
    }

    public function testGetBillId()
    {
        $upm = new UserPaymentManager();
        $billId = $upm->getBillId(1);

        $this->assertEquals(UserPayment::BILLING_PREFIX . date('Ymd') . 1, $billId);
    }
}
