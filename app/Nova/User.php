<?php

namespace App\Nova;

use App\Nova\Lenses\BlockedUsers;
use KABBOUCHI\NovaImpersonate\Impersonate;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Place;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Panel;
use App\Nova\Lenses\MostValuableUsers;
use App\Nova\Lenses\UsersWhoCanPayWithBill;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\UsersPerMonth;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\User';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'username';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'usr_id', 'username', 'first_name', 'last_name', 'email', 'street', 'organisation'
    ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Users';

    public static function label()
    {
        return 'Benutzer';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'usr_id')
                ->sortable(),

            Gravatar::make(),

            Text::make('Username')
                ->sortable()
                ->rules('required', 'max:255')
                ->creationRules(['unique:usr,username'])
                ->updateRules(['unique:usr,username,{{resourceId}},usr_id']),

            Text::make('Name', function () {
                return $this->first_name.' '.$this->last_name;
            })
                ->exceptOnForms()
                ->sortable(),

            Text::make('First Name')
                ->onlyOnForms()
                ->sortable()
                ->rules('max:255'),

            Text::make('Last Name')
                ->onlyOnForms()
                ->sortable()
                ->rules('max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:255')
                ->creationRules(['unique:usr,email'])
                ->updateRules(['unique:usr,email,{{resourceId}},usr_id']),

/*            Text::make('Funds')
                ->sortable()
                ->exceptOnForms(),*/

            Number::make('Funds', 'funds', function ($value) {
                return number_format($value, 2) . '€';
            })
                ->sortable()
                ->exceptOnForms(),

            Currency::make('Funds', 'funds')
                ->currency('EUR')
                ->onlyOnForms(),

/*            Currency::make('Funds')
                ->sortable()
                ->exceptOnForms()
                ->currency('EUR')
                ->locale('de'),*/

            DateTime::make('Created', 'created_at'),

            Boolean::make('Active', 'is_acct_active')
                ->sortable(),

            Text::make('Trial', 'is_trial')
                ->displayUsing(function ($id) {
                    return trans_choice('package.trial_state', $id);
                })
                ->exceptOnForms()
                ->sortable(),

            Text::make('Trial', 'is_trial')
                ->onlyOnForms(),

            Boolean::make('Blocked', 'is_blocked')
                ->hideFromIndex()
                ->sortable(),

            Boolean::make('Updating', 'is_updating')
                ->hideFromIndex()
                ->sortable(),

            Boolean::make('Protected', 'is_protected')
                ->hideFromIndex()
                ->sortable(),

            Boolean::make('Supporter', 'is_supporter')
                ->hideFromIndex()
                ->sortable(),

            Boolean::make('Advertiser', 'is_advertiser')
                ->hideFromIndex()
                ->sortable(),

            Boolean::make('Can pay by bill', 'can_pay_by_bill')
                ->hideFromIndex(),

            Boolean::make('Agency enabled', 'agency_enabled')
                ->hideFromIndex(),

            Text::make('Package', 'package.package_name')
                ->exceptOnForms()
                ->displayUsing(function ($id) {
                    return trans_choice('package.package_name', $id);
                }),

            Number::make('Package', 'package_id')
                ->onlyOnForms(),

            Text::make('Rolle', 'role_id')
                ->exceptOnForms()
                ->displayUsing(function ($id) {
                    return trans_choice('user.role_name', $id);
                }),

            Number::make('Rolle', 'role_id')
                ->onlyOnForms(),

            Boolean::make('Use new Statistics', 'use_new_statistics')
                ->hideFromIndex()
                ->sortable(),

/*            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6')
                ->updateRules('nullable', 'string', 'min:6'),*/

            new Panel('Address Information', $this->addressFields()),

            HasOne::make('UserBillingContact'),

            HasMany::make('UserPayments'),

            HasMany::make('Space'),

            Impersonate::make()->withMeta([
                'id' => $this->usr_id,
                'hideText' => true,
            ])->hideWhenUpdating(),
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
            new NewUsers,
            new UsersPerDay,
            new UsersPerMonth,
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
            new Filters\Package,
            new Filters\UserType,
            new Filters\Trial,
            new Filters\UserRegion,
            new Filters\UserFunds,
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
            new MostValuableUsers,
            new UsersWhoCanPayWithBill,
            new BlockedUsers
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
        return [];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "{$this->first_name} {$this->last_name} ({$this->usr_id}) <{$this->email}>";
    }

    private function addressFields()
    {
        return [
            Text::make('Straße', 'street')->hideFromIndex(),
            Text::make('Hausnummer', 'housenumber')->hideFromIndex(),
            //Text::make('E-Mail-Adresse', 'feed_email')->hideFromIndex(),
            Text::make('City')->hideFromIndex(),
            //Text::make('State', 'region')->hideFromIndex(),
            Text::make('Postal Code', 'post_code')->hideFromIndex(),
            Country::make('Country')->sortable(),
            Text::make('Organisation')->hideFromIndex(),
            Text::make('Url')->hideFromIndex(),
            Text::make('Telephone')->hideFromIndex(),
            Text::make('Telefax')->hideFromIndex(),
        ];
    }
}
