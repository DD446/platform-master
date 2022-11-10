<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = \Countrylist::getList('de');

        // Exclude terratories
        foreach (['EZ', 'IO', 'TF', 'PS'] as $code) {
            unset($countries[$code]);
        }

        // TODO: I18N
        $countriesFirst = [
            'DE' => 'Deutschland',
            'AT' => 'Österreich',
            'CH' => 'Schweiz',
            'LI' => 'Liechtenstein',
        ];

        foreach ($countriesFirst as $code => $country) {
            unset($countries[$code]);
        }

        $countries = $countriesFirst + $countries;

        if (\request()->has('grouped')) {
            $grouped = [];
            foreach ($countries as $code => $country) {
                $grouped[] = [
                    'code' => $code,
                    'name' => $country,
                ];
            }
            $countries = $grouped;
        }

        if (request()->wantsJson()) {
            return response()->json($countries);
        }

        abort(404);
    }
}
