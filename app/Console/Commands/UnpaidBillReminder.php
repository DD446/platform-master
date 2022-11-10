<?php

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\UserPayment;

class UnpaidBillReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:send-unpaid-bill-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        Log::debug("Scheduler: Unpaid bill reminder");

        $openPayments = UserPayment::with('payer')
            ->where('is_paid', '=', 0)
            ->where('payment_method', '=', UserPayment::PAYMENT_METHOD_BILL)
            ->whereDate('date_created', '<', CarbonImmutable::now()->subDays(10))
            ->get()
            ->groupBy('payer_id');

        $this->line("Found `" . $openPayments->count() . "` users with due bills.");
        $now = now();
        $counter = 1;

        foreach ($openPayments as $userId => $payments) {
            $user = User::with('userbillingcontact')->find($userId);

            if (!$user) {
                $this->error("User with ID: {$userId} not found.");
                foreach ($payments as $payment) {
                    $payment->update(['is_paid' => -1]);
                }
                continue;
            }

            $this->line("Processing user: `" . $user->username . "` with `" . $payments->count() . "` unpaid bills.");
            ++$counter;

            // Send an email to the billing contact email address
            // or if this address is not available use account email
            $email = $user->userbillingcontact->email ?? $user->email;

            if ($email) {
                foreach ($payments as $payment) {
                    if (!$payment->is_paid) {
                        if ($payment->state < 4) {
                            $this->line("User `".$user->username."` <{$email}>: Sending unpaid reminder mail from `".$payment->date_created->formatLocalized('%d.%m.%Y')."` in state `".$payment->state."`.");
                            $when = $now->addSeconds(15 * $counter);
                            Mail::to($email)->later($when, new \App\Mail\UnpaidBillReminder($payment));
                            ++$counter;
                        } else {
                            // TOOD: Inkasso
                            // TODO: Suspend account
                            $user->is_blocked = 1;
                            $user->save();
                        }
                    }
                }
            }
        }

        return 0;
    }
}
