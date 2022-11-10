<?php

namespace App\Nova;

use App\Nova\Metrics\AudiotakesContractPartnerCity;
use App\Nova\Metrics\AudiotakesContractPartnerCountry;
use App\Nova\Metrics\AudiotakesContractPartnerPostCode;
use App\Nova\Metrics\AudiotakesContractsPerMonth;
use App\Nova\Metrics\AudiotakesPioniereCount;
use App\Nova\Metrics\AudiotakesPioniereCountInactive;
use App\Nova\Metrics\AudiotakesPioniereTrend;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class AudiotakesContractPartner extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\AudiotakesContractPartner';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'first_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'first_name', 'last_name', 'email', 'organisation', 'city'
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
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

            Text::make('Organisation')
                ->sortable(),

            Text::make('VAT Id'),

            HasMany::make('Audiotakes Bank Transfer'),

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
            new AudiotakesContractPartnerCountry,
            new AudiotakesContractPartnerCity,
            new AudiotakesContractPartnerPostCode,
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

    public static function label()
    {
        return 'audiotakes Vertragspartner';
    }

    public static function singularLabel()
    {
        return 'audiotakes Vertragspartner';
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        $title = $this->first_name . ' ' . $this->last_name;

        if ($this->organisation) {
            $title .= ' (' . $this->organisation . ')';
        }

        return $title;
    }
}
