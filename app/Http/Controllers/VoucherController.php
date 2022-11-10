<?php

namespace App\Http\Controllers;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Http\Requests\VoucherUpdateRequest;
use App\Models\VoucherRedemption;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
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
        //
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
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(VoucherUpdateRequest $request, $voucher)
    {
        $validated = $request->validated();

        LaravelMatomoTracker::queueEvent('voucher', 'reedeem', $validated['voucher'], auth()->user()->username);

        $oVoucher = Voucher::where('voucher_code', '=', Str::lower($validated['voucher']))->first();

        if (VoucherRedemption::owner()->where('voucher_id', '=', $oVoucher->id)->count() >= $oVoucher->voucher_action->usage_limit) {
            throw ValidationException::withMessages(['voucher_code' => trans('accounting.validation_error_voucher_already_used', ['name' => $voucher])]);
        }

        $benefit = trans_choice('accounting.voucher_action_type', $oVoucher->voucher_action->type, ['units' => $oVoucher->voucher_action->units, 'unitLabel' => $oVoucher->voucher_action->getUnitLabel()]);
        $msg = trans('accounting.message_success_applied_voucher', ['name' => $voucher, 'benefit' => $benefit]);

        try {
            $oVoucher->redeem(auth()->user());
        } catch (\Exception $e) {
            Log::error("ERRO: User " . auth()->user()->username . ": Trying to reedeem voucher: " . $e->getMessage());
            //$msg = trans('accounting.message_error_applying_voucher');
            throw ValidationException::withMessages(['voucher_code' => $e->getMessage()]);
        }

        return response()->json($msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        //
    }
}
