<?php

namespace App\Console\Commands;

use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use App\Models\UserPayment;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Space;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserExtra;
use App\Scopes\IsVisibleScope;
use Illuminate\Support\Facades\DB;

class TransferStorageSpace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:transfer-storage-space';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
/*        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Space::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();*/

        $users = User::customer()->where('usr_id', '>', 14107)->get();
        $packages = Package::withoutGlobalScope(IsVisibleScope::class)->get();
        $packageSizes = [];

        foreach ($packages as $package) {
            $packageSizes[$package->package_id] = (int) get_package_units($package, Package::FEATURE_STORAGE);
        }

        foreach ($users as $user) {
            DB::transaction(function () use ($user, $packageSizes) {
                $last = UserAccounting::package()
                    ->where('usr_id', '=', $user->id)
                    ->where('status', '=', true)
                    ->orderByDesc('date_start')
                    ->first();

                $space = new Space();
                $space->type = Space::TYPE_REGULAR;
                $space->user_id = $user->usr_id;
                $space->is_available = true;

                if ($last) {
                    $packageId = $last->activity_characteristic;

                    if ($user->package_id != $last->activity_characteristic) {
                        \Log::debug("User {$user->usr_id}: Package ID `{$user->package_id}` is not matching last booking `{$last->activity_characteristic}`");
                        $packageId = $user->package_id;
                    }
                    $space->space = $space->space_available = $packageSizes[$packageId];
                } else {
                    $amount = 0;
                    if (!$user->isInTrial()) {
                        $amount = change_prefix($user->package->monthly_cost * $user->package->paying_rhythm);
                    }
                    $uam = new UserAccountingManager();
                    $last = $uam->add($user, Activity::PACKAGE, $user->package_id, $amount,
                        null, UserPayment::CURRENCY_DEFAULT, $user->date_trialend);

                    $this->line("User {$user->usr_id} ({$user->email}) was not charged, yet.");
                    //$space->created_at = $user->date_trialend ?? $this->getDate($user->date_created);
                    $space->space = $space->space_available = $packageSizes[$user->package_id];
                }
                $space->user_accounting_id = $last->accounting_id;
                $space->created_at = $last->date_start;
                $space->save();

                $extras = UserAccounting::extras()
                    ->where('usr_id', '=', $user->usr_id)
                    ->where('activity_characteristic', '=', 2)
                    ->whereDate('date_start', '>=', CarbonImmutable::now()->subDays(60))
                    ->get();

                foreach($extras as $ua) {
                    $amount = change_prefix($ua->amount);
                    $space = new Space();
                    $space->user_id = $user->id;
                    $space->user_accounting_id = $ua->accounting_id;
                    $space->type = Space::TYPE_EXTRA;
                    $space->created_at = $this->getDate($ua->date_start);
                    $space->space = UserExtra::DEFAULT_STORAGE * $amount;
                    $space->space_available = UserExtra::DEFAULT_STORAGE * $amount;
                    $space->is_available = true;
                    $space->save();
                }
            });
        }
    }

    private function getDate($time)
    {
        $dateStart = new \DateTime($time);
        $period = new \DatePeriod($dateStart, \DateInterval::createFromDateString('1 month'), new \DateTime());
        $dateNow = new \DateTime();
        $date = new \DateTime();

        foreach ($period as $dt) {
            if ($dt > $dateNow) {
                break;
            }
            $date = $dt;
        }

        return $date;
    }
}
