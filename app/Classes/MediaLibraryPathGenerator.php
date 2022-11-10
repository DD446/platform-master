<?php
/**
 * User: fabio
 * Date: 04.09.18
 * Time: 09:23
 */

namespace App\Classes;

use App\Models\Page;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaLibraryPathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        if (is_a($media->model, Page::class)) {
            return 'page/' . $media->getKey();
        }

        if (is_a($media->model, User::class)) {
            $username = User::whereUsrId($media->model_id)->value('username');

            return get_user_path($username) . DIRECTORY_SEPARATOR . $media->getKey();
        }

        return $media->getKey();
    }
}
