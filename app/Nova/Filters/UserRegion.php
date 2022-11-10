<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserRegion extends Filter
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
            case 'dach':
                return $query->whereIn('country', ['de', 'at', 'ch']);
            case 'not_dach':
                return $query->whereNotIn('country', ['de', 'at', 'ch']);
            case 'outside_german':
                return $query->whereNotIn('country', ['de', 'at', 'ch', 'li', 'lu']);
            case 'europe_wo_dach':
                return $query->whereIn('country', ['li', 'lu', 'gb', 'es', 'fi', 'dk', 'nl', 'be', 'fr', 'pt', 'cz', 'se']);
            case 'outside_europe':
                return $query->whereNotIn('country', ['de', 'at', 'ch', 'li', 'lu', 'gb', 'es', 'fi', 'dk', 'nl', 'be', 'fr', 'pt', 'cz', 'se']);
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
            'DACH-Region' => 'dach',
            'Ausserhalb DACH' => 'not_dach',
            'Ausserhalb Deutschsprachig' => 'outside_german',
            'Europäisch ohne DACH' => 'europe_wo_dach',
            'Ausserhalb europäisch' => 'outside_europe',
        ];
    }
}
