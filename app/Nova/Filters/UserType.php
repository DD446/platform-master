<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\User;

class UserType extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('role_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Gast' => User::ROLE_GUEST,
            'Administrator' => User::ROLE_ADMIN,
            'Benutzer' => User::ROLE_USER,
            'Team-Mitglied' => User::ROLE_TEAM,
            'Supporter' => User::ROLE_SUPPORTER,
            'Redakteur' => User::ROLE_EDITOR,
        ];
    }
}
