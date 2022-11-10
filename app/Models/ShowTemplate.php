<?php

namespace App\Models;

use App\Models\Base\ShowTemplate as BaseShowTemplate;

class ShowTemplate extends BaseShowTemplate
{
	protected $fillable = [
		'user_id',
		'name',
		'feed_id',
		'title',
		'description',
		'author',
		'copyright',
		'link',
		'itunes_title',
		'itunes_subtitle',
		'itunes_summary',
		'itunes_episode_type',
		'itunes_season',
		'itunes_explicit',
		'itunes_logo',
		'is_public'
	];

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
