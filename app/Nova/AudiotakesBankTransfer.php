<?php

namespace App\Nova;

use App\Nova\Actions\MarkAsPaid;
use App\Nova\Filters\PaidOrUnpaid;
use App\Nova\Metrics\AudiotakesBankTransferOpen;
use App\Nova\Metrics\AudiotakesBankTransferPaid;
use App\Nova\Metrics\AudiotakesBankTransferTotal;
use App\Nova\Metrics\AudiotakesBankTransferTrend;
use Illuminate\Http\Request;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class AudiotakesBankTransfer extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\AudiotakesBankTransfer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'user_id',
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'user' => ['username', 'email'],
    ];

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Audiotakes';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('User')
                ->nullable()
                ->searchable(),

            BelongsTo::make('Audiotakes Payout Contact', 'audiotakes_payout_contact')
                ->nullable()
                ->searchable(),

            BelongsTo::make('Audiotakes Contract Partner', 'audiotakes_contract_partner')
                ->nullable()
                ->searchable(),

            Text::make('VAT Id', 'audiotakes_payout_contact.vat_id'),

            Currency::make('Funds', 'funds')
                ->sortable()
                ->exceptOnForms(),

            Number::make('Auszahlungsbetrag', 'funds', function ($value) {
                return $this->amountGross . ' €';
            })
                ->onlyOnIndex(),

            Number::make('Funds', 'funds')
                ->onlyOnForms(),

            Boolean::make('Is paid'),

            Link::make('Gutschrift', 'id')
                ->url(function () {
                    if ($this->id) {
                        return route('audiotakes.creditvoucher', ['id' => $this->id]);
                    }
                })
                ->text("PDF")
                ->exceptOnForms()
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new AudiotakesBankTransferTotal,
            new AudiotakesBankTransferOpen,
            new AudiotakesBankTransferPaid,
            new AudiotakesBankTransferTrend,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new PaidOrUnpaid,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new MarkAsPaid)->showOnTableRow(),
        ];
    }

    public static function label()
    {
        return 'audiotakes Auszahlungen';
    }

    public static function singularLabel()
    {
        return 'audiotakes Auszahlung';
    }
}
