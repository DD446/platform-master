<?php

namespace App\Nova;

use App\Classes\Datacenter;
use Illuminate\Support\Str;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Campaign extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Campaign';

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
        'id', 'title',
    ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Advertising';

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
        $itunes = [];
        foreach (Datacenter::getItunesCategoriesAsArray() as $a) {
            $itunes[$a['value']] = $a['text'];
        }

        return [
            ID::make()->sortable(),

            Text::make('Title'),

            Text::make('Description')
                ->displayUsing(function($c) {
                    return Str::limit($c, 50);
                })
                ->onlyOnIndex(),

            Textarea::make('Description')
                ->hideFromIndex()
                ->alwaysShow(),

            Text::make('Name'),

            Text::make('Reply To'),

            Select::make('Kategorie')
                ->options($itunes)
                ->searchable()
                ->displayUsingLabels(),

            BelongsTo::make('User')
                ->searchable(),

            HasMany::make('CampaignInvitations'),

            Date::make('Created', 'created_at'),

/*            Link::make('Einladungen', 'id')
                ->url(function () {
                    return route('invitations.create') . '?campaign_id=' . $this->id;
                })
                ->text("Verschicken")
                ->exceptOnForms()*/
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
