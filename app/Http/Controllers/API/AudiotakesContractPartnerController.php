<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AudiotakesContractPartnerStoreRequest;
use App\Models\AudiotakesContractPartner;
use Illuminate\Http\Request;

class AudiotakesContractPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function index()
    {
        return response()->json(AudiotakesContractPartner::owner()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function store(AudiotakesContractPartnerStoreRequest $request)
    {
        $validated = $request->validated();
        $data = $validated['user'];
        $data['user_id'] = auth()->id();

        $acp = AudiotakesContractPartner::create($data);

        if (!$acp) {
            throw new \Exception(trans('audiotakes.error_message_creating_contract_partner'));
        }

        $msg = [
            'message' => trans('audiotakes.success_message_creating_contract_partner'),
            'id' => $acp->id,
        ];

        return response()->json($msg);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AudiotakesContractPartner  $audiotakesContractPartner
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function update(Request $request, AudiotakesContractPartner $audiotakesContractPartner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AudiotakesContractPartner  $audiotakesContractPartner
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function destroy(AudiotakesContractPartner $audiotakesContractPartner)
    {
        //
    }
}
