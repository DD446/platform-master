<?php

namespace App\Console\Commands;

use App\Models\UserAccounting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use App\Models\User;
use App\Models\UserPayment;
use Illuminate\Support\Facades\Log;

class ChargeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:charge-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Substracts funds from account when new payment period is due';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::debug("Scheduler: Charge users");

        $now = Carbon::now();
        $uam = new UserAccountingManager();
        $aUsers = $uam->getDueCustomers();
        $this->line("Found " . count($aUsers) . " due customers");

        foreach ($aUsers as $case) {
            try {
                $oUser = User::find($case->usr_id);
                $this->line("Processing user: " . $oUser->username);

                if (!$oUser) {
                    continue;
                }

                // Start with end of last booking
                $dateStart = $case->date_end;

                if ($oUser->new_package_id) {
                    DB::transaction(function () use ($oUser) {
                        $oUser->package_id = $oUser->new_package_id;
                        $oUser->new_package_id = null;
                        $oUser->save();
                    });
                    $oUser->refresh();
                }

                DB::transaction(function () use ($uam, $oUser, $dateStart) {
                    $packageId = $oUser->package_id;
                    // New accounting period for existing customer
                    $uam->add($oUser, Activity::PACKAGE, $packageId,
                        change_prefix($oUser->package->monthly_cost * $oUser->package->paying_rhythm),
                        null, UserPayment::CURRENCY_DEFAULT, $dateStart);
                });
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }

        $aUsers = User::whereIsTrial(User::IS_TRIAL)->where('date_trialend', '<', now())->get();
        $this->line("Found " . $aUsers->count() . " users where trial period ended.");

        foreach ($aUsers as $oUser) {
            $this->line("Processing user: " . $oUser->username);
            try {
                $ua = UserAccounting::package()->where('usr_id', '=', $oUser->id)->latest('accounting_id')->first();
                if (!$ua || $ua->date_end < $now) {

                    if ($oUser->new_package_id) {
                        DB::transaction(function () use ($oUser) {
                            $oUser->package_id = $oUser->new_package_id;
                            $oUser->new_package_id = null;
                            $oUser->save();
                        });
                        $oUser->refresh();
                    }

                    DB::transaction(function () use ($uam, $oUser) {
                        // This shouldn´t really happen
                        $uam->add($oUser, Activity::PACKAGE, $oUser->package_id,
                            change_prefix($oUser->package->monthly_cost * $oUser->package->paying_rhythm),
                            null, UserPayment::CURRENCY_DEFAULT, $oUser->date_trialend);
                    });
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
    }
}
