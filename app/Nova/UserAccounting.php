<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Classes\Activity;
use Titasgailius\SearchRelations\SearchesRelations;

class UserAccounting extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\UserAccounting::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'activity_description';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'activity_description',
        'usr_id',
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
            ID::make('accounting_id')->sortable(),

            Number::make('User ID', 'usr_id')
                ->hideFromIndex(),

            BelongsTo::make('User')
                ->sortable()
                ->searchable()
                ->nullable(),

            Text::make('Activity Type')
                ->displayUsing(function($c) {
                    return trans_choice('bills.accounting_activity_type', $c);
                }),

            Text::make('Activity Characteristic')
/*                ->displayUsing(function ($c) {
                    return trans_choice('bills.accounting_activity_add_funds_type', $c);
                })*/,

            Text::make('Activity Description'),

            Currency::make('Amount'),

            Text::make('Currency'),

            Date::make('Created', 'date_created'),
            Date::make('Start', 'date_start'),
            Date::make('End', 'date_end'),

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
