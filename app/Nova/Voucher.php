<?php

namespace App\Nova;

use Carbon\Carbon;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Voucher extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Voucher';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'voucher_code';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'voucher_code',
        'comment',
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
            ID::make('ID', 'id')
                ->sortable(),

            BelongsTo::make('Voucher Action'),

            Text::make('Code', 'voucher_code'),

            Number::make('Usage count', 'usage_count')
                ->withMeta(['value' => $this->usage_count ?: 0]),

            DateTime::make('Valid until')
                ->withMeta(['value' => $this->valid_until ?: Carbon::now()->addYear()]),

            Textarea::make('Comment'),

            Number::make('Valid for', 'valid_for')
                ->withMeta(['value' => $this->valid_for ?: -1])
                ->hideFromIndex(),

            Number::make('Amount')
                ->withMeta(['value' => $this->amount ?: 1]),

            Number::make('Amount Per Person')
                ->withMeta(['value' => $this->amount_per_person ?:1]),

            DateTime::make('Date created')
                ->withMeta(['value' => $this->date_created ?: Carbon::now()])
                ->hideFromIndex()
                ->hideWhenUpdating(),
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
