<?php

namespace App\Models;

use App\Models\Base\HelpVideo as BaseHelpVideo;
use Illuminate\Support\Str;

class HelpVideo extends BaseHelpVideo
{
	protected $fillable = [
		'page_title',
		'page_description',
		'title',
		'subtitle',
		'content',
		'username',
		'poster',
		'mp4',
		'webm',
		'ogv',
		'is_public'
	];

    public function getLink($type)
    {
        if (!$this->{$type}) {
            if ($type == 'poster') {
                return asset('images1/help/video-placeholder.png');
            }
            return null;
        }
        $file = get_file($this->username, $this->{$type});

        if ($file) {
            return get_direct_uri($this->username, 'download', $file['name']);
        }

        return null;
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->title);
    }
}
