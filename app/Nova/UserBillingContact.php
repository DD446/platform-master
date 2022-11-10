<?php

namespace App\Nova;

use App\Nova\Metrics\BillingContactBillByMailCount;
use App\Nova\Metrics\BillingContactsPerCity;
use App\Nova\Metrics\BillingContactsPerCountry;
use App\Nova\Metrics\BillingContactTrend;
use App\Nova\Metrics\BillingContactTypeCount;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class UserBillingContact extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\UserBillingContact';

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
        'first_name', 'last_name', 'email', 'organisation', 'city', 'department'
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
    public static $group = 'Payment';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User')
                ->searchable(),

            Text::make('First Name')
                ->rules('required', 'max:255'),

            Text::make('Last Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email'),

            Text::make('Telephone'),

            Text::make('Telefax')
                ->onlyOnForms(),

            Text::make('Organisation')
                ->sortable(),

            Text::make('Department'),

            Text::make('Street')
                ->onlyOnForms(),

            Text::make('Housenumber')
                ->onlyOnForms(),

            Text::make('City')
                ->sortable(),

            Country::make('Country')
                ->sortable(),

            Text::make('Post code')
                ->sortable(),

            Text::make('VAT Id')
                ->sortable(),

            Text::make('Extras')
                ->onlyOnForms(),

            Boolean::make('Bill by email')
                ->sortable(),
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
            new BillingContactTrend,
            new BillingContactsPerCountry,
            new BillingContactsPerCity,
            new BillingContactTypeCount,
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
        return [];
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
}
