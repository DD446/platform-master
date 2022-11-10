<?php

namespace App\Nova;

use App\Nova\Metrics\AudiotakesContractOpenIds;
use App\Nova\Metrics\AudiotakesContractsPerMonth;
use App\Nova\Metrics\AudiotakesPioniereCount;
use App\Nova\Metrics\AudiotakesPioniereCountInactive;
use App\Nova\Metrics\AudiotakesPioniereTrend;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class AudiotakesContract extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\AudiotakesContract';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'identifier';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'feed_id',
        'identifier'
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'user' => ['username', 'email'],
        'audiotakes_contract_partner' => ['first_name', 'last_name', 'organisation'],
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

            BelongsTo::make('Audiotakes Contract Partner', 'audiotakes_contract_partner')
                ->nullable()
                ->searchable(),

            Text::make('Feed ID')
                ->rules('max:255'),

            Text::make('Identifier')
                ->rules('max:255'),

            Number::make('Share')
                ->sortable(),

            DateTime::make('Date accepted', 'audiotakes_date_accepted')
                ->sortable(),

            DateTime::make('Date canceled', 'audiotakes_date_canceled')
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
            new AudiotakesPioniereCount,
            new AudiotakesPioniereTrend,
            new AudiotakesPioniereCountInactive,
            new AudiotakesContractsPerMonth,
            new AudiotakesContractOpenIds,
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
        return 'audiotakes Verträge';
    }

    public static function singularLabel()
    {
        return 'audiotakes Vertrag';
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->whereNotNull('feed_id');
    }
}
