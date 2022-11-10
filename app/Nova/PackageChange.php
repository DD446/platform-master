<?php

namespace App\Nova;

use App\Nova\Metrics\PackageChangeDeletionCount;
use App\Nova\Metrics\PackageChangeDeletionTrend;
use App\Nova\Metrics\PackageChangeDowngradeCount;
use App\Nova\Metrics\PackageChangeDowngradeTrend;
use App\Nova\Metrics\PackageChangeUpgradeCount;
use App\Nova\Metrics\PackageChangeUpgradeTrend;
use App\Nova\Metrics\PackageDowngradeCount;
use App\Nova\Metrics\PackageUpgradeCount;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class PackageChange extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\PackageChange::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Admin';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

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
                ->searchable(),

            Number::make('Type')
                ->hideFromIndex(),

            Text::make('Typ', 'type')
                ->exceptOnForms()
                ->displayUsing(function ($id) {
                    return trans_choice('package.package_change_type', $id);
                }),

            Number::make('From')
                ->hideFromIndex(),

            Number::make('To')
                ->hideFromIndex(),

            Text::make('Von', 'from')
                ->exceptOnForms()
                ->displayUsing(function ($id) {
                    if (!$id) return '';
                    return trans_choice('package.package_name_by_id', $id);
                }),

            Text::make('Nach', 'to')
                ->exceptOnForms()
                ->displayUsing(function ($id) {
                    if (!$id) return '';
                    return trans_choice('package.package_name_by_id', $id);
                }),

            Date::make('Created at'),
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
            new PackageChangeUpgradeCount,
            new PackageChangeUpgradeTrend,
            new PackageChangeDowngradeCount,
            new PackageChangeDowngradeTrend,
            new PackageChangeDeletionCount,
            new PackageChangeDeletionTrend,
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
            new Filters\PackageChange,
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
