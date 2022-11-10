<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastRoulette extends \App\Models\Base\PodcastRoulette
{
    use HasFactory;

    const ROULETTE_ROUND = 2;

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function match()
    {
        return $this->hasOne(\App\Models\PodcastRouletteMatch::class, 'id', 'roulette_id');
    }

    public function partnerMatch()
    {
        return $this->hasMany(\App\Models\PodcastRouletteMatch::class, 'id', 'roulette_partner_id');
    }

    public function matches()
    {
        if ($this->match) {
            return $this->match->merge($this->partnerMatch);
        }

        return $this->partnerMatch();
    }
}
