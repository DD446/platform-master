<?php

namespace App\Nova;

use App\Nova\Actions\MarkAsPaid;
use App\Nova\Filters\UserPaymentStatusFilter;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use App\Nova\Lenses\PaymentsChargedWithBill;
use App\Nova\Metrics\Revenue;
use App\Nova\Metrics\RevenuePerDay;
use App\Nova\Metrics\RevenuePerMonth;
use App\Nova\Lenses\UnpaidBills;
use Titasgailius\SearchRelations\SearchesRelations;

class UserPayment extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\UserPayment';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'payment_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'payment_id', 'receiver_id', 'bill_id', 'amount'
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'receiver' => ['username', 'email'],
        'payer' => ['username', 'email'],
    ];

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = true;

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
            ID::make('ID', 'payment_id')
                ->sortable(),

            Text::make('Bill ID'),

            BelongsTo::make('Receiver')
                ->sortable()
                ->searchable(),

            Boolean::make('User is blocked', 'receiver.is_blocked'),

            BelongsTo::make('Payer')
                ->hideFromIndex()
                ->searchable(),

            //Text::make('Amount'),

            Currency::make('Amount', function() {
                return $this->amount;
            })
                ->currency($this->currency)
                ->sortable(),

            Text::make('Payment Method')
                ->displayUsing(function ($id) {
                    return trans_choice('accounting.text_payment_method', $id);
                })->exceptOnForms(),

            Select::make('Payment Method')
                ->options(
                    array_map(function($id) { return  trans_choice('accounting.text_payment_method', $id); }, array_combine(range(1,5),range(1,5)))
                )->onlyOnForms(),

            Boolean::make('Is paid'),


            Boolean::make('Is refundable')
                ->hideFromIndex(),

            Boolean::make('Is refunded')
                ->hideFromIndex(),

            Number::make('State')
                ->hideFromIndex(),

            Currency::make('Amount Refunded', function() {
                return $this->amount_refunded;
            })->currency($this->currency)
                ->onlyOnDetail(),

            Currency::make('Amount refunded')
                ->hideFromDetail()
                ->hideFromIndex(),

            Text::make('Comment')
                ->hideFromIndex(),

            DateTime::make('Created', 'date_created'),

            Link::make('Bill', 'payment_id')
                ->url(function () {
                    // This prevents failure when creating a new entry
                    if (isset($this->bill_id) && $this->bill_id) {
                        return route('bills.download', [ 'id' => $this->bill_id ]);
                    }
                })
                ->text('↓ Download'),
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
            new UserPaymentStatusFilter,
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
        return [
            new PaymentsChargedWithBill,
            new UnpaidBills,
        ];
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

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    //public static $with = ['receiver', 'payer'];

}
