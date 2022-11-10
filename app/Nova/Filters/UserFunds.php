<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserFunds extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

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
        switch ($value) {
            case 'positive':
                return $query->where('funds', '>', 0);
            case 'positive_zero':
                return $query->where('funds', '>=', 0);
            case 'negative':
                return $query->where('funds', '<', 0);
            case 'zero':
                return $query->where('funds', '=', 0);
        }

        return $query;
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
            'Positiv oder Null' => 'positive_zero',
            'Positiv' => 'positive',
            'Negativ' => 'negative',
            'Null' => 'zero',
        ];
    }
}
