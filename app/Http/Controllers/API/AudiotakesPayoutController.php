<?php

namespace App\Http\Controllers\API;

use App\Classes\AudiotakesManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\AudiotakesPayoutRequest;
use App\Models\AudiotakesContract;
use Illuminate\Http\Request;

class AudiotakesPayoutController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function store(AudiotakesPayoutRequest $request)
    {
        $validated = $request->validated();

        if ($validated['payoutOption'] == 'funds') {
            $funds = AudiotakesManager::transferFunds(auth()->user(), $validated['payoutFunds']);
            $msg =  trans('audiotakes.success_transfer_funds', ['amount' => $validated['payoutFunds'], 'currency' => AudiotakesContract::DEFAULT_CURRENCY ]); // TODO: I18N currency
        } else {
            $funds = AudiotakesManager::payoutFunds(auth()->user(), $validated['payoutFunds'], $validated['payoutGoal'], $validated['audiotakes_contract_partner_id']);
            $msg =  trans('audiotakes.success_payout_funds', ['amount' => $validated['payoutFunds'], 'currency' => AudiotakesContract::DEFAULT_CURRENCY ]);
        }

        return response()->json([
            'message' => $msg,
            'funds' => $funds,
        ]);
    }
}
