<?php

namespace App\Models;

use App\Mail\PodcastRouletteMatchMail;
use App\Models\Base\PodcastRouletteMatch as BasePodcastRouletteMatch;
use Illuminate\Support\Facades\Mail;

class PodcastRouletteMatch extends BasePodcastRouletteMatch
{
	protected $fillable = [
		'roulette_id',
		'roulette_partner_id',
		'file_id',
		'cover_id',
		'shownotes',
		'version',
	];

    protected static function boot()
    {
        // auto-sets values on creation
        static::created(function(PodcastRouletteMatch $match) {
            $match->sendNotification();
        });

        parent::boot();
    }

    public function sendNotification()
    {
        Mail::to($this->player->user)->queue(new PodcastRouletteMatchMail($this));
        Mail::to($this->partner->user)->queue(new PodcastRouletteMatchMail($this, true));
    }
}
