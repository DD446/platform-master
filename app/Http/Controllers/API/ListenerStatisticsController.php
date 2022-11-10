<?php

namespace App\Http\Controllers\API;

use App\Classes\Statistics\CombinedStatistics;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListenerStatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $username = auth()->user()->username;
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            'range' => ['required', 'array:df,dt'], // TODO: Laravel 9: required_array_keys:df,dt
            'range.*' => ['required', 'date'],
            'feed' => ['nullable', 'exists:App\Models\Feed,feed_id,username,' . $username],
            'episode' => ['nullable', 'string'],
        ]);

        (new CombinedStatistics($username))->getListeners($validated['range']);
        $count = [
            'now' => 0,
            'prev' => 0,
            'change' => null,
        ];

        return response()->json($count);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
}
