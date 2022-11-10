<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $connection = 'wordpress';

    /**
     * @var string
     */
    protected $primaryKey = 'blog_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'domain',
        'path',
    ];

    public function site()
    {
        return $this->belongsTo(\App\Models\Site::class);
    }
}
