<?php
/**
 * User: fabio
 * Date: 09.07.18
 * Time: 09:30
 */

use App\Classes\FeedGeneratorManager;
use App\Classes\ShowManager;
use App\Models\PlayerConfig;
use App\Models\Space;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Classes\Activity;
use App\Classes\Domain;
use App\Classes\DomainManager;
use App\Models\Feed;
use App\Models\MemberQueue;
use App\Models\Package;
use App\Models\Show;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserData;
use App\Models\UserExtra;
use JamesHeinrich\GetID3\GetID3;
use Khill\Duration\Duration;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

define('IANA_URL', 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt');

if (! function_exists('get_user_path')) {
    /**
     * @param $username
     * @return string
     * @throws Exception
     */
    function get_user_path(string $username): string
    {
        if (!$username) {
            throw  new Exception(trans('errors.username_missing'));
        }
        $username = strtolower($username);

        return DIRECTORY_SEPARATOR . $username[0] . DIRECTORY_SEPARATOR . $username[1] .
            DIRECTORY_SEPARATOR . $username[2] . DIRECTORY_SEPARATOR . $username;
    }
}

if (! function_exists('get_user_media_path')) {
    /**
     * @param $username
     * @return string
     * @throws Exception
     */
    function get_user_media_path(string $username): string
    {
        if (!$username) {
            throw new \Exception(trans('errors.username_missing'));
        }

        return UserData::MEDIA_STORAGE_DIR . get_user_path($username);
    }
}

if (! function_exists('get_user_feed_path')) {
    /**
     * @param $username
     * @return string
     * @throws Exception
     */
    function get_user_feed_path(string $username): string
    {
        if (!$username) {
            throw new \Exception(trans('errors.username_missing'));
        }

        return UserData::MEDIA_FEED_DIR . get_user_path($username);
    }
}

if (! function_exists('get_user_feed_filename')) {
    /**
     * @param $username
     * @return string
     * @throws Exception
     */
    function get_user_feed_filename(string $username, string $feedId): string
    {
        if (!$username) {
            throw new \Exception(trans('errors.username_missing'));
        }

        return storage_path(get_user_feed_path($username)) . DIRECTORY_SEPARATOR . $feedId . Feed::DEFAULT_EXTENSION;
    }
}

if (! function_exists('get_user_public_path')) {
    /**
     * @param $username
     * @return string
     * @throws Exception
     */
    function get_user_public_path(string $username): string
    {
        if (!$username) {
            throw new \Exception(trans('errors.username_missing'));
        }

        return UserData::MEDIA_PUBLIC_DIR . get_user_path($username);
    }
}

if (! function_exists('get_user_storage_path')) {
    /**
     * @param $username
     * @return string
     * @throws Exception
     */
    function get_user_storage_path($username)
    {
        $path = storage_path(get_user_media_path($username));

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }
}

if (! function_exists('get_user_publish_path')) {
    /**
     * @param  string  $username
     * @return string
     */
    function get_publish_path(string $username, string $filename, ?string $publicDir = null, ?string $feedId = null): string
    {
        $feedDir = ((!is_null($feedId)) ? $feedId . DIRECTORY_SEPARATOR : $feedId);
        $username = strtolower($username);

        return storage_path(get_user_public_path($username) . DIRECTORY_SEPARATOR . $feedDir . $publicDir . DIRECTORY_SEPARATOR
            . $filename);
    }
}

if (! function_exists('get_file')) {
    /**
     * @param  string  $username
     * @param  string  $id
     * @return array|null
     * @throws Exception
     */
    function get_file(string $username, string $id): ?array
    {
        $path = storage_path(get_user_media_path($username)) . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
        $group = null;

        try {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $value) {
                if ($value->isDir() && !in_array($value->getBasename(), ['.', '..'])) {
                    $group = $value->getBasename();
                }
                if ($value->isFile()) {
                    $mimetype = get_mimetype_by_extension($value->getExtension());
                    return [
                        'id'    => $id,
                        'name'  => $value->getBasename(), // File name
                        'path'  => $value->getPathname(), // Complete path and file name
                        'byte'  => $value->getSize(), // Size in bytes
                        'size'  => get_size_readable($value->getSize()), // Formatted size
                        'time'  => date("d.m.Y H:i:s", $id), // Date of upload
                        'last'  => date("d.m.Y H:i:s", $value->getMTime()), // Last modification
                        'cat' => $group,
                        'intern' => get_intern_uri($id), // Prevent calls on file being tracked through statistics
                        'mimetype' => $mimetype,
                        'type' => Str::before($mimetype, '/'),
                        'extension' => File::extension($value->getPathname()),
                    ];
                }
            }
        } catch (\UnexpectedValueException $e) {
            return null;
        }
        return null;
    }
}

