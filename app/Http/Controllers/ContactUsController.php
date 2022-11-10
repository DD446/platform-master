<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactUsRequest;
use App\Mail\ContactUsMail;
use App\Models\ContactUs;
use SEO;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        SEO::setTitle(trans('contact_us.title'));

        $aEnquiry = [
            'general' => trans('contact_us.enquiry.general'),
            'commercial' => trans('contact_us.enquiry.commercial'),
            'bill' => trans('contact_us.enquiry.bill'),
            'interview' => trans('contact_us.enquiry.interview'),
            'feedback' => trans('contact_us.enquiry.feedback'),
            'feature' => trans('contact_us.enquiry.feature'),
            'support' => trans('contact_us.enquiry.support'),
            'bug' => trans('contact_us.enquiry.bug'),
        ];

        $args = [
            'enquiry_types' => $aEnquiry,
            'enquiry_type' => request()->get('enquiry_type'),
            'user' => (auth()->user() ?? null),
            'comment' => request('comment'),
        ];

        if (request()->ajax() || request()->wantsJson()) {
            return view('parts.forms.contactus', $args);
        }
        return view('contact_us', $args);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactUsRequest $request)
    {
        $msg = ['error' => trans('contact_us.store_error')];
        $save = true;

        // Spam prevention
        if (!\auth()->check()) {
            // Fail on anything with HTML tags
            if (strlen($request->comment) != strlen(strip_tags($request->comment))) {
                $save = false;
            }

            // Fail on names with camel case, e.g. GreaterWorlds and lower case r-u-t
            if ($request->name != ucwords(mb_strtolower($request->name))) {
                $save = false;
            }
        }

        if ($save) {
            $contact = new ContactUs($request->all());

            if ($contact->save()) {
                $msg = ['success' => trans('contact_us.store_success'), 'text' => trans('contact_us.store_success_extra')];
            }
            //app('mathcaptcha')->reset();
        }

        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json($msg);
            }
            return response()->json($msg);
        }
        return redirect()->route('contactus.create')->with($msg);
    }
}
