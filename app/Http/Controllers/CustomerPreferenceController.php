<?php

namespace App\Http\Controllers;

use App\Models\CustomerPreference;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerPreferenceController extends Controller
{
       public function store(Request $request){
           $customerPreference = new CustomerPreference;
           $customerPreference->company_name = $request->company_name;
           $customerPreference->image_name = $request->image_name;
           $customerPreference->user_id = $request->user_id;
           $customerPreference->landing_page_id = $request->landing_page_id;
           $customerPreference->save();
       }
}

