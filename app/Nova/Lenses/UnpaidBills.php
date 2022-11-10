<?php

namespace App\Nova\Lenses;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use App\Models\UserPayment;
use App\Nova\Receiver;

class UnpaidBills extends Lens
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
                ->whereIsPaid(false)
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

            DateTime::make('Created', 'date_created')
                ->sortable(),

            Boolean::make('Is Refundable'),

            Boolean::make('Is Refunded'),

/*            Select::make('Status', 'state')
                ->options(
                    array_map(function($id) { return  trans_choice('accounting.text_user_payment_state', $id); }, array_combine(range(0,4),range(0,4)))
                ),*/

            Number::make('Status', 'state')
                ->sortable()
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
        return 'unpaid-bills';
    }
}
