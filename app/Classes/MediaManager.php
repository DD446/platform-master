<?php
/**
 * User: fabio
 * Date: 10.11.20
 * Time: 21:59
 */

namespace App\Classes;


use App\Events\FileSavedEvent;
use App\Events\UpdateShowGuid;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaManager
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @param  \Illuminate\Http\UploadedFile  $getFile
     * @return array
     * @throws \Exception
     */
    public function saveFile(\Illuminate\Http\UploadedFile $file): array
    {
        $statusCode = 200;

        $id = time();
        $uploadPath = get_user_media_path($this->user->username) . DIRECTORY_SEPARATOR . $id;

        while(File::isDirectory(storage_path($uploadPath))) {
            $id = time();
            $uploadPath = File::basename($uploadPath) . DIRECTORY_SEPARATOR . $id;
        }
        $originalName = $file->getClientOriginalName();
        $filename = $this->user->getUniqueFilename($originalName);
        $path = $file->storeAs($uploadPath, $filename);

        if ($originalName != $filename) {
            $trans = trans('mediamanager.success_upload_media_new_name',
                ['originalName' => $originalName, 'filename' => $filename]);
        } else {
            $trans = trans('mediamanager.success_upload_media', ['originalName' => $originalName]);
        }
        $msg = ['success' => $trans];

        if ($path != $uploadPath . DIRECTORY_SEPARATOR . $filename) {
            $trans = trans('errors.file_upload', ['filename' => $originalName]);
            $msg = ['error' => $trans];
            $statusCode = 500;
            //throw new \Exception($msg);

            return [
                'message' => $msg,
                'statusCode' => $statusCode,
                'statusText' =>  $trans,
            ];
        }

        $_file = get_file($this->user->username, $id);
        event(new FileSavedEvent($this->user, $_file));

        return [
            'message' => $msg,
            'statusCode' => $statusCode,
            'statusText' =>  $trans,
            'id' => $id,
            'file' => $_file,
        ];
    }

    /**
     * @param  string  $url
     * @param  false  $isImport
     * @return array
     * @throws \Exception
     */
    public function saveFileFromUrl(string $url, $isImport = false)
    {
        do {
            $id = time();
        } while(File::isDirectory(storage_path(get_user_media_path($this->user->username)) . DIRECTORY_SEPARATOR . $id));

        $uploadPath = get_user_media_path($this->user->username) . DIRECTORY_SEPARATOR . $id;

        while(File::isDirectory(storage_path($uploadPath))) {
            $id = time();
            $uploadPath = File::basename($uploadPath) . DIRECTORY_SEPARATOR . $id;
        }

        $ext = pathinfo(parse_url($url)['path'])['extension'];
        $name = pathinfo(parse_url($url)['path'])['filename'];
        $originalName = $name . '.' . $ext;
        $filename = $this->user->getUniqueFilename($originalName);

        File::makeDirectory(storage_path($uploadPath), 0755, true);

        $opts = [
            'http'=> [
                'method' => "GET",
                'user_agent' => 'podcaster.de FileImporter/0.2',
            ]
        ];

        if (!$this->copy($url, storage_path($uploadPath . '/' . $filename))) {
        //$context = stream_context_create($opts);
        //if (!copy($url, storage_path($uploadPath . '/' . $filename))) {
        //if (!File::put(storage_path($uploadPath . '/' . $filename), file_get_contents($url, false, $context))) {
            $trans = trans('errors.file_upload', ['filename' => $originalName]);
            $msg = ['error' => $trans];
            $statusCode = 500;

            return [
                'message' => $msg,
                'statusCode' => $statusCode,
                'statusText' =>  $trans,
            ];
        }

        $_file = get_file($this->user->username, $id);

        if (!$_file) {
            Log::error("User '{$this->user->username}': Expected file with id '{$id}' and filename '{$originalName}' not found.");
            $trans = trans('errors.file_upload', ['filename' => $originalName]);
            $msg = ['error' => $trans];
            $statusCode = 500;

            return [
                'message' => $msg,
                'statusCode' => $statusCode,
                'statusText' =>  $trans,
            ];
        }

        event(new FileSavedEvent($this->user, $_file, $isImport));

        if ($originalName != $filename) {
            $trans = trans('mediamanager.success_upload_media_new_name',
                ['originalName' => $originalName, 'filename' => $filename]);
        } else {
            $trans = trans('mediamanager.success_upload_media', ['originalName' => $originalName]);
        }

        return [
            'message' => ['success' => $trans],
            'statusCode' => 200,
            'statusText' => $trans,
            'id' => $id,
            'file' => $_file,
        ];
    }

    /**
     * @param  string  $username
     * @return int
     * @throws \Exception
     */
    public function count()
    {
        return count(glob(storage_path(get_user_media_path($this->user->username) . '/*'), GLOB_ONLYDIR));
    }

    /**
     * @param  string  $url
     * @param  string  $dest
     * @return bool
     */
    public function copy(string $url, string $dest)
    {
        $fp = fopen($dest, 'w+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_USERAGENT, 'podcaster.de FileImporter/0.3');
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return true;
    }

    /**
     * @param $id
     * @param  string  $label
     * @param  string|null  $group
     * @return bool
     * @throws \Exception
     */
    public function rename($id, string $label, ?string $group = null)
    {
        $file = get_file($this->user->username, $id);

        if ($file['name'] == $label
            && $file['cat'] == $group) {
            // Name stays the same
            return true;
        }

        if (is_null($group) && $file['name'] != $label && !$this->user->isUniqueFilename($label)) {
            throw new \Exception(trans('errors.file_rename_failure_not_unique'));
        }

        if ($file['name'] != $label) {
            $label = $this->user->getUniqueFilename($label);
        }
        // Path includes group if group is set
        $destination = File::dirname($file['path']);

        if (!is_null($group)) {
            if ($file['cat']) {
                $destination = File::dirname($destination);
            }

            if ($group != User::CATEGORY_DEFAULT) {
                $destination .= DIRECTORY_SEPARATOR . $group;
            }
        } else {
            // File was in a group
            if (File::basename($destination) != $id) {
                $destination = File::dirname($destination);
            }
        }

        $directory = $destination;
        $destination .= DIRECTORY_SEPARATOR . $label;

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory);
        }

        $res = File::move($file['path'], $destination);

        if ($res && $file['cat'] && $file['cat'] != $group) {
            // Remove old directory aka group
            File::deleteDirectory(File::dirname($file['path']));
        }

        if (!$this->user->link($destination,UserData::MEDIA_DIRECT_DIR)) {
            Log::error("User '{$this->user->username}': Creating link for destination $destination failed.");
        }

        // Relink public links for feed and direct downloads to new destination
        foreach ($this->user->getLinks($file['path']) as $link) {
            unlink($link);
            $dir = File::dirname($link);
            $link = $dir . DIRECTORY_SEPARATOR . $label;

            if (!is_link($link)) {
                File::link($destination, $link);
            }

            // The file is used in a feed
            $base = File::basename($dir);

            if ($base != 'download') {
                event(new UpdateShowGuid($this->user->username, $id));
            }
        }

        return $res;
    }

    /**
     * @param  string  $token
     * @param  string  $url
     * @return array
     * @throws \Exception
     */
    public function saveAuphonicProduction(string $token, string $url): array
    {
        $statusCode = 200;

        $id = time();
        $uploadPath = get_user_media_path($this->user->username) . DIRECTORY_SEPARATOR . $id;

        while(File::isDirectory(storage_path($uploadPath))) {
            $id = time();
            $uploadPath = File::basename($uploadPath) . DIRECTORY_SEPARATOR . $id;
        }

        $res = Http::withToken($token)->get($url);

        if (!$res->ok()) {
            throw new \Exception("ERROR: Could not download file from `$url`.");
        }
        $originalName = basename(parse_url($url, PHP_URL_PATH));
        $filename = $this->user->getUniqueFilename($originalName);

        File::makeDirectory(storage_path($uploadPath), 0755, true);

        if (!File::put(storage_path($uploadPath . '/' . $filename), $res->body())) {
            $trans = trans('errors.file_upload', ['filename' => $originalName]);
            $msg = ['error' => $trans];
            $statusCode = 500;

            return [
                'message' => $msg,
                'statusCode' => $statusCode,
                'statusText' =>  $trans,
            ];
        }

        if ($originalName != $filename) {
            $trans = trans('mediamanager.success_upload_media_new_name',
                ['originalName' => $originalName, 'filename' => $filename]);
        } else {
            $trans = trans('mediamanager.success_upload_media', ['originalName' => $originalName]);
        }
        $msg = ['success' => $trans];

        $_file = get_file($this->user->username, $id);
        event(new FileSavedEvent($this->user, $_file));

        return [
            'message' => $msg,
            'statusCode' => $statusCode,
            'statusText' =>  $trans,
            'id' => $id,
            'file' => $_file,
        ];
    }
}
