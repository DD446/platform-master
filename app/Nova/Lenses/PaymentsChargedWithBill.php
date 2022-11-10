<?php

namespace App\Nova\Lenses;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use App\Models\UserPayment;
use App\Nova\Receiver;

class PaymentsChargedWithBill extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query
                ->where('payment_method', '=', UserPayment::PAYMENT_METHOD_BILL)
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'payment_id')
                ->sortable(),

            Text::make('Bill ID'),

            BelongsTo::make('Receiver', 'receiver', Receiver::class)
                ->sortable()
                ->searchable(),
/*
            BelongsTo::make('Payer')
                ->hideFromIndex()
                ->hideWhenUpdating()
                ->searchable(),*/

            Currency::make('Amount')
                ->sortable(),

            Text::make('Comment'),

            Boolean::make('Is paid'),

            DateTime::make('Created', 'date_created')
                ->sortable(),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'payments-charged-with-bill';
    }
}