if (! function_exists('get_file_id_by_filename')) {
    /**
     * @param  string  $username
     * @param  string  $id
     * @return array|null
     * @throws Exception
     */
    function get_file_id_by_filename(string $username, string $filename): ?int
    {
        $basePath = storage_path(get_user_media_path($username));
        $res = File::glob("{$basePath}/*/{$filename}");

        if ($res && count($res) == 1) {
            return Str::between($res[0], $basePath. '/', '/' . $filename);
        }

        // Grouped file (lives in subfolder)
        $res = File::glob("{$basePath}/*/*/{$filename}");

        if ($res && count($res) == 1) {
            return Str::before(Str::between($res[0], $basePath. '/', '/' . $filename), '/');
        }

        throw new \Illuminate\Contracts\Filesystem\FileNotFoundException();
    }
}

if (! function_exists('get_feed_uri')) {
    /**
     * @param  string  $feedId
     * @param  array  $domain
     * @param  string  $type
     * @return string
     */
    function get_feed_uri(string $feedId, array $domain, string $type = 'rss'): string
    {
/*        $uri = new \Nyholm\Psr7\Uri();
        $uri->withScheme($domain['protocol']);
        $uri->withPath($feedId . '.' . $type);
        $uri->withHost($domain['subdomain'] . '.' . $domain['tld']);

        return $uri;*/

        return 'https://' . $domain['subdomain'] . '.' . $domain['tld'] . '/' . $feedId . '.' . $type;
        //return $domain['protocol'] . '://' . $domain['subdomain'] . '.' . $domain['tld'] . '/' . $feedId . '.' . $type;
    }
}

if (! function_exists('get_blog_uri')) {
    /**
     * @param  array  $domain
     * @param  string|null  $protocol
     * @return string
     */
    function get_blog_uri(array $domain, ?string $protocol = null): string
    {
        return $protocol ?? $domain['protocol'] . '://' . $domain['subdomain'] . '.' . $domain['tld'] . '/';
    }
}

if (! function_exists('get_enclosure_uri')) {
    /**
     * @param  string  $feedId
     * @param  string  $file
     * @param  array  $domain
     * @param  string|null  $protocol
     * @return string
     */
    function get_enclosure_uri(string $feedId, string $file, array $domain, ?string $protocol = null, ?string $source = null): string
    {
        $link = $protocol ?? $domain['protocol'] . '://' . $domain['subdomain'] . '.' . $domain['tld'] . '/' . $feedId . '/media/' . $file;

        if (!is_null($source)) {
            $link .= '?origin=' . $source;
        }

        return $link;
    }
}

if (! function_exists('get_image_uri')) {
    /**
     * @param  string  $feedId
     * @param  string  $file
     * @param  array  $domain
     * @param  string|null  $protocol
     * @return string
     */
    function get_image_uri(string $feedId, string $file, array $domain, ?string $protocol = null): string
    {
        return $protocol ?? $domain['protocol'] . '://' . $domain['subdomain'] . '.' . $domain['tld'] . '/' . $feedId . '/logos/' . $file;
    }
}

if (! function_exists('get_direct_uri')) {

    /**
     * @param  string  $username
     * @param  string  $type
     * @param  string|null  $file
     * @return string
     */
    function get_direct_uri(string $username, string $type = 'download', ?string $file = null, ?string $source = null): string
    {
        $link = 'https://' . Str::lower($username) . '.' . Str::lower(config('app.domain')) . '/' . $type . '/' . $file;

        if (!is_null($source)) {
            $link .= '?origin=' . $source;
        }

        return $link;
    }
}

if (! function_exists('get_intern_uri')) {

    /**
     * @param  string  $id
     * @return string
     */
    function get_intern_uri(string $id): string
    {
        return config('app.url') . '/mediathek/' . $id;
    }
}

if (! function_exists('is_logo')) {

    function is_logo(array $file, $throw = false): bool
    {
        $dimensions = getimagesize($file['path']);

        if (!$dimensions) {
            throw new \Exception(trans('feeds.error_channel_image_not_readable', ['id' => $file['id'], 'name' => $file['name']]));
        }

        // Extension
        $ext = File::extension($file['name']);

        if (!$ext) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_missing_extension'));
            }
            return false;
        }

        if (!in_array($ext, ['png', 'jpg'])) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_wrong_extension'));
            }
            return false;
        }

        // Square
        if ($dimensions[0] != $dimensions[1]) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_not_square', ['height' => $dimensions[0], 'width' => $dimensions[1]]));
            }
            return false;
        }

        // MinimumSize
        if ($dimensions[0] < 1400) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_too_small', ['height' => $dimensions[0]]));
            }
            return false;
        }

        // MaximumSize
        if ($dimensions[0] > 3000) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_too_big', ['height' => $dimensions[0]]));
            }
            return false;
        }

        // Size
        if ($file['byte'] > 3145728) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_filesize_too_big', ['filesizeFormatted' => get_size_readable($file['byte'])]));
            }
            return false;
        }

        // Type
        if (!in_array($dimensions['mime'], ['image/png', 'image/jpeg'])
            || !in_array($dimensions[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_wrong_type', ['type' => $dimensions[2]]));
            }
            return false;
        }

        // Colorspace
        if (isset($dimensions['channels']) && $dimensions['channels'] != 3) {
            if ($throw) {
                throw new \Exception(trans('feeds.error_channel_image_wrong_colorspace'));
            }
            return false;
        }

        // BackgroundColor
        if ($dimensions['mime'] == 'image/png') {
            if (is_alpha_png($file['path'])) {
                if (has_transparency($file['path'])) {
                    if ($throw) {
                        throw new \Exception(trans('feeds.error_channel_image_transparent_background'));
                    }
                    return false;
                }
            }
        }

        return true;
    }
}

