<?php

namespace App\Models;

use App\Models\Base\Team as BaseTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends BaseTeam
{
    use HasFactory;

	protected $fillable = [
		'owner_id',
		'name'
	];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($query) {
            $query->name = $query->name ?? trans('teams.text_default_team_name');
        });
    }

    public function scopeOwner($query)
    {
        return $query->where('owner_id', '=', auth()->id());
    }
}
