<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\UserAccounting;
use Illuminate\Http\Request;

class UserAccountingController extends Controller
{
    const CACHE_KEY_PAYPAL_AMOUNT_LIST = 'paypal_amount_list';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        \SEO::setTitle(trans('accounting.page_title_funds'));

        $bookings = UserAccounting::owner()->orderBy('date_created', 'DESC')->paginate(15);

        return view('user.accounting.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        \SEO::setTitle(trans('accounting.page_title_funds_add'));

        $aAmount = $this->getAmountList();

        return view('user.accounting.create', compact('aAmount'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserAccounting  $userAccounting
     * @return \Illuminate\Http\Response
     */
    public function show(UserAccounting $userAccounting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserAccounting  $userAccounting
     * @return \Illuminate\Http\Response
     */
    public function edit(UserAccounting $userAccounting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserAccounting  $userAccounting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserAccounting $userAccounting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserAccounting  $userAccounting
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserAccounting $userAccounting)
    {
        //
    }

    private function getAmountList()
    {
        $list = Cache::get(self::CACHE_KEY_PAYPAL_AMOUNT_LIST);

        if ($list) {
            return $list;
        }

        $fifteenToSeventyFive = range(15, 75, 5);
        $eigthyToHundred = range(80, 100, 10);
        $hundredToTwoHundred = range(125, 200, 25);
        $twoHundredToFiveHundred = range(250, 500, 50);
        $fiveHundredToThousand = range(600, 1000, 100);

        foreach (array_merge($fifteenToSeventyFive, $eigthyToHundred, $hundredToTwoHundred, $twoHundredToFiveHundred, $fiveHundredToThousand) as $value) {
            $list[$value] = trans('accounting.option_amount', ['sum' => $value]);
        }

        Cache::forever(self::CACHE_KEY_PAYPAL_AMOUNT_LIST, $list);

        return $list;
    }

    public function paypal(Request $request)
    {
        if ($request->method() == 'GET') {
            if (\request('success') == 'paypal') {
                request()->session()->flash('success', trans('accounting.message_success_payment'));
            }

            return redirect()->route('accounting.index');
        }

        $this->validate($request, [
            'amount' => 'required|numeric',
        ]);
        return view('user.accounting.paypal', ['amount' => request()->amount]);
    }
}
