<?php

namespace App\Nova\Lenses;

use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use App\Models\UserPayment;

class MostValuableUsers extends Lens
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
            $query->select(self::columns())
                ->join('user_payments', 'usr.usr_id', '=', 'user_payments.receiver_id')
                /*->where('user_payments.payer_id', '=', 'usr.usr_id')*/
                ->where('user_payments.payment_method', '!=', UserPayment::PAYMENT_METHOD_VOUCHER)
                ->orderBy('revenue', 'desc')
                ->groupBy('usr.usr_id', 'usr.username')
        ));
    }


    /**
     * Get the columns that should be selected.
     *
     * @return array
     */
    protected static function columns()
    {
        return [
            'usr.usr_id',
            'usr.username',
            DB::raw('SUM(user_payments.amount) as revenue'),
            'usr.funds'
        ];
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
            ID::make('ID', 'usr_id'),

            Text::make('Name', 'username'),

            Number::make('Revenue', 'revenue', function ($value) {
                return number_format($value, 2) . '€';
            }),

            Number::make('Funds', 'funds', function ($value) {
                return number_format($value, 2) . '€';
            }),
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
        return 'most-valuable-users';
    }
}
