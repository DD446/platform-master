<?php

namespace App\Nova;

use App\Nova\Metrics\FeedCount;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Feed extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Feed';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'feed_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'feed_id', 'username', 'rss.title',
    ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Content';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('Feed-ID', 'feed_id')
                ->sortable(),

            Text::make('Username', 'username')
                ->sortable()
                ->rules('required', 'max:255'),

/*            BelongsTo::make('User')
                ->sortable()->searchable(),*/

            Text::make('Title', 'rss.title')
                ->sortable()
                /*->rules('required', 'max:255')*/,

            Code::make('Description', 'rss.description')
                ->options(['mode' => 'html']),

            Boolean::make('Seeks advertisers', 'settings.ads')
                ->hideFromIndex(),

            //Select::make('Itunes Categories', 'itunes.category'),

            Text::make('Spotify-URI', 'settings.spotify_uri')
                ->hideFromIndex(),

            Text::make('Amazon', 'settings.amazon')
                ->hideFromIndex(),

            Boolean::make('audiotakes', 'settings.audiotakes'),

            Text::make('audiotakes ID', 'settings.audiotakes_id')
                ->hideFromIndex(),

            Boolean::make('Chartable', 'settings.chartable'),

            Text::make('Chartable ID', 'settings.chartable_id')
                ->hideFromIndex(),

            Boolean::make('RMS', 'settings.rms'),

            Text::make('RMS ID', 'settings.rms_id')
                ->hideFromIndex(),

            Boolean::make('Podcorn', 'settings.podcorn'),

            Boolean::make('Podtrac', 'settings.podtrac'),
            //HasMany::make('Shows'),
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
            new FeedCount,
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

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "{$this->username}";
    }

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    //public static $with = ['user'];

}
