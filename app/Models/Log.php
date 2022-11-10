<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mongodb';

    protected $dates = ['created'];

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
