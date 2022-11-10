<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Classes\UserAccountingManager;
use App\Models\Space;
use App\Models\UserExtra;
use Illuminate\Support\Facades\Log;

class ChargeUserExtra extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:charge-user-extra';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to charge regularly charge user extras';

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
        Log::debug("Scheduler: Charge user extras");

        $dateEnd = Carbon::now();
        $this->line("Processing entries before: " . $dateEnd);

        // Hole alle wiederholenden Extras, die neu abgerechnet werden müssen
        $aExtras = UserExtra::with('user')->whereHas('user', function ($query) { $query->where('is_acct_active', '=', 1); })
            ->where('is_repeating', '=', UserExtra::IS_REPEATING)
            ->where('date_end', '<', $dateEnd)
            ->orderBy('date_end')
            ->get();

        $uaManager = new UserAccountingManager();

        // Gehe durch alle offenen Extras durch
        foreach ($aExtras as $extra) {
            $this->line("Charging repeating extras `" . $extra->extras_description . "` for user: " . $extra->user->username);

            if ($extra->user->funds - $extra->extras_count < 0) {
                $this->line("Skipping to charge repeating extras `" . $extra->extras_description . "` for user: " . $extra->user->username . " because of missing funds.");
                continue;
            }

            // Begin transaction
            DB::transaction(function () use ($extra, $uaManager) {
                $uaManager->trackOrder($extra);

                $extra->user->funds -= $extra->extras_count;
                $extra->user->save();
                $extra->date_start = $extra->date_end;
                $extra->date_end = $extra->date_end->addDays(30);
                $extra->save();
            }, 3);
            // End transaction
            $this->line("Charged repeating extras for user: " . $extra->user->username);
        }
    }
}
