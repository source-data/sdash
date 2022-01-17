<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'cover_photo',
        'url',
        'is_public',
    ];

    public function panels()
    {
        return $this->belongsToMany('App\Models\Panel')->withTimestamps();
    }

    public function publicPanels()
    {
        return $this->belongsToMany('App\Models\Panel')
            ->where('is_public', true)
            ->withTimestamps()
            ->orderBy('pivot_created_at', 'asc');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function confirmedUsers()
    {
        return $this->belongsToMany('App\User')->wherePivot('status', 'confirmed');
    }

    public function requestedUsers()
    {
        return $this->belongsToMany('App\User')->wherePivot('status', 'requested');
    }

    public function administrators()
    {
        return $this->belongsToMany('App\User')
            ->wherePivot('status', 'confirmed')
            ->wherePivot('role', 'admin');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }
}
