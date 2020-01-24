<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Figure extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function panels()
    {
        return $this->belongsToMany('App\Models\Panel')->withTimestamps();
    }
}