if (! function_exists('is_logo_spatie')) {

    function is_logo_spatie(Media $file): bool
    {
        $dimensions = getimagesize($file->getPath());

        // Extension
        $ext = File::extension($file->name);

        if (!in_array($ext, ['png', 'jpg'])) {
            return false;
        }

        // Square
        if ($dimensions[0] != $dimensions[1]) {
            return false;
        }

        // MinimumSize
        if ($dimensions[0] < 1400) {
            return false;
        }

        // MaximumSize
        if ($dimensions[0] > 3000) {
            return false;
        }

        // Size
        if ($file->size > 512000) {
            return false;
        }

        // Type
        if (!in_array($dimensions['mime'], ['image/png', 'image/jpeg'])
            || !in_array($dimensions[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            return false;
        }

        // Colorspace
        if (isset($dimensions['channels']) && $dimensions['channels'] != 3) {
            return false;
        }

        // BackgroundColor
        if (is_alpha_png($file->getPath())) {
            if (has_transparency($file->getPath())) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('is_alpha_png')) {
    function is_alpha_png($image): bool
    {
        return (ord(@file_get_contents($image, null, null, 25, 1)) == 6);
    }
}

if (! function_exists('has_transparency')) {
    /**
     * Estimates, if image has pixels with transparency. It shrinks image to 64 times smaller
     * size, if necessary, and searches for the first pixel with non-zero alpha byte.
     * If image has 1% opacity, it will be detected. If any block of 8x8 pixels has at least
     * one semi-opaque pixel, the block will trigger positive result. There are still cases,
     * where image with hardly noticeable transparency will be reported as non-transparent,
     * but it's almost always safe to fill such image with monotonic background.
     *
     * Icons with size <= 64x64 (or having square <= 4096 pixels) are fully scanned with
     * absolutely reliable result.
     *
     * @param $file
     * @return bool
     */
    function has_transparency(string $file): bool
    {
        $image = imagecreatefrompng($file);

        if (!($image)) {
            throw new \InvalidArgumentException("Image resource expected. Got: " . gettype($image) . " for file {$file}");
        }

        $shrinkFactor      = 64.0;
        $minSquareToShrink = 64.0 * 64.0;

        $width  = imagesx($image);
        $height = imagesy($image);
        $square = $width * $height;

        if ($square <= $minSquareToShrink) {
            [$thumb, $thumbWidth, $thumbHeight] = [$image, $width, $height];
        } else {
            $thumbSquare = $square / $shrinkFactor;
            $thumbWidth  = (int) round($width / sqrt($shrinkFactor));
            $thumbWidth < 1 and $thumbWidth = 1;
            $thumbHeight = (int) round($thumbSquare / $thumbWidth);
            $thumb       = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagealphablending($thumb, false);
            imagecopyresized($thumb, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
        }

        for ($i = 0; $i < $thumbWidth; $i++) {
            for ($j = 0; $j < $thumbHeight; $j++) {
                if (imagecolorat($thumb, $i, $j) & 0x7F000000) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('get_size_readable')) {

    /**
     *
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function get_size_readable($bytes, $precision = 2, $base = 1024)
    {
        $units  = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes  = max($bytes, 0);
        $pow    = floor(($bytes ? log($bytes) : 0) / log($base));
        $pow    = min($pow, count($units) - 1);
        $bytes  /= pow($base, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}


if (! function_exists('make_url_safe')) {

    function make_url_safe(string $file, $allowDoublePoint = false): string
    {
        // Remove any trailing dots, as those aren't ever valid file names.
        $file = rtrim($file, '.');

        $replace = '_';

        $from = ["Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž", '´'];
        $to   = ["A", "A", "A", "Ae", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "ae", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "Oe", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "oe", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "Ue", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "ss", "t", "t", "b", "u", "u", "u", "ue", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z", $replace];
        $file = str_replace($from, $to, $file);

        $regex = ['#(\.){2,}#'];
        $file = preg_replace($regex, '-', $file);
        if ($allowDoublePoint) {
            $regex = ['#[^A-Za-z0-9\_\-\(\) \.]#'];
        } else {
            $regex = ['#[^A-Za-z0-9\_\-\(\) ]#'];
        }
        $file = preg_replace($regex, '-', $file);
        $regex = ['#^\.#'];
        $file = preg_replace($regex, '-', $file);
        $file = preg_replace('!\s+!', $replace, $file);
        $file = trim($file);

        $regex = "/[\._-]+$/"; // replace all special characters (.*?!@#$&-_) from the end
        $file = preg_replace($regex, "", $file);

        return $file;
    }
}

if (!function_exists('get_dir_size')) {

    function get_dir_size($directory, $day, $month, $hour, $minute, $year, $thisMonth = true)
    {
        $size = 0;

        try {
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory),
                \RecursiveIteratorIterator::LEAVES_ONLY) as $file){
                if ($file->isFile()) {
                    if ($thisMonth) {
                        // Media file resides in subdir but comparison below is done with dir above
                        if (File::dirname($file->getPath()) != $directory) {
                            $path = File::basename(File::dirname($file->getPath()));
                        } else {
                            $path = File::basename($file->getPath());
                        }

                        if (mktime($hour, $minute, 0, $month, $day, $year) <= $path) {
                            $size += $file->getSize();
                        }
                    } else {
                        $size += $file->getSize();
                    }
                }
            }
        } catch (\UnexpectedValueException $e) {
            return false;
        }

        return $size;
    }

}

if (!function_exists('array_unset_recursive')) {

    function array_unset_recursive(&$array, array $unwantedKeys)
    {
        foreach ($unwantedKeys as $key) {
            unset($array[$key]);
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                array_unset_recursive($value, $unwantedKeys);
            } elseif (is_object($value)) {
                foreach ($unwantedKeys as $key) {
                    unset($value->{$key});
                }
            }
        }
    }
}

if (!function_exists('array_key_first')) {

    /**
     * Gets the first key of an array
     *
     * @param array $array
     * @return mixed
     */
    function array_key_first(array $array)
    {
        return $array ? array_keys($array)[0] : null;
    }
}

if (! function_exists('user_uuid')) {

    function user_uuid(User $user)
    {
        $salt = 'wR9$\H@f"NA:2e,`stCAUKBT^>_aHaGa-u01`!{!hPmFjHgu3]m?MQKTk4I(M>?nMq6@{;Me)$:gpM/cB|nxz1VhMA*ZpWZm550WtN5)lTU9_.[5`ib#*kBx<L!47o$b';

        return md5($user->usr_id . $user->username . $user->created_at . $salt);
    }
}

if (! function_exists('get_guid')) {

    function get_guid($prefix = '')
    {
        return str_replace('.', '', uniqid($prefix, true));
    }
}

if (! function_exists('refresh_feed')) {

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @return int
     * @throws Exception
     */
    function refresh_feed(string $username, string $feedId)
    {
        Log::debug("User '{$username}': refresh_feed called for feed '{$feedId}'.");
        //Cache::forget('rss_feed_' . mb_strtolower($user->login_name) . '_' . $feedId);
        $fgm = new \App\Classes\FeedGeneratorManager($username, $feedId);

        return $fgm->publish();
    }
}

if (! function_exists('get_package_feature')) {

    function get_package_feature(Package $package, $type)
    {
        $packageMappings = $package->mappings;
        // TODO: Simplify with one query
        $packageFeatures = $package->features;
        $packageFeature = $packageFeatures->search(function($item) use ($type) {
            return $item->feature_name == $type;
        });

        if ($packageFeature === false) {
            return false;
        }

        $packageFeatureId = $packageFeatures[$packageFeature]->package_feature_id;
        $feature = $packageMappings->search(function($item) use ($packageFeatureId) {
            return $item->package_feature_id == $packageFeatureId;
        });

        if ($feature === false) {
            return false;
        }

        return $package->mappings[$feature];
    }
}

if (! function_exists('get_package_units')) {

    function get_package_units(Package $package, $type)
    {
        $feature = get_package_feature($package, $type);

        if (!$feature) {
            return false;
        }

        return $feature->units;
    }
}

if (! function_exists('get_player_configuration_count')) {

    function get_player_configuration_count(User $user)
    {
        $units = get_package_units($user->package, Package::FEATURE_PLAYER_CONFIGURATION);
        $extras = UserExtra::where('usr_id', '=', $user->id)->where('extras_type', '=', UserExtra::EXTRA_PLAYERCONFIGURATION)->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count');
        $extraUnits = $extras * UserExtra::getPieces()[UserExtra::EXTRA_PLAYERCONFIGURATION];
        $used = PlayerConfig::where('user_id', '=', $user->id)->count();
        $total = $units + $extraUnits;

        return [
            'included' => $units,
            'extra' => $extraUnits,
            'total' => $total,
            'used' => $used,
        ];
    }
}

if (! function_exists('get_package_status')) {

    /**
     * @param  Package  $package
     * @param $type
     * @return mixed
     */
    function get_package_status(Package $package, $type)
    {
        $feature = get_package_feature($package, $type);

        if (!$feature) {
            return false;
        }

        return $feature->status;
    }
}

if (! function_exists('has_package_feature')) {

    function has_package_feature(Package $package, $type)
    {
        return get_package_status($package, $type) == 1;
    }
}

if (! function_exists('get_package_feature_feeds')) {

    function get_package_feature_feeds(Package $package, User $user)
    {
        $feedsIncluded = get_package_units($package, Package::FEATURE_FEEDS);
        $feedsExtra = UserExtra::where('usr_id', '=', $user->id)->where('extras_type', '=', UserExtra::EXTRA_FEED)->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count');
        $feedsUsed = Feed::where('username', '=', $user->username)->count();
        $feedsTotal = $feedsIncluded + $feedsExtra;

        return [
            'included' => $feedsIncluded,
            'extra' => $feedsExtra,
            'total' => $feedsTotal,
            'used' => $feedsUsed,
            'available' => $feedsTotal-$feedsUsed,
        ];
    }
}

if (! function_exists('get_package_feature_statistics_export')) {

    /**
     * @param  Package  $package
     * @param  User  $user
     * @return array
     */
    function get_package_feature_statistics_export(Package $package, User $user): array
    {
        $included = get_package_units($package, Package::FEATURE_STATISTICS_EXPORT);
        $extra = UserExtra::where('usr_id', '=', $user->id)->where('extras_type', '=', UserExtra::EXTRA_STATSEXPORT)->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count') * UserExtra::getPieces()[UserExtra::EXTRA_STATSEXPORT];
        $accountingTimes = get_user_accounting_times($user->id);
        $used = \App\Models\StatsExport::withTrashed()->where('user_id', '=', $user->id)->whereDate('created_at', '>', $accountingTimes['startTime'])->count(); // TODO
        $total = $included + $extra;

        return [
            'included' => $included,
            'extra' => $extra,
            'total' => $total,
            'used' => $used,
            'available' => $total - $used,
        ];
    }
}

if (! function_exists('get_user_space')) {

    function get_user_space(User $user)
    {
        // Database calculated storage usage: storage grant, storage usage
        $total = Space::available()->whereUserId($user->id)->sum('space');
        $totalSpace = get_size_readable($total);
        $included = Space::available()->whereUserId($user->id)->whereType(Space::TYPE_REGULAR)->sum('space');
        $includedSpace = get_size_readable($included);
        $extra = Space::available()->whereUserId($user->id)->whereType(Space::TYPE_EXTRA)->sum('space');
        $extraSpace = get_size_readable($extra);
        $available = Space::available()->whereUserId($user->id)->sum('space_available');
        $availableSpace = get_size_readable($available);
        $used = $total - $available;
        $usedSpace = get_size_readable($used);

        return [
            'total' => $totalSpace,
            'included' => $includedSpace,
            'extra' => $extraSpace,
            'used' => $usedSpace,
            'available' => $availableSpace,
            'raw' => [
                'total' => $total,
                'included' => $included,
                'extra' => $extra,
                'available' => $available,
                'used' => $used,
            ]
        ];
    }
}

if (! function_exists('get_package_feature_player_configuration')) {

    /**
     * @param  Package  $package
     * @param  User  $user
     * @return array
     */
    function get_package_feature_player_configuration(Package $package, User $user): array
    {
        $packageConfigurations = (int) get_package_units($package, Package::FEATURE_PLAYER_CONFIGURATION);
        $usedConfigurations = \App\Models\PlayerConfig::whereUserId($user->user_id)->count();
        $extraConfigurations = UserExtra::where('usr_id', '=', $user->user_id)->playerconfiguration()->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count');
        $maxConfigurations = $packageConfigurations + $extraConfigurations;
        $availableConfigurations = $maxConfigurations - $usedConfigurations;

        return [
            'included' => $packageConfigurations,
            'extra' => $extraConfigurations,
            'total' => $maxConfigurations,
            'used' => $usedConfigurations,
            'available' => $availableConfigurations,
        ];
    }
}

if (! function_exists('get_package_feature_members')) {

    /**
     * @param  Package  $package
     * @param  User  $user
     * @return array
     */
    function get_package_feature_members(Package $package, User $user): array
    {
        $packageConfigurations = (int) get_package_units($package, Package::FEATURE_MEMBERS);
        $usedConfigurations = \App\Models\Team::whereOwnerId($user->user_id)->withCount('members')->get()->sum('members_count');
        $extraConfigurations = UserExtra::where('usr_id', '=', $user->user_id)->member()->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count');
        $maxConfigurations = $packageConfigurations + $extraConfigurations;
        $availableConfigurations = $maxConfigurations - $usedConfigurations;
        $userId = $user->user_id;
        $queued = MemberQueue::whereHas('team', function ($query) use ($userId) {
            $query->where('teams.owner_id', '=', $userId);
        })->count();

        return [
            'included' => $packageConfigurations,
            'extra' => $extraConfigurations,
            'total' => $maxConfigurations,
            'used' => $usedConfigurations,
            'available' => $availableConfigurations,
            'queued' => $queued,
            'usedWithQueued' => $usedConfigurations + $queued,
        ];
    }
}

if (! function_exists('get_package_feature_custom_domains')) {

    /**
     * @param  Package  $package
     * @param  User  $user
     * @return array
     */
    function get_package_feature_custom_domains(Package $package, User $user): array
    {
        $packageDomains = (int) get_package_units($package, Package::FEATURE_DOMAINS);
        $usedDomains = Feed::distinct('domain.hostname')->where('username', '=', $user->username)->whereIn('domain.is_custom', [1, '1', true])->get()->count();
        // Not available
        //$extraConfigurations = UserExtra::where('usr_id', '=', $user->user_id)->domain()->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count');
        $extraConfigurations = 0;
        $maxConfigurations = $packageDomains + $extraConfigurations;
        $availableConfigurations = $maxConfigurations - $usedDomains;

        return [
            'included' => $packageDomains,
            'extra' => $extraConfigurations,
            'total' => $maxConfigurations,
            'used' => $usedDomains,
            'available' => $availableConfigurations,
        ];
    }
}

if (! function_exists('get_package_feature_premium_subdomains')) {

    /**
     * @param  Package  $package
     * @param  User  $user
     * @return array
     */
    function get_package_feature_premium_subdomains(Package $package, User $user): array
    {
        $username = $user->username;
        $packageDomains = (int) get_package_units($package, Package::FEATURE_SUBDOMAINS_PREMIUM);
        $feeds = Feed::where('username', '=', $username)
            ->where('domain.website_type', '!=', false)
            ->where('domain.is_custom', '=', false)
            ->where(function ($query) {
                return $query->where('domain.website_redirect', '=', false)
                    ->orWhere(function ($query) {
                        return $query->whereNull('domain.website_redirect');
                    });
            })
            ->where('domain.subdomain', 'not like', "$username.%")
            ->get();

        $usedDomains = $feeds->filter(function($feed) {
            $subdomain = Str::before($feed->domain['subdomain'], '.');
            return strlen($subdomain) < Domain::SUBDOMAIN_LENGTH_MINIMUM;
        })->unique(function ($feed) {
            return $feed->domain['subdomain'];
        })->count();

        // Not available, (yet!?)
        //$extraConfigurations = UserExtra::where('usr_id', '=', $user->user_id)->TODO()->whereDate('date_end', '>', date('Y-m-d H:i:s'))->sum('extras_count');
        $extraConfigurations = 0;
        $maxConfigurations = $packageDomains + $extraConfigurations;
        $availableConfigurations = $maxConfigurations - $usedDomains;

        return [
            'included' => $packageDomains,
            'extra' => $extraConfigurations,
            'total' => $maxConfigurations,
            'used' => $usedDomains,
            'available' => $availableConfigurations,
        ];
    }
}

if (! function_exists('change_prefix')) {

    function change_prefix($num)
    {
        if (!is_numeric($num)) {
            return $num;
        }

        return (0 - $num);
    }
}


if (! function_exists('get_newest_show')) {

    function get_newest_show(Feed $feed, $needsEnclosure = false): ?array
    {
        if (isset($feed->entries) && count($feed->entries) > 0) {
            $entries = collect($feed->entries);
            $entries = $entries->filter(function ($value, $key) use ($needsEnclosure) {
                if ($needsEnclosure) {
                    return !empty($value['show_media'])
                        && $value['is_public'] == Show::PUBLISH_NOW;
                }
                return $value['is_public'] == Show::PUBLISH_NOW;
            });
            $entries = $entries->sortByDesc('lastUpdate');

            return $entries->shift();
        }

        return null;
    }
}

if (! function_exists('get_show_by_guid')) {

    /**
     * @param  Feed  $feed
     * @param  string  $guid
     * @return array|null
     * @throws Exception
     */
    function get_show_by_guid(Feed $feed, string $guid): ?array
    {
        if (isset($feed->entries) && count($feed->entries) > 0) {
            foreach($feed->entries as $entry) {
                if (isset($entry['guid'])
                        && $entry['guid'] === $guid) {
                    return $entry;
                }
            }
        }

        throw new \Exception('Show not found');
    }
}

if (! function_exists('get_show_by_aid')) {

    /**
     * @param  Feed  $feed
     * @param  string  $aid
     * @return array|null
     * @throws Exception
     */
    function get_show_by_aid(Feed $feed, string $aid): ?array
    {
        if (isset($feed->entries) && count($feed->entries) > 0) {
            foreach($feed->entries as $entry) {
                if (isset($entry['audiotakes_guid'])
                        && $entry['audiotakes_guid'] === $aid) {
                    return $entry;
                }
            }

            foreach($feed->entries as $entry) {
                $username = $feed->username;
                try {
                    if (isset($entry['show_media']) && !empty($entry['show_media'])) {
                        $file = get_file($username, $entry['show_media']);

                        if ($file) {
                            if ($aid === sha1($file['name'])) {
                                return $entry;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("ERROR: Username `$username`: Could not get file for show `{$entry['guid']}`");
                }
            }
        }

        throw new \Exception('Show not found');
    }
}

if (! function_exists('get_guid')) {
    /**
     * @param  string  $prefix
     * @return string
     */
    function get_guid(string $prefix): string
    {
        return str_replace('.', '', uniqid($prefix, true));
    }
}

if (!function_exists('get_user_accounting_times')) {

    /**
     * @param  int  $userId
     * @return array
     * @throws Exception
     * @todo Cache
     */
    function get_user_accounting_times(int $userId): ?array
    {
        $res = null;
        // TODO: I18N
        $format = 'd.m.Y H:i \U\h\r';

        $order = UserAccounting::where('usr_id', '=', $userId)
            ->where('activity_type', '=', Activity::PACKAGE)
            ->orderByDesc('accounting_id')
            ->first();

        if ($order) {
            $res = [];
            $res['startTime'] = $order->date_start; // packageStartDate
            $res['startTimeFormatted'] = $order->date_start->format($format);
            $res['endTime'] = $order->date_end; // packagePaidDate
            $res['endTimeFormatted'] = $order->date_end->format($format);; // packagePaidDate
            $period = new \DatePeriod(new \DateTime($order->date_start),
                \DateInterval::createFromDateString('1 month'),
                new \DateTime($order->date_end));

            $dateNow = new \DateTime();
            $dtThis = new \DateTime($order->date_start);
            $dtNext = (new \DateTime($order->date_end))->modify('+5 minutes');

            foreach ($period as $dt) {
                if ($dt > $dateNow) {
                    $dtNext = $dt;
                    break;
                }
                $dtThis = $dt;
            }
            $res['nextTime'] = $dtNext; // renewTime
            $res['nextTimeFormatted'] = $dtNext->format($format); // renewTimeFormatted
            $res['currentTime'] = $dtThis;
            $res['currentTimeFormatted'] = $dtThis->format($format);
        }

        return $res;
    }
}

if (!function_exists('change_prefix')) {

    /**
     * @param $v
     * @return int|mixed|string
     */
    function change_prefix($v)
    {
        if (!is_numeric($v)) {
            return $v;
        }

        return (0 - $v);
    }
}

if (!function_exists('get_tlds')) {

    /**
     * @return array
     */
    function get_tlds(): array
    {
        return Cache::get('IANA_TLDS', function() {
            $tlds = Http::get(IANA_URL)->body();
            $aTLDs = preg_split("/\r\n|\n|\r/", $tlds);
            // If last key has empty value - remove it
            if (!last($aTLDs)) {
                array_pop($aTLDs);
            }
            array_filter($aTLDs);
            array_shift($aTLDs);
            array_walk($aTLDs, function(&$val) { $val = mb_strtolower(trim($val, "\n")); } );


            Cache::put('IANA_TLDS', $aTLDs, now()->addDay());

            return $aTLDs;
        });
    }
}
if (!function_exists('get_mimetype_by_extension')) {

    function get_mimetype_by_extension($extension)
    {
        $mimetype = mb_strtolower($extension);

        switch ($extension) {
            case 'mp3':
                $mimetype = 'mpeg';
                break;
            case 'm4a':
                $mimetype = 'mp4';
                break;
            case 'ogv':
                $mimetype = 'ogg';
                break;
        }

        switch ($extension) {
            case 'mp3':
            case 'm4a':
            case 'aac':
            case 'ogg':
            case 'oga':
            case 'wav':
            case 'weba':
            case 'flac':
                return "audio/" . $mimetype;
            case 'mp4':
            case 'ogv':
            case 'webm':
                return "video/" . $mimetype;
            case 'png':
            case 'jpeg':
            case 'jpg':
            case 'gif':
            case 'webp':
                return "image/" . $mimetype;
            default:
                return "application/" . $mimetype;
        }
    }
}

if (!function_exists('get_link')) {

    function get_link(string $name, string $hostname, ?string $type = 'rss')
    {
        return strtolower($hostname) . DIRECTORY_SEPARATOR . $name . $type;
    }
}

if (!function_exists('get_default_domain')) {

    function get_default_domain(string $username)
    {
        if (function_exists('mb_strtolower')) {
            $username = mb_strtolower($username);
        } else {
            $username = strtolower($username);
        }
        $dm = new DomainManager();
        $aLocalDomains = $dm->getLocal();
        $defaultDomain = array_shift($aLocalDomains);
        $tld = Str::afterLast($defaultDomain, '.');
        $subdomain = substr($defaultDomain, 0, strlen($defaultDomain) - strlen($tld)-1);

        return [
            "tld" => $tld,
            "subdomain" => "{$username}.{$subdomain}",
            "protocol" => "https",
            "hostname" => "https://{$username}.{$subdomain}.{$tld}",
            "domain" => $defaultDomain,
            "is_custom" => false,
            "website_type" => false,
            "website_redirect" => false,
            "feed_redirect" => false,
        ];
    }
}

if (!function_exists('cache_player_config')) {

    function cache_player_config(\App\Models\PlayerConfig $pc)
    {
        $file = storage_path('app/public/player/config/' . $pc->uuid . '.js');

        File::ensureDirectoryExists(File::dirname($file), 0755, true);
        File::put($file, $pc->get());

        if (!empty($pc->custom_styles)) {
            $cssFile = storage_path('app/public/player/config/' . $pc->uuid . '.css');
            File::put($cssFile, $pc->custom_styles);
        }
    }
}

if (!function_exists('get_wordpress_enclosure')) {

    function get_wordpress_enclosure(string $username, $mediaId, Feed $feed)
    {
        $file = get_file($username, $mediaId);

        if (!$file) {
            return '';
        }

        $mime = $file['mimetype'];

        if ($mime == 'audio/m4a') {
            $mime = 'audio/mp4';
        }

        $link = get_enclosure_uri($feed->feed_id, $file['name'], $feed->domain, null, 'blog');

        return $link . PHP_EOL . $file['byte'] . PHP_EOL . $mime;
    }
}

if (!function_exists('link_show_for_feed')) {

    /**
     * @param  string  $username
     * @param  Feed  $feed
     * @param  string  $guid
     * @return bool
     * @throws Exception
     */
    function link_show_for_feed(string $username, Feed $feed, string $guid)
    {
        $showManager = new ShowManager();
        $show = $showManager->get($feed, $guid);

        if (!$show || !isset($show['show_media'])) {
            return false;
        }

        $mediaId = $show['show_media'];

        if (!$mediaId) {
            return false;
        }

        $file = get_file($username, $mediaId);

        if (!$file) {
            return false;
        }

        $fgm = new FeedGeneratorManager($username, $feed->feed_id);

        if (!$fgm->link($file, UserData::FEED_MEDIA_DIR)) {
            throw new \Exception("User {$username}: Adding link to '{$file['path']}' for feed '{$feed->feed_id}' and show with guid '{$guid}' failed.");
        }

        return true;
    }
}

if (!function_exists('get_duration')) {
    /**
     * @param  string  $username
     * @param  string  $id
     * @return int|string
     * @throws Exception
     */
    function get_duration(string $username, string $id)
    {
        $file = get_file($username, $id);
        $getID3 = new GetID3();
        $duration = new Duration();
        // HEAVY OPERATION
        $mixinfo = $getID3->analyze($file['path'], $file['byte']);
        $showDuration = $mixinfo['playtime_seconds'] ?? 0;
        $_duration = $duration->formatted($showDuration, true);

        if (Str::contains($_duration, '.')) {
            // Remove microseconds as it is not valid in itunes:duration
            $_duration = Str::before($_duration, '.');
        }

        if (Str::startsWith($_duration, '0:')) {
            $_duration = '0' . $_duration;
        }

        if (Str::endsWith($_duration, ':')) {
            $_duration .= '00';
        }

        return $_duration;
    }
}

if (!function_exists('get_country_spelled')) {
    function get_country_spelled($countryCode)
    {
        $locale = Str::before(app()->getLocale(), '-');
        $countries = \Countrylist::getList($locale);

        if (!array_key_exists($countryCode, $countries)) {
            return $countryCode;
        }

        return $countries[$countryCode];
    }
}

if (!function_exists('in_array_r')) {
    /**
     * @param $needle
     * @param $haystack
     * @param $strict
     * @return bool
     */
    function in_array_r($needle, $haystack, bool $strict = true)
    {
        foreach ($haystack as $item) {

            if( ! $strict && is_string( $needle ) && ( is_float( $item ) || is_int( $item ) ) ) {
                $item = (string)$item;
            }

            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }
}


if (!function_exists('link_user_enclosures')) {
    function link_user_enclosures(string $username, string $feedId)
    {
        $feed = Feed::whereUsername($username)->whereFeedId($feedId)->firstOrFail();
        $basePath = storage_path(get_user_public_path($username));
        $feedPath = $basePath.DIRECTORY_SEPARATOR.$feed->feed_id.DIRECTORY_SEPARATOR.'media';
        $logoPath = $basePath.DIRECTORY_SEPARATOR.$feed->feed_id.DIRECTORY_SEPARATOR.'logos';
        File::ensureDirectoryExists($feedPath);
        File::ensureDirectoryExists($logoPath);

        foreach($feed->entries as $show) {
            try {
                link_file($username, $show['show_media'], $feedPath);

                if (isset($show['itunes']['logo']) && $show['itunes']['logo']) {
                    link_file($username, $show['show_media'], $logoPath);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
    }
}

if (!function_exists('link_file')) {
    function link_file(string $username, string $id, string $path)
    {
        try {
            $file = get_file($username, $id);
            $link = $path.DIRECTORY_SEPARATOR.$file['name'];
            if (!File::isFile($link)) {
                File::link($file['path'], $link);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
