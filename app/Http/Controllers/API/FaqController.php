<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCollection;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * @param  Request  $request
     * @return FaqCollection
     * @hideFromAPIDocumentation
     */
    public function search(Request $request)
    {
        $q = $request->get('q');
        $faqs = Faq::search($q)->get();

        return new FaqCollection($faqs);
    }
}
