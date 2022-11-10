<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $connection = 'mongodb';

    protected $primaryKey = '_id';

    protected $dates = ['created'];

    private $username;

    protected $fillable = [
        'username'
    ];

/*    public function getDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('Y-m-d', $value);
    }*/

    /**
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (isset($attributes['username']) && !empty($attributes['username'])) {
            $this->username = $attributes['username'];
        }
        parent::__construct($attributes);
    }

    public function getTable()
    {
        if (app()->runningInConsole() || $this->username) {
            $username = $this->username;
        } else {
            $username = auth()->user()->username;
        }

        return parent::getTable() . '_' . $username;
    }
}
