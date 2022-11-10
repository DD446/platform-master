<?php
/**
 * User: fabio
 * Date: 05.07.18
 * Time: 13:58
 */

namespace App\Models;


use Illuminate\Support\Facades\Log;

class UserData
{
    const MEDIA_STORAGE_DIR = 'hostingstorage/mediafiles';
    const MEDIA_PUBLIC_DIR = 'publicstorage';
    const MEDIA_FEED_DIR = 'feedstorage';
    const MEDIA_DIRECT_DIR  = 'download';
    const MEDIA_EMBED_DIR   = 'embed';
    const MEDIA_SOCIAL_DIR  = 'social';
    const LOGOS_PUBLIC_DIR  = 'logos';
    const FEED_MEDIA_DIR  = 'media';

    /**
     * @param $mediaId
     * @param $username
     * @param  bool  $withPath
     * @return string|null
     * @throws \Exception
     */
    public static function get($mediaId, $username, $withPath = true)
    {
        if (!$mediaId || !$username || strlen($username) < 3) {
            return null;
        }

        $dir            = null;
        $path           = get_user_storage_path($username) . DIRECTORY_SEPARATOR . $mediaId . DIRECTORY_SEPARATOR;

        try {
            $iterator   = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $value) {
                if ($value->isFile()) {
                    if ($withPath) {
                        return $path . $dir . $value->getFilename();
                    } else {
                        return $value->getFilename();
                    }
                } elseif ($value->isDir()) {
                    if (substr($value->getFilename(), 0, 1) != '.') {
                        $dir .= $value->getFilename() . DIRECTORY_SEPARATOR;
                    }
                }
            }
        } catch (\UnexpectedValueException $e) {
            Log::debug($e->getTraceAsString());
            Log::debug("Could not get file path for mediaId ($mediaId), username ($username) withPath ($withPath): "
                . $e->getMessage());
            return null;
        }

        return null;
    }
}
