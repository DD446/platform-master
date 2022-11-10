<?php

namespace App\Models;

use App\Classes\TaxCalculator;
use App\Models\Base\AudiotakesBankTransfer as BaseAudiotakesBankTransfer;
use App\Notifications\AudiotakesBankTransferCreditNoteNotification;
use App\Notifications\AudiotakesBankTransferRequestNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Actionable;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;

class AudiotakesBankTransfer extends BaseAudiotakesBankTransfer
{
    use Notifiable, Actionable;

    const UNPAID = false;
    const PAID = true;
    const CN_STORAGE_DIR = 'audiotakes-bank-transfer-creditnote';
    const CN_EXTENSION = '.pdf';
    const VAT = 19;

    protected $fillable = [
		'user_id',
		'audiotakes_payout_contact_id',
		'audiotakes_contract_partner_id',
		'funds',
		'is_paid'
	];

    protected static function booted()
    {
        parent::booted();

        static::created(function(AudiotakesBankTransfer $transfer)
        {
            $transfer->sendNotification();
        });
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function goal()
    {
        return $this->audiotakes_payout_contact();
    }

    public function markAsPaid()
    {
        $this->update(['is_paid' => true]);
    }

    public function sendNotification()
    {
        //$this->notify(new AudiotakesBankTransferRequestNotification);
        $this->notify(new AudiotakesBankTransferCreditNoteNotification);
    }

    public function getCountrySpelledAttribute()
    {
        return get_country_spelled($this->audiotakes_contract_partner->country);
    }

    public function getBillingNumberAttribute()
    {
        return trans('audiotakes.billing_number', ['id' =>  Str::padLeft($this->id, 8, 0)]);
    }

    public function getCustomerNumberAttribute()
    {
        return Str::padLeft($this->audiotakes_payout_contact->id, 8, 0);
    }

    public function getAmountGrossAttribute()
    {
        if ($this->isPrivate() || $this->is_reverse_charge) {
            return $this->funds;
        }

        $oTaxCalculator = new TaxCalculator($this->audiotakes_payout_contact->country, $this->audiotakes_payout_contact->vat_id, $this->audiotakes_contract_partner->post_code ?? null);
        $oTaxCalculator->setNet($this->funds);
        $oTaxCalculator->setVat(self::VAT);

        return $oTaxCalculator->getGross();
    }

    public function getAmountTaxAttribute()
    {
        if ($this->isPrivate() || $this->is_reverse_charge) {
            return false;
        }

        $oTaxCalculator = new TaxCalculator($this->audiotakes_payout_contact->country, $this->audiotakes_payout_contact->vat_id, $this->audiotakes_contract_partner->post_code);
        $oTaxCalculator->setNet($this->funds);
        $oTaxCalculator->setVat(self::VAT);

        return $oTaxCalculator->getTax();
    }

    public function getAmountVatAttribute()
    {
        if ($this->isPrivate() || $this->is_reverse_charge) {
            return false;
        }

        return self::VAT;
    }

    public function getIsReverseChargeAttribute()
    {
        if ($this->isPrivate()) {
            return false;
        }

        $oTaxCalculator = new TaxCalculator($this->audiotakes_payout_contact->country, $this->audiotakes_payout_contact->vat_id, $this->audiotakes_contract_partner->post_code ?? null);

        return $oTaxCalculator->isReverseCharge();
    }

    public function getCurrencyAttribute()
    {
        return 'EUR';
    }

    public function saveBill(): string
    {
        $username = $this->user->username;
        $filename = storage_path(self::CN_STORAGE_DIR . get_user_path($username)) . DIRECTORY_SEPARATOR . $this->billing_number. self::CN_EXTENSION;
        File::ensureDirectoryExists(File::dirname($filename));
        $payment = clone $this;
        $html = view('audiotakes.creditvoucher-wrapper', compact('payment'))->render();

        Browsershot::html($html)
            ->setOption('addStyleTag', json_encode(['content' => 'html { -webkit-print-color-adjust: exact; }']))
            ->noSandbox()
            ->format('A4')
            ->save($filename);

        if (!File::exists($filename)) {
            Log::error("ERROR: User: {$username}: Could not create creditvoucher at {$filename}.");
            throw new \Exception("ERROR: Could not create creditvoucher.");
        }

        return $filename;
    }

    public function isPrivate(): bool
    {
        return is_null($this->audiotakes_payout_contact->vat_id);
    }
}
