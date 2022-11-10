<?php

namespace App\Http\Controllers\API;

use App\Classes\AuphonicManager;
use App\Classes\MediaManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuphonicPresetStoreRequest;
use App\Http\Requests\AuphonicPresetUpdateRequest;
use App\Http\Resources\AuphonicPresetCollection;
use App\Http\Resources\AuphonicPresetResource;
use App\Models\Feed;
use App\Models\UserOauth;
use App\Models\WebhookSend;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use podcasthosting\Auphonic\Resource\Production;
use Spatie\WebhookClient\Models\WebhookCall;

class AuphonicPresetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AuphonicPresetCollection
     * @hideFromAPIDocumentation
     */
    public function index()
    {
        $validated = $this->validate(\request(), [
            'feed_id' => ['required', 'string', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ]);

        $presets = collect();
        $client = (new AuphonicManager())->getClient(auth()->user()->approvals, $validated['feed_id']);
        $lists = $client->preset()->getList();

        if ($lists) {
            $lists = new Collection($lists);
            $presets = $lists->map(function($value) {
                return [
                    'id' => $value->uuid,
                    'name' => $value->preset_name,
                ];
            });
        }
        return new AuphonicPresetCollection($presets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function store(AuphonicPresetStoreRequest $request)
    {
        $validated = $request->validated();

        $client = (new AuphonicManager())->getClient(auth()->user()->approvals, $validated['feed_id']);
        $production = new Production();

        $metadata = new Production\Metadata();
        $metadata->setTitle($validated['title']);
        $metadata->setSummary($validated['summary']);
        $metadata->setSubtitle($validated['subtitle']);
        $metadata->setTags($validated['tags']);
        $metadata->setArtist($validated['artist']);
        $metadata->setTrack($validated['track']);
        $metadata->setPublisher($validated['publisher']);
        $metadata->setAlbum($validated['album']);
        $metadata->setUrl($validated['url']);
        $metadata->setLicense($validated['license']);
        $metadata->setLicenseUrl($validated['license_url']);
        $metadata->setGenre($validated['genre']);
        $metadata->setYear($validated['year']);
        //$metadata->setAppendChapters($validated['append_chapters']);

/*        if ($validated['location']) {
            $location = new Production\Metadata\Location();
            $location->setLatitude($validated['location']['latitude']);
            $location->setLongitude($validated['location']['longitude']);
            $metadata->setLocation($location);
        }*/

        $production->setMetadata($metadata);
        //$production->setChapters($validated['chapters']);

        $webhook =  url(AuphonicManager::WEBHOOK_URI);
        $clientProduction = new \podcasthosting\Auphonic\Client\Production();
        $clientProduction->setClient($client);
        $startedProduction = $clientProduction->create($production, $validated['preset'], $webhook);

        //$client->setProduction($clientProduction);
        //$started = $client->production()->create($production, $validated['preset'], $webhook);
        $res = [
            'token' => $client->getToken(),
            'uuid' => $startedProduction->getUuid(),
        ];

        WebhookSend::create([
            'user_id' => auth()->id(),
            'service' => 'auphonic',
            'status' => $startedProduction->status,
            'payload' => $startedProduction,
        ]);

        return response()->json($res);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function show($id)
    {
        $validated = $this->validate(\request(), [
            'feed_id' => ['required', 'string', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ]);

        $client = (new AuphonicManager())->getClient(auth()->user()->approvals, $validated['feed_id']);

        return response()->json($client->preset()->load($id)->getMetadata());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function update(AuphonicPresetUpdateRequest $request, $preset)
    {
        $validated = $request->validated();
        $uuid = $validated['uuid'];
        $client = (new AuphonicManager())->getClient(auth()->user()->approvals, $validated['feed_id']);
        $clientProduction = new \podcasthosting\Auphonic\Client\Production();
        $clientProduction->setClient($client);
        $production = $clientProduction->get($uuid);

        $media = null;
        $mediaManager = new MediaManager(auth()->user());

        foreach($production->getOutputFiles() as $file) {
            $media = $mediaManager->saveAuphonicProduction($client->getToken(), $file->getDownloadUrl());
        }

        return response()->json($media);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @hideFromAPIDocumentation
     */
    public function destroy($id)
    {
        //
    }
}
