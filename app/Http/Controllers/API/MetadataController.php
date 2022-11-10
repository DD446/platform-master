<?php

namespace App\Http\Controllers\API;

use App\Classes\FileTagManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\MetadataRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class MetadataController extends Controller
{
    /**
     * Get metadata
     *
     * Retrieves the metadata of a mediafile
     *
     * @group Media
     * @urlParam media_id int required ID of the media file. Example: 123456789
     * @hideFromAPIDocumentation
     */
    public function show($id)
    {
        request()->merge(['id' => $id]);
        $validated = $this->validate(request(), [
            'id' => 'required|numeric',
            'type' => ['nullable', 'in:' . FileTagManager::DATA_TYPE_COMMON . ',' . FileTagManager::DATA_TYPE_CHAPTERS]
        ]);

        $file = get_file(auth()->user()->username, $id);
        $data = [];

        if ($file) {
            $ftm = new FileTagManager($file);
            $data = $ftm->getData($validated['type']);
        }

        array_walk_recursive($data, function(&$item) {
            $item = html_entity_decode($item);
        });

        return response()->json($data);
    }

    /**
     * Update metadata
     *
     * Change a mediafile's metadata
     *
     * @group Media
     * @urlParam media_id int required ID of the media file. Example: 123456789
     * @hideFromAPIDocumentation
     */
    public function update(MetadataRequest $request, $id)
    {
        $data = $request->toArray();

/*        array_walk($data, function (&$item, $key) {
            if (is_array($item) && count($item) < 2) {
                $item = $item[0];
            }
            if (!$item) {
                unset($item);
            }
        });*/
        Log::debug($data);

        $file = get_file(auth()->user()->username, $id);

        if ($file) {
            $ftm = new FileTagManager($file);
        }
        $response = $ftm->write($data, true, true);

        return response()->json($response);
    }
}
