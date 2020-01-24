<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'origin',
        'role',
        'type'
    ];

    public function panels(Type $var = null)
    {
        return $this->belongsToMany('App\Models\Panel')->withTimestamps();
    }
}
