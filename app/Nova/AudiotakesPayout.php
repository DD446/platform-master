<?php

namespace App\Nova;

use App\Nova\Filters\Month;
use App\Nova\Filters\Year;
use App\Nova\Metrics\AudiotakesFundsOpenTotal;
use App\Nova\Metrics\AudiotakesFundsRawTotal;
use App\Nova\Metrics\AudiotakesFundsTotal;
use App\Nova\Metrics\AudiotakesRevenueTotal;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class AudiotakesPayout extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\AudiotakesPayout::class;

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
        'id', 'user_id', 'year',
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'user' => ['username', 'email', 'first_name', 'last_name'],
        'audiotakes_contract' => ['identifier'],
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
                ->sortable(),

            BelongsTo::make('Audiotakes Contract')
                ->sortable(),

            Number::make('Funds')
                ->sortable(),

            Number::make('Funds open')
                ->sortable(),

            Number::make('Funds raw')
                ->sortable(),

            Number::make('Month')
                ->sortable(),

            Number::make('Year')
                ->sortable(),

            Number::make('Holdback')
                ->onlyOnDetail(),

            Number::make('Share')
                ->onlyOnDetail(),

            Text::make('Currency')
                ->onlyOnDetail(),
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
            new AudiotakesFundsTotal,
            new AudiotakesFundsOpenTotal,
            new AudiotakesFundsRawTotal,
            new AudiotakesRevenueTotal,
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
            new Month,
            new Year
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
}
