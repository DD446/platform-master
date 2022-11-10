<?php

namespace App\Http\Controllers\API;

use App\Classes\MediaManager;
use App\Events\UpdateShowGuid;
use App\Http\Controllers\Controller;
use App\Http\Resources\MediaCollection;
use App\Models\Models\Media;
use App\Models\User;
use App\Rules\StorageSpaceAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use JamesHeinrich\GetID3\GetID3;
use Khill\Duration\Duration;

class MediaController extends Controller
{
    /**
     * List files
     *
     * Gets a list of the user´s uploaded (media) files.
     *
     * @group Media
     * @apiResourceModel MediaCollection
     * @queryParam sort_by string Sort criterium (Allowed values: 'name', 'size', 'created'. Default: 'name'). Example: name
     * @queryParam sort_dir string Sort order ('asc', 'desc'. Default: 'desc'). Example: desc
     * @queryParam filter string Search for a file (name). Example: "kreativ"
     * @queryParam strict bool Used to limit search results to exact matches. (Default: 0) Example: 1
     * @queryParam page string[] Used for pagination. You can pass "number" and "size". Default: page[number] =1, page[size] = 30 *Attention* The `Example request` is not correct for this parameter. It should look like this: page[number]=5&page[size]=30 Example: page[number]=5&page[size]=30
     * @responseFile resources/responses/medias.get.json
     *
     * @return MediaCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (Gate::forUser(auth()->user())->denies('viewMedia')) {
            abort(403);
        }

        $request = request();
        $validated = $this->validate($request, [
            'sort_by' => ['nullable', 'string', 'in:name,size,created'],
            'sort_dir' => ['nullable', 'string'],
            'filter' => ['nullable', 'string'],
            'strict' => ['nullable', 'numeric'],
            'page.number' => ['nullable', 'numeric'],
            'page.size' => ['nullable', 'numeric'],
        ], [], [
            'sort_by' => 'Sortier-Kriterium',
            'sort_desc' => 'Sortierung absteigend',
            'filter' => 'Filter',
            'page.number' => 'Seite',
            'page.size' => 'Anzahl Ergebnisse',
            'strict' => 'Genaue Treffer',
        ]);

        $media = auth()->user()->getFiles($validated['sort_by'], strtolower($validated['sort_dir']) != 'asc', $validated['filter'], $validated['page']['number'], $validated['page']['size'], $validated['strict']);

        array_walk($media['items'], function($file) {
            unset($file->intern);
            unset($file->path);
            return $file;
        });

        return response()->json($media);
    }

    /**
     * Get file
     *
     * Gets details for a media file.
     *
     * @group Media
     * @urlParam media_id int required ID of the media file. Example: 123456789
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(int $media_id)
    {
        $file = auth()->user()->getFile($media_id);

        if (!$file || !is_array($file)) {
            abort(404, trans('mediamanager.message_error_no_file_found', ['id' => $media_id]));
        }

        $oInfo          = new \finfo(\FILEINFO_NONE |  \FILEINFO_CONTINUE |  \FILEINFO_PRESERVE_ATIME);
        if ($oInfo) {
            $file['info'] = Str::before($oInfo->file($file['path']), '\\'); // Details on file
        }

        $oMime          = new \finfo(\FILEINFO_MIME_TYPE |  \FILEINFO_CONTINUE |  \FILEINFO_PRESERVE_ATIME);

        if ($oMime) {
            $file['mime'] = Str::before($oMime->file($file['path']), '\\');
        }

        if (Str::startsWith($file["mime"], 'image/')) {
            list($width, $height) = getimagesize($file["path"]);
            $file["width"] = $width;
            $file["height"] = $height;
            try {
                $file["isLogo"] = is_logo($file, false);
            } catch (\Exception $e) {
                $file["isLogo"] = false;
            }
        } elseif (Str::startsWith($file["mime"], 'audio/')) {
            $file['duration'] = get_duration(auth()->user()->username, $media_id);
        }

        unset($file['intern']);
        unset($file['path']);

        return response()->json($file);
    }

    /**
     * Upload file
     *
     * Stores a file in the media manager.
     *
     * @group Media
     * @bodyParam media file required The media file to upload.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'media' => ['file', 'required', new StorageSpaceAvailable()],
        ], [], [
            'media' => 'Datei',
        ]);

        $mediaManager = new MediaManager();
        $aRes = $mediaManager->saveFile($request->file('media'));

        return response()->json($aRes)->setStatusCode($aRes['statusCode'], $aRes['statusText']);
    }

    /**
     * Replace file
     *
     * Upload a new file to replace an existing one.
     * This automatically renews the GUID for episodes
     * where this file is used and refreshes the feed.
     *
     * @group Media
     * @urlParam media_id int required ID of the media file to be replaced. Example: 123456789
     * @bodyParam media file required The media file to upload to replace the existing file.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request, $media_id)
    {
        $this->validate($request, [
            'media' => 'file|required',
        ], [], [
            'media' => 'Datei',
        ]);

        /** @var User $user */
        $user = auth()->user();
        $file = $user->getFile($media_id);

        if (!$file) {
            abort(404, trans('mediamanager.message_error_no_file_found', ['id' => $media_id]));
        }

        $dir = $media_id;
        $group = File::basename(File::dirname($file['path']));
        // File is in a subfolder
        if ($group != $media_id) {
            $dir .= DIRECTORY_SEPARATOR . $group;
        }
        $uploadPath = get_user_media_path($user->username) . DIRECTORY_SEPARATOR . $dir;
        $path = $request->media->storeAs($uploadPath, $file['name']);
        $msg = ['success' => trans('mediamanager.message_success_file_replacement', ['name' => $file['name']])];

        event(new UpdateShowGuid($user->username, $media_id));

        if (!$path || $path != $uploadPath . DIRECTORY_SEPARATOR . $file['name']) {
            $msg = ['error' => "Die Datei konnte nicht ersetzt werden."];
            return response()->json($msg)->setStatusCode(500, trans('errors.file_upload'));
        }

        $aRes = [
            'message' => $msg,
            'id' => $media_id,
            'file' => get_file($user->username, $media_id),
        ];
        return response()->json($aRes);
    }

    /**
     * Delete file
     *
     * Remove a file from the media manager.
     *
     * @group Media
     * @urlParam media_id int required ID of the media file to be deleted. Example: 123456789
     *
     * @throws \Exception
     */
    public function destroy($media_id)
    {
        /** @var User $user */
        $user = auth()->user();

        try {
            if (!$user->deleteFile($media_id)) {
                throw new \Exception(trans('mediamanager.failure_delete_media'));
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json([
            'message' => trans('mediamanager.success_delete_media')
        ]);
    }
}
