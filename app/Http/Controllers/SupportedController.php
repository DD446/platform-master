<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supporter;
use App\Models\User;

class SupportedController extends Controller
{
    public function index()
    {
        $supported = Supporter::supported()->get();

        return view('supported.index', compact('supported'));
    }

    public function show(Request $request)
    {
        $this->validate($request, [
            'supported_id' => 'required|exists:supporters,user_id'
        ]);

        Supporter::where('user_id', $request->supported_id)
            ->where('supporter_id', auth()->user()->usr_id)
            ->firstOrFail();

        $supported = User::findOrFail($request->supported_id);

        Auth::user()->impersonate($supported);
        //Auth::loginUsingId($request->supported_id);

        return redirect()->route('home');
    }
}
