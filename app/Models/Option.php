<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Corcel\Model\Option as Corcel;

class Option extends Corcel
{
    use HasFactory;

    public function blog()
    {
        return $this->belongsTo(\App\Models\Blog::class, 'blog_id', null, 'blog_id');
    }
}
