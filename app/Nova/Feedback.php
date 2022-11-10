<?php

namespace App\Nova;

use App\Nova\Metrics\FeedbackCount;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Feedback extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Feedback';

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
        'id', 'user_id', 'type', 'comment'
    ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Content';

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

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

            Textarea::make('Comment')
                ->alwaysShow(),

            Text::make('Comment', 'comment')
                ->displayUsing(function($id) {
                    return Str::limit(strip_tags($id), 150);
                })->onlyOnIndex(),

            Text::make('Type')
                ->displayUsing(function($id) {
                    return trans_choice('feedback.type', $id);
                }),

            Text::make('Entity'),

            BelongsTo::make('User'),

            Text::make('Vorname', 'user.first_name')
                ->hideFromIndex(),

            Text::make('Nachname', 'user.last_name')
                ->hideFromIndex(),

            Text::make('E-Mail', 'user.email'),

            Images::make('Screenshot', 'screenshot') // second parameter is the media collection name
                ->conversionOnIndexView('thumb'), // conversion used to display the image

            Date::make('Created', 'created_at'),

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
            new FeedbackCount,
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
