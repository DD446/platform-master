<?php

namespace App\Nova;

use App\Nova\Actions\MarkAsPaid;
use App\Nova\Filters\Created;
use App\Nova\Filters\PaidOrUnpaid;
use App\Nova\Metrics\AudiotakesPodcasterTransferOpen;
use App\Nova\Metrics\AudiotakesPodcasterTransferPaid;
use App\Nova\Metrics\AudiotakesPodcasterTransferTotal;
use App\Nova\Metrics\AudiotakesPodcasterTransferTrend;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class AudiotakesPodcasterTransfer extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\AudiotakesPodcasterTransfer::class;

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
        'id',
        'user_d',
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
                ->nullable()
                ->searchable(),

            Number::make('Funds', 'funds', function ($value) {
                return number_format($value, 2) . '€';
            })
                ->sortable()
                ->exceptOnForms(),

            Number::make('Funds', 'funds')
                ->onlyOnForms(),

            Boolean::make('Is paid'),

            DateTime::make('Created at')
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
            new AudiotakesPodcasterTransferTotal,
            new AudiotakesPodcasterTransferOpen,
            new AudiotakesPodcasterTransferPaid,
            new AudiotakesPodcasterTransferTrend,
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
            new PaidOrUnpaid,
            new Created,
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
        return [
            (new MarkAsPaid)->showOnTableRow(),
        ];
    }

    public static function label()
    {
        return 'audiotakes Guthaben-Transfers';
    }

    public static function singularLabel()
    {
        return 'audiotakes Guthaben-Transfer';
    }
}
