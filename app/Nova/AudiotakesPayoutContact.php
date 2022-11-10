<?php

namespace App\Nova;

use App\Nova\Filters\Verified;
use Danielebarbaro\LaravelVatEuValidator\Rules\VatNumber;
use Illuminate\Http\Request;
use Intervention\Validation\Rules\Iban;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class AudiotakesPayoutContact extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\AudiotakesPayoutContact::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
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

            Text::make('Name')
                ->rules('required', 'max:255'),

            Text::make('Steuernummer', 'tax_id')
                ->rules('max:255'),

            Text::make('Umsatzsteuer-ID', 'vat_id')
                ->rules('max:255', 'nullable', new VatNumber),

            Text::make('PayPal', 'paypal')
                ->rules('max:255', 'email'),

            Text::make('Konto-Inhaber', 'bank_account_owner')
                ->rules('max:255'),

            Text::make('IBAN', 'iban')
                ->rules('max:255', 'nullable', new Iban),

            Country::make('Country')
                ->sortable(),

            Boolean::make('Is verified'),
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
        return [];
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
            new Verified
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
        return [];
    }

    public static function label()
    {
        return 'audiotakes Auszahlungskonten';
    }

    public static function singularLabel()
    {
        return 'audiotakes Auszahlungskonto';
    }
}
