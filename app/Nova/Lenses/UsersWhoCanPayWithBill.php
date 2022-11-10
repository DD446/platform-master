<?php

namespace App\Nova\Lenses;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use KABBOUCHI\NovaImpersonate\Impersonate;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use App\Models\UserPayment;

class UsersWhoCanPayWithBill extends Lens
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
                ->where('can_pay_by_bill', '=', true)
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
            ID::make('ID', 'usr_id'),

            Text::make('Name', 'username'),

            Text::make('Organisation', 'organisation'),

            Number::make('Funds', 'funds', function ($value) {
                return number_format($value, 2) . '€';
            }),

/*            Impersonate::make()->withMeta([
                'id' => $this->user->usr_id,
                'hideText' => true,
            ])->hideWhenUpdating(),*/
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
        return 'users-who-can-pay-with-bill';
    }
}
