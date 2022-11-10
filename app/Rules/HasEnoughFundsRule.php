<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use App\Models\Package;
use App\Models\UserAccounting;

class HasEnoughFundsRule implements Rule
{
    /**
     * @var Package
     */
    private Package $newPackage;

    /**
     * @var string
     */
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Package $newPackage)
    {
        $this->newPackage = $newPackage;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $funds = auth()->user()->funds;

        // Berücksichtige bei Berechnung Guthaben, was zurückerstattet wird
        // Hole letzte Buchung für User/Paket: Activity::PACKAGE
        // Falls der Benutzer für seine letzte Buchung etwas bezahlt hat,
        // berechne den Betrag, den der Benutzer anteilig zurückerhält
        // Weise den Benutzer ausführlich darauf hin, dass er Guthaben einzahlen muss,
        // falls Kosten das vorhandene Guthaben übersteigen
        // Schreibe Betrag für Restlaufzeit des aktuell gebuchten Paketes gut
        // Falls der Benutzer für seine letzte Buchung etwas bezahlt hat

        $costPackage        = $this->newPackage->monthly_cost * $this->newPackage->paying_rhythm;
        // Berücksichtige bei Berechnung Guthaben, was zurückerstattet wird
        // Hole letzte Buchung für User/Paket
        $lastOrder = UserAccounting::whereUsrId(auth()->user()->user_id)
            ->whereActivityType(Activity::PACKAGE)
            ->where('date_end', '>=', now())
            ->orderByDesc('accounting_id')->first();
        $payback = 0;

        // Falls der Benutzer für seine letzte Buchung etwas bezahlt hat,
        // berechne den Betrag, den der Benutzer anteilig zurückerhält
        if ($lastOrder && $lastOrder->amount < 0) {
            $uam = new UserAccountingManager();
            $payback = $uam->getRefund($lastOrder);
        }

        // Insgesamt verfügbares Guthaben
        $cFunds = $funds + $payback;

        if ($cFunds < $costPackage) {
            $mFunds = $costPackage - $cFunds;
            $packageName = trans_choice('package.package_name', $this->newPackage->package_name);
            $this->message = trans('package.message_error_insufficient_funds',
                ['currency' => $lastOrder->currency ?? 'EUR', 'funds' => number_format($funds, 2), 'payback' => number_format($payback, 2), 'cfunds' => number_format($cFunds, 2), 'packageName' => $packageName, 'uri' => route('accounting.create'), 'topay' => number_format($mFunds, 2)]);
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
