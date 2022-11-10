<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supporter;
use App\Models\User;

class SupporterController extends Controller
{
    public function index()
    {
        $supporters = User::supporter()->get();
        $personals = Supporter::personal()->get();

        return view('supporter.index', compact('supporters', 'personals'));
    }

    public function store(Request $request)
    {
        $userId = auth()->user()->usr_id;

        $this->validate($request, [
            'supporter_id' => 'required|exists:usr,usr_id'
        ]);

        $msg = ['success' => trans('supporter.success_supporter_saved')];

        $supporter = Supporter::withTrashed()
            ->where('user_id', $userId)
            ->where('supporter_id', $request->supporter_id)
            ->first();

        if ($supporter) {
            $ret = $supporter->restore();
        } else {
            $supporter = new Supporter();
            $supporter->user_id = $userId;
            $supporter->supporter_id = $request->supporter_id;
            $ret = $supporter->save();
        }

        if (!$ret) {
            $msg = ['error' => trans('supporter.failure_supporter_saved')];
        }

        return redirect()->route('supporter.index')->with($msg);
    }

    public function show(Supporter $supporter)
    {
    }

    public function destroy(Request $request)
    {
        $msg = ['success' => trans('supporter.success_supporter_deleted')];

        if (!Supporter::personal()->find($request->supporter)->delete()) {
            $msg = ['error' => trans('supporter.failure_supporter_deleted')];
        }

        return redirect()->route('supporter.index')->with($msg);
    }
}
