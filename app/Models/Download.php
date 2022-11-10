<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

class Download extends Model
{
    protected $table = 'downloads';

    protected $connection = 'mongodb';

    protected $primaryKey = '_id';

    protected $dates = ['date', 'created'];

    private $username;

    protected $fillable = [
        'username'
    ];

    public function __construct(array $attributes = array())
    {
        if (isset($attributes['username']) && !empty($attributes['username'])) {
            $this->username = $attributes['username'];
        }
        parent::__construct($attributes);
    }

/*    public function getDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('Y-m-d', $value);
    }*/

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
