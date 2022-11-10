<?php

namespace App\Http\Controllers\API;

use App\Classes\Datacenter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function lang()
    {
        $locale = Str::before(app()->getLocale(), '-');
        $lang = include base_path('vendor/umpirsky/language-list/data/' . $locale . '/language.php');

        if (\request()->exists('grouped')) {
            $list = [];
            foreach ($lang as $key => $item) {
                $list[] = [
                    'value' => $key,
                    'text' => $item
                ];
            }
            $lang = $list;
        }

        return response()->json($lang);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function itunes()
    {
        $itunes = Datacenter::getItunesCategoriesAsArray();

        return response()->json($itunes);
    }
}
