<?php

namespace App\Models;

use App\Classes\MediaManager;
use App\Events\UserDeletedEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Passport\HasApiTokens;
use App\Events\FileDeletedEvent;
use App\Events\FileSavedEvent;
use App\Notifications\CampaignInvitationNotification;
use App\Scopes\IsActiveScope;
use Jenssegers\Mongodb\Eloquent\HybridRelations;
use App\Scopes\IsVisibleScope;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, HybridRelations, SoftDeletes, Impersonate, SerializesModels, InteractsWithMedia, HasFactory, TwoFactorAuthenticatable;

    const TERMS_VERSION = 1.2;
    const PRIVACYPOLICY_VERSION = 2.1;
    const MEDIA_STORAGE_DIR = 'hostingstorage/mediafiles';
    const CATEGORY_DEFAULT  = '_default_';
    const CATEGORY_CREATE_ACTION = '_CREATE_';

    protected $primaryKey = 'usr_id';

    protected $table = 'usr';

    const ROLE_GUEST = 0;
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;
    const ROLE_TEAM = 3;
    const ROLE_SUPPORTER = 4;
    const ROLE_EDITOR = 5;

    const IS_TRIAL = 1;
    const IS_INACTIVE = 0; // deprecated
    const IS_ACTIVE = -1;
    const HAS_PAID = -2;

    protected $with = ['package'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'name_title',
        'email',
        'url',
        'passwd',
        'password',
        'terms_date',
        'terms_version',
        'privacy_date',
        'privacy_version',
        'city',
        'street',
        'housenumber',
        'post_code',
        'country',
        'organisation',
        'department',
        'additional_specifications',
        'register_court',
        'register_number',
        'vat_id',
        'board',
        'chairman',
        'representative',
        'mediarepresentative',
        'controlling_authority',
        'agency_enabled',
        'audiotakes_enabled',
        'telephone',
        'telefax',
        'is_trial',
        'use_new_statistics',
        'can_pay_by_bill',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'passwd',
        'password',
        'remember_token',
        'role_id',
        'feed_email',
        'is_acct_active',
        'is_trial',
        'date_created',
        'date_trialend',
        'created_by',
        'last_updated',
        'updated_by',
        'terms_date',
        'terms_version',
        'privacy_date',
        'privacy_version',
        'forum_number_post',
        'is_updating',
        'is_blocked',
        'is_protected',
        'parent_user_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_supporter',
        'is_advertiser',
        'can_pay_by_bill',
        'has_paid',
        'super',
        'name',
        'avatar',
        'preferences',
        'last_login',
        'package',
        'audiotakes_enabled',
        'additional_specifications',
        'new_package_id',
        'use_new_statistics',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'usr_id' => 'int',
        'is_acct_active' => 'bool',
        'is_blocked' => 'bool',
        'is_trial' => 'int', // Can have three states: -1, 0, 1 - so do NOT change to bool
        'is_supporter' => 'bool',
        'is_advertiser' => 'bool',
        'audiotakes_enabled' => 'bool',
        'use_new_statistics' => 'bool',
        'package_id' => 'int',
    ];

    private $extensions = [
        'audio' => ['mp3','aac','m4a','wav','flac','ogg','oga','wma'],
        'text' => ['txt','pdf','doc','odp','html','odt','ods','xls','vtt'],
        'image' => ['jpg','jpeg','gif','png','ico','tiff','svg','webp','bmp'],
        'video' => ['mp4','ogv','wmv','avi','mkv','webm'],
        'logo' => ['jpg','png'],
        'enclosure' => ['mp3','aac','m4a','ogg','pdf','mp4'],
    ];

    protected $dates = [
        'date_created',
        'date_trialend',
        'last_updated',
        'terms_date',
        'privacy_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return string
     */
    public static function getNewUsername(): string
    {
        $username = strtolower(Str::random(6));

        while (!self::isUsernameAvailable($username)) {
            return self::getNewUsername();
        }

        return $username;
    }

    /**
     * @param  string  $username
     * @return bool
     */
    public static function isUsernameAvailable(string $username)
    {
        if (User::where('username', '=', $username)->count() > 0) {
            return false;
        }

        if (UserQueue::where('username', '=', $username)->count() > 0) {
            return false;
        }

        if (UserForbidden::where('username', '=', $username)->count() > 0) {
            return false;
        }

        if (Route::has($username)) {
            return false;
        }

        return true;
    }

    public function isInTrial()
    {
        return $this->is_trial == self::IS_TRIAL;
    }

    protected static function boot()
    {
        self::addGlobalScope(new IsActiveScope());

        // TODO: Implement
/*        static::updated(function(User $user) {
            event(new UserUpdateEvent($user));
        });*/

        static::deleted(function(User $user) {
            Log::debug("User " . $user->username . ": Deleted the account.");
            $user->update(['email' => uniqid('DELETED_') . '_' . time() . $user->email]);

            PackageChange::create([
                'user_id' => $user->id,
                'type' => PackageChange::TYPE_DELETE,
                'from' => $user->package_id,
            ]);

            event(new UserDeletedEvent($user));
        });

        static::restored(function(User $user) {
            Log::debug("User " . $user->username . ": Account restored.");

            $files = $user->getFiles('name', false, null, 1, 100000);

            foreach($files['items'] as $file) {
                $user->link($file->path, UserData::MEDIA_DIRECT_DIR);
            }

            foreach(Feed::owner()->get() as $feed) {
                link_user_enclosures($user->username, $feed->feed_id);

                try {
                    $this->line("User `{$feed->username}`: Renewing feed with id `{$feed->feed_id}`");
                    refresh_feed($feed->username, $feed->feed_id);
                } catch (\Exception $e) {
                    $this->error("User `{$feed->username}`: Failed to refresh feed with id `{$feed->feed_id}`.");
                }
            }
        });

        parent::boot();
    }

    public function getAuthPassword()
    {
        return $this->passwd;
//        return !is_null($this->password) ? $this->password : $this->passwd;
    }

    public function package()
    {
        return $this->hasOne('App\Models\Package', 'package_id', 'package_id')->withoutGlobalScope(IsVisibleScope::class);
    }

    public function userpayments()
    {
        return $this->hasMany('App\Models\UserPayment', 'receiver_id', 'usr_id');
    }

    public function userbillingcontact()
    {
        return $this->hasOne('App\Models\UserBillingContact', 'user_id', 'usr_id');
        //return $this->hasMany('App\Models\UserBillingContact', 'user_id', 'usr_id');
    }

    public function accounting()
    {
        return $this->hasMany(UserAccounting::class, 'usr_id', 'usr_id');
    }

    public function feeds()
    {
        return $this->hasMany('App\Models\Feed', 'username', 'username');
    }

    public function extras()
    {
        return $this->hasMany(UserExtra::class, 'usr_id', 'usr_id');
    }

    public function approvals()
    {
        return $this->hasMany(UserOauth::class, 'user_id', 'usr_id');
    }

    public function space()
    {
        return $this->hasMany(Space::class, 'user_id', 'usr_id');
    }

    public function userExtra()
    {
        return $this->extras();
    }

    public function getFullnameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

/*    public function getFirstnameAttribute()
    {
        return $this->first_name;
    }

    public function getLastnameAttribute()
    {
        return $this->last_name;
    }*/

    public function getAddr1Attribute()
    {
        return $this->street;
    }

    public function getAddr2Attribute()
    {
        return $this->housenumber;
    }

    public function getAddr3Attribute()
    {
        return $this->feed_email;
    }

    public function getIdAttribute()
    {
        return $this->usr_id;
    }

    public function getUserIdAttribute()
    {
        return $this->usr_id;
    }

    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->username;
    }

    public function getAvatarAttribute()
    {
        return null; // TODO: Implement user image
    }

    /**
     * @return bool
     */
    public function canImpersonate()
    {
        return in_array($this->role_id, [self::ROLE_ADMIN, self::ROLE_SUPPORTER]);
    }

    /**
     * @return bool
     */
    public function canBeImpersonated()
    {
        if ($this->role_id === self::ROLE_ADMIN) {
            return in_array($this->role_id, [self::ROLE_USER, self::ROLE_TEAM, self::ROLE_SUPPORTER, self::ROLE_EDITOR]);
        }
        return in_array($this->role_id, [self::ROLE_USER, self::ROLE_TEAM, self::ROLE_EDITOR]);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function makeLogfileDir()
    {
        $dir = $this->getLogfileDir();

        if (!File::isDirectory($dir)) {
            if (!File::makeDirectory($dir, 0755, true, true)) {
                throw new \Exception("ERROR: Could not create directory '{$dir}' or parent directory for user " . $this->username);
            }
        }
        @chown($dir, config('filesystems.webserver_log.user'));
        @chgrp($dir, config('filesystems.webserver_log.group'));

        return (File::isDirectory($dir) && File::isReadable($dir));
    }

    public function getLogfileDir()
    {
        $webserverLogDir = config('filesystems.webserver_log.dir');

        return $webserverLogDir . $this->getUserPath();
    }

    /**
     *
     *
     * @param  string  $username
     * @return string
     * @throws \Exception
     */
    public function getUserPath()
    {
        return get_user_path($this->username);
    }

    public function getStoragePath()
    {
        $path = storage_path(get_user_media_path($this->username));

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path . DIRECTORY_SEPARATOR;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        $aFolder = [
            User::CATEGORY_DEFAULT => trans('mediamanager.group_default'),
        ];

        try {
            foreach (new \DirectoryIterator($this->getStoragePath()) as $fileinfo) {
                if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                    foreach (new \DirectoryIterator($fileinfo->getPathname()) as $dir) {
                        if ($dir->isDir() && !$dir->isDot()) {
                            $name =  $dir->getBasename();
                            $aFolder[$name] = $name;
                        }
                    }
                }
            }
        }  catch (\UnexpectedValueException $e) {
        }

        $aFolder = array_merge($aFolder, [
            null => '--------------------------------------------',
            self::CATEGORY_CREATE_ACTION => 'Neue Gruppe erstellen']
        );
        return $aFolder;
    }

    /**
     * @param  string  $sortBy
     * @param  bool  $sortDesc
     * @param  string|null  $filter
     * @param  int  $currentPage
     * @param  int  $perPage
     * @param  bool  $strict
     * @return array
     * @throws \Exception
     */
    public function getFiles(?string $sortBy = 'name', $sortDesc = false, ?string $filter = null, ?int $currentPage = 1, ?int $perPage = 10,
        $strict = false): array
    {
        try {
            $dIterator      = new \RecursiveDirectoryIterator($this->getStoragePath());
            $rIterator      = new \RecursiveIteratorIterator($dIterator,  \RecursiveIteratorIterator::SELF_FIRST);

            if (!class_exists('finfo')) {
                throw new \Exception("Class 'finfo' is missing.");
            }

            //$oInfo          = new \finfo(\FILEINFO_NONE |  \FILEINFO_CONTINUE |  \FILEINFO_PRESERVE_ATIME);
            //$oMime          = new \finfo(\FILEINFO_MIME_TYPE |  \FILEINFO_CONTINUE |  \FILEINFO_PRESERVE_ATIME);
            #$aBatchFolders  = (array)\de\App\Encoding\Encoding::singleton()->getCategories(PodcastUserDAO::singleton()->getUserIdByUsername($username));

/*            if (!$oInfo || !$oMime) {
                throw new \Exception("Opening fileinfo database failed");
            }*/
            $aFiles = $labels = [];

            foreach ($rIterator as $file) {
                if ($file->isFile()) {

                    if (!is_null($filter)) {
                        if (preg_match_all('/type:([^\s]+)/', $filter, $matches)) {
                            array_shift($matches);
                            $ext = strtolower($file->getExtension()); // Lower-case extension, e.g. .WAV to .wav
                            $extensions = [];

                            foreach ($matches[0] as $match) {
                                if (array_key_exists($match, $this->extensions)) {
                                    $extensions = array_merge($extensions, $this->extensions[$match]);
                                }
                            }

                            if (!in_array($ext, $extensions)) {
                                continue;
                            }
                        }
                    }

                    $aPath          = explode(DIRECTORY_SEPARATOR, ltrim(substr_replace($file->getPath(), '', 0,
                        strlen($this->getStoragePath())), DIRECTORY_SEPARATOR));
                    $id             = $aPath[0];
                    $cat            = isset($aPath[1]) ? $aPath[1] : self::CATEGORY_DEFAULT;

                    if ($cat != self::CATEGORY_DEFAULT && !in_array($cat, $labels)) {
                        array_push($labels, $cat);
                    }

                    if (!is_null($filter)) {
                        if (preg_match('/folder:("[^"]*+"|[^"\s]*+)/', $filter, $matches)) {
                            array_shift($matches);
                            // Strip out the surrounding "..." , e.g. "Was ist das?" -> Was ist das?
                            array_walk($matches, function(&$m) {
                                $m = trim($m, "\t\n\r\0\X0B\"");
                            });

                            if (!in_array($cat, $matches)) {
                                continue;
                            }
                        }
                    }

                    /*                    $_type          = PodcastDeEnclosureTypeTools::getTypeContentByFileExtension($file->getBasename());
                                        $type           = ($_type == ENCLOSURE_TYPE_UNKNOWN)
                                            ? PodcastDeEnclosureTypeTools::getTypeContent($oMime->file($file->getPathname())) : $_type;*/
                    $extension = mb_strtolower($file->getExtension());
                    $mimetype = get_mimetype_by_extension($extension);
                    $o = new \stdClass();
                    $o->id = $id; // Identifier
                    $o->name =  $file->getBasename(); // File name
                    $o->path =  $file->getPathname(); // Complete path and file name
                    //$o->info =  $oInfo->file($file->getPathname()); // Details on file
                    //$o->mime =  self::fixMime($oMime->file($file->getPathname()); $file->getExtension()); // Mime type
                    //$o->type =  $type; // podcaster.de type (int)
                    $o->byte =  $file->getSize(); // Size in bytes
                    $o->created = date("d.m.Y H:i:s", $id); // Date of upload
                    $o->size =  get_size_readable($file->getSize(), 2, 1024); // Formatted size
                    $o->last =  date("d.m.Y H:i:s", $file->getMTime()); // Last modification
                    $o->cat = $cat;
                    $o->url = get_direct_uri($this->username, 'download', $o->name, 'embed');
                    $o->intern = get_intern_uri($o->id); // Prevent calls on file being tracked through statistics
                    $o->extension = $extension;
                    $o->mimetype = $mimetype;
                    $o->type = Str::before($mimetype, '/');
                    $created = new CarbonImmutable($o->created);
                    $o->created_date = $created->formatLocalized('%d.%m.%Y');
                    $o->created_time = $created->formatLocalized('%H:%I');

                    $aFiles[] = $o;
                } elseif ($file->isDir()) {
                    $cat = $file->getFilename();
                }
            }

            $a = [];
            $items = collect($aFiles);
            $sortDesc = (!$sortDesc || $sortDesc == 'false' || $sortDesc == 'desc' ? false : true);

            switch($sortBy) {
                case 'size':
                    $items = $items->sortBy(function($o, $b) {
                        return $o->byte;
                    }, \SORT_NUMERIC, $sortDesc);
                    break;
                case 'created':
                    $items = $items->sortBy(function($o, $b) {
                        return strtotime($o->created);
                    }, \SORT_NUMERIC, $sortDesc);
                    break;
                default:
                    $items = $items->sortBy($sortBy, \SORT_REGULAR, $sortDesc);
            }

            if (!is_null($filter)) {
                // This filters for a filename pattern
                if ($filter != 'null') {

                    if ($filter === 'type:logo') {
                        $items = $items->filter(function ($a) {
                            $_a = (array)$a;
                            try {
                                return is_logo($_a);
                            } catch (\Exception $e) {
                                Log::debug("Checking logo failed: " . $e->getMessage());
                                return false;
                            }
                        });
                    }

                    // Skip predefined filters
                    $filter = preg_replace('/(type|folder):("[^"]*+"|[^"\s]*+)/', '', $filter);
                    $filter = trim($filter);

                    if ($filter) {
                        $items = $items->filter(function ($a) use ($filter, $strict) {
                            if ($strict) {
                                return $a->name === $filter;
                            } else {
                                return stripos($a->name, $filter) !== false;
                            }
                        });
                    }
                }
            }
            // Count has to come before slicing and after filtering
            $a['count'] = count($items);

            while(($currentPage-1)*$perPage > $a['count']) {
                $currentPage -= 1;
            }
            // Only slice if result is not empty
            if ($perPage > 0 && $perPage < $a['count']) {
                $items = $items->slice(($currentPage - 1) * $perPage, $perPage);
            }
            // You have to reset keys otherwise json_encode in json() response converts this to an array
            $a['items'] = array_values($items->toArray());
            $a['labels'] = $labels;

            return $a;
        } catch (\UnexpectedValueException $e) {
            return [];
        }
    }

    /**
     * Get the information of a file
     *
     * @param int $id
     * @return array
     */
    public function getFile($id): ?array
    {
        return get_file($this->username, $id);
    }

    /**
     * @param $id
     * @param $label
     * @param  string  $category
     * @return array
     * @throws \Exception
     */
    public function copyFile($id, $label, $category = self::CATEGORY_DEFAULT)
    {
        $file = $this->getFile($id);

        $newId = time();
        $destination = $this->getStoragePath() . DIRECTORY_SEPARATOR . $newId . DIRECTORY_SEPARATOR;

        if ($category != self::CATEGORY_DEFAULT) {
            $destination .= $category . DIRECTORY_SEPARATOR;
        }

/*        if ($this->isUniqueFilename($label)) {
            $destination .= $label;
        } else {
            $uniqueFilename = $this->getUniqueFilename($label);
            $destination .= $uniqueFilename;
        }*/

        $destination .= $this->getUniqueFilename($label);

        if (!File::makeDirectory(File::dirname($destination), 0755, true)) {
            throw new \Exception(trans('errors.directory_creation_failure'));
        }

        if (!File::isDirectory(File::dirname($destination))) {
            throw new \Exception(trans('errors.directory_missing_failure'));
        }

        if (!File::copy($file['path'], $destination)) {
            throw new \Exception(trans('errors.copy_file_failure'));
        }

        if (!File::exists($destination)) {
            throw new \Exception(trans('errors.file_existince_failure'));
        }

        if (!File::isReadable($destination)) {
            throw new \Exception(trans('errors.file_readable_failure'));
        }

        if (!$this->link($destination,UserData::MEDIA_DIRECT_DIR)) {
            \Log::error("Creating link for destination $destination failed.");
        }

        $newFile = $this->getFile($newId);

        event(new FileSavedEvent($this, $newFile));

        return $newFile;
    }

    /**
     * @param $id integer
     * @param $label string New filename
     * @return bool
     * @throws \Exception
     */
    public function renameFile($id, string $label, $group = null)
    {
        return (new MediaManager($this))->rename($id, $label, $group);
    }

    /**
     * Get all symlinks to a specified file in the publish dir
     *
     * @param string $filename
     * @return array
     */
    public function getLinks(string $filename): array
    {
        $aLinks = [];

        try {
            $dIterator      = new \RecursiveDirectoryIterator(storage_path(get_user_public_path($this->username)));
            $rIterator      = new \RecursiveIteratorIterator($dIterator, \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rIterator as $file) {
                if ($file->isLink()) {
                    $link = readlink($file->getPathname());

                    if ($link == $filename || File::basename($link) == File::basename($filename)) {
                        $aLinks[] = $file->getPathname();
                    }
                }
            }
        } catch (\Exception $e) {
            return $aLinks;
        }

        return $aLinks;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteFile($id)
    {
        $usage = $this->usageFile($id);

        if ($usage->count() > 0) {
            $item = $usage->shift();
            if ($item->logo['itunes'] == $id) {
                throw new \Exception(trans('errors.file_delete_failure_file_is_used_as_channel_image', ['channel' => $item->rss['title']]), 406);
            }

            foreach ($item->entries as $entry) {
                if ($entry['show_media'] == $id) {
                    throw new \Exception(trans('errors.file_delete_failure_file_is_used_as_show', ['channel' => $item->rss['title'], 'show' => $entry['title']]), 406);
                } elseif ($entry['itunes']['logo'] == $id) {
                    throw new \Exception(trans('errors.file_delete_failure_file_is_used_as_show_image', ['channel' => $item->rss['title'], 'show' => $entry['title']]), 406);
                }
            }
            throw new \Exception(trans('errors.file_delete_failure_file_is_used'), 406);
        }

        $file = $this->getFile($id);

        foreach ($this->getLinks($file['path']) as $link) {
            unlink($link);
        }

        File::delete($this->getStoragePath() . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file['cat'] . DIRECTORY_SEPARATOR . $file ['name']);

        event(new FileDeletedEvent($this, $file));

        if (!is_null($file['cat'])) {
            File::deleteDirectory($this->getStoragePath() . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file['cat']);
        }

        return File::deleteDirectory($this->getStoragePath() . DIRECTORY_SEPARATOR . $id);
    }

    /**
     * @param $filename
     * @return bool
     * @throws \Exception
     */
    private function isUniqueFilename($filename): bool
    {
        $files = $this->getFiles('name', false, $filename, 1, 1, true);

        return $files['count'] < 1;
    }

    /**
     * @param $filename
     * @return string
     * @throws \Exception
     */
    public function getUniqueFilename($filename)
    {
        $path = pathinfo($filename);

        $ext = strtolower($path['extension']);

        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $allowDoublePoints = in_array($ext, ['txt', 'text', 'htm', 'html', 'pdf', 'doc', 'csv', 'odt']);
        $filename = make_url_safe($path['filename'], $allowDoublePoints) . '.' . $ext;

        // Do not allow hidden files (starting with .)
        // They fall through the check above as $path['filename'] is empty
        // as the leading point is regarded as part of extension
        $filename = ltrim($filename, '.');

        while (!$this->isUniqueFilename($filename)) {
            if (preg_match_all('/\((\d+)\)/', $filename, $matches)) {
                $file = pathinfo($filename);
                $a = array_pop($matches);
                $i = end($a);
                $j = $i+1;
                $filename = Str::replaceLast('(' . $i . ')', "({$j})", $file['filename']) . ($file['extension'] ? '.' . $file['extension'] : '');
            } else {
                $file = pathinfo($filename);
                $filename = $file['filename'] . '(1).' . $file['extension'];
            }
        }

        return $filename;
    }

    public function getAvailableStorage()
    {
        return Space::available()->whereUserId($this->id)->sum('space_available');
    }

    /**
     * Local scope to only select regular users
     *
     * @return mixed
     */
    public function scopeCustomer($query)
    {
        return $query->where('role_id', '=', self::ROLE_USER);
    }

    /**
     * Local scope to user with supporter status
     *
     * @return mixed
     */
    public function scopeSupporter($query)
    {
        return $query->where('is_supporter', '=', true);
    }

    /**
     * Local scope to user with supporter status
     *
     * @return mixed
     */
    public function scopeTrial($query)
    {
        return $query->where('is_trial', '=', self::IS_TRIAL);
    }

    /**
     * Sends an invitation (by mail) to inform / invite an user to take part in an ad campaign
     *
     * @param Campaign $campaign
     * @param Feed $feed
     */
    public function sendCampaignInvitation(Campaign $campaign, Feed $feed)
    {
        $this->notify(new CampaignInvitationNotification($campaign, $feed));
    }

    /**
     * Checks if the given file is used within a podcast as show or image
     *
     * @param $id
     * @return mixed
     */
    public function usageFile($id)
    {
        return Feed::owner()
            ->where(function($query) use ($id) {
                return $query->where('logo.itunes', '=', $id)
                    ->orWhere('entries.itunes.logo', '=', $id)
                    ->orWhere('entries.show_media', '=', $id);
            })
            ->get();
    }

    public function isSuperAdmin()
    {
        return $this->role_id === self::ROLE_ADMIN;
    }


    /**
     * Links public accessable file to stored file
     *
     * @param  string  $file  Full file name with path
     * @param $publicDir
     * @param  null  $feedId
     * @return bool
     * @throws \Exception
     */
    public function link($file, $publicDir, $feedId = null)
    {
        $link = $this->getPublishPath(basename($file), $feedId, $publicDir);

        if (is_link($link) && readlink($link) == $file) {
            return true;
        } elseif (is_link($link) && readlink($link) != $file) {
            unlink($link);
        }

        $path = dirname($link);
        $path = preg_replace('#/releases/\d+/#', '/current/', $path);

        // Ensure directory structure is existing
        File::ensureDirectoryExists($path);

        # softlink
        File::link($file, $link);

        return File::isReadable($link)
                && is_link($link)
                && readlink($link) == $file;
    }

    /**
     *
     *
     * @param  string  $filename
     * @param  string  $username
     * @param  string  $feedId
     * @param  string  $publicDir
     * @return string
     * @throws \Exception
     */
    private function getPublishPath($filename, $feedId, $publicDir)
    {
        $feedDir = ((!is_null($feedId)) ? $feedId . DIRECTORY_SEPARATOR : $feedId);

        return storage_path(Str::finish(get_user_public_path($this->username), DIRECTORY_SEPARATOR) . $feedDir . $publicDir) . DIRECTORY_SEPARATOR
            . $filename;
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);

        $this->addMediaConversion('itunes')
            ->width(1400)
            ->height(1400);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }

    public function addFunds(float $amount)
    {
        $this->funds += $amount;

        return $this->save();
    }

    public function subtractFunds(float $amount)
    {
        $this->funds -= $amount;

        return $this->save();
    }

    public function scopePionier($query)
    {
        return $query->has('pioniereContracts');
    }

    public function scopeNotPionier($query)
    {
        return $query->doesntHave('pioniereContracts');
    }

    public function pioniereContracts()
    {
        return $this->hasMany(AudiotakesContract::class, 'user_id', 'usr_id');
    }
}
