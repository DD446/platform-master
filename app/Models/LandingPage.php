<?php

namespace App\Models;

use App\Models\Base\LandingPage as BaseLandingPage;
use App\Scopes\IsPublicScope;
use Illuminate\Support\Str;

class LandingPage extends BaseLandingPage
{
	protected $fillable = [
		'page_title',
		'page_description',
		'title',
		'subtitle',
		'teaser',
		'content'
	];

    protected static function boot()
    {
        self::addGlobalScope(new IsPublicScope);

        parent::boot();
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->title,'-', config('app.locale'));
    }
}
