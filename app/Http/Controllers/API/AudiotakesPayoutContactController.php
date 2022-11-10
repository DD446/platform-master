<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAudiotakesPayoutContactRequest;
use App\Http\Requests\UpdateAudiotakesPayoutContactRequest;
use App\Models\AudiotakesPayoutContact;
use Illuminate\Http\Request;

class AudiotakesPayoutContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function index()
    {
        return AudiotakesPayoutContact::owner()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function store(StoreAudiotakesPayoutContactRequest $request)
    {
        $validated = $request->validated();

        if ($validated['goal_type'] === 'bank') {
            unset($validated['paypal']);
        } else {
            unset($validated['iban']);
        }

        $res = AudiotakesPayoutContact::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'country' => $validated['country'],
            'paypal' => $validated['paypal'] ?? null,
            'bank_account_owner' => $validated['bank_account_owner'] ?? null,
            'iban' => $validated['iban'] ?? null,
            'tax_id' => $validated['tax_id'] ?: null,
            'vat_id' => $validated['vat_id'] ?: null,
        ]);

        if (!$res) {
            throw new \Exception(trans('audiotakes.error_message_creating_payout_contact'));
        }

        $msg = ['message' => trans('audiotakes.success_message_creating_payout_contact')];

        return response()->json($msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AudiotakesPayoutContact  $audiotakesPayoutContact
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function show(AudiotakesPayoutContact $audiotakesPayoutContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AudiotakesPayoutContact  $audiotakesPayoutContact
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function update(UpdateAudiotakesPayoutContactRequest $request, AudiotakesPayoutContact $audiotakesPayoutContact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AudiotakesPayoutContact  $audiotakesPayoutContact
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function destroy(AudiotakesPayoutContact $audiotakesPayoutContact)
    {
        //
    }
}
