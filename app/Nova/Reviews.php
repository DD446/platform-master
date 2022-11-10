<?php

namespace App\Nova;

use App\Nova\Metrics\ReviewCount;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Reviews extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Review';

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
        'id', 'q1', 'published_cite'
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
    public static $group = 'Content';

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    //public static $with = ['user'];

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

            Text::make('Cite', 'published_cite')
                ->displayUsing(function($value) {
                    return Str::limit(strip_tags($value), 50);
                })
                ->onlyOnIndex(),

            Text::make('Q1')
                ->displayUsing(function($value) {
                    return Str::limit(strip_tags($value), 50);
                })
                ->onlyOnIndex(),

            Trix::make('Q1')
                ->alwaysShow()
                ->hideFromIndex(),

            Trix::make('Q2')
                ->alwaysShow()
                ->hideFromIndex(),

            Trix::make('Q3')
                ->alwaysShow()
                ->hideFromIndex(),

            Trix::make('Q4')
                ->alwaysShow()
                ->hideFromIndex(),

            Trix::make('Cite', 'published_cite')
                ->alwaysShow()
                ->hideFromIndex(),

            Boolean::make('Published', 'is_published')
                ->sortable(),

            BelongsTo::make('User')
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
            new ReviewCount,
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
            new Filters\Published,
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
