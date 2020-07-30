<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Panel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'caption',
        'user_id',
        'type',
        'subtype',
        'clicks',
        'downloads',
        'made_public_at'
    ];

    public function image()
    {
        return $this->hasOne('App\Models\Image');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function authors()
    {
        return $this->belongsToMany('App\User')->withPivot('role')->as('role');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group')->withTimestamps();
    }

    public function files()
    {
        return $this->hasMany('App\Models\File');
    }

    public function figures()
    {
        return $this->belongsToMany('App\Models\Figure')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag')->as('meta')->withTimestamps();
    }

    public function accessToken()
    {
        return $this->hasOne('App\Models\PanelAccessToken');
    }

    public function scopeOrderByUpdated($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    public function getSavePathAttribute()
    {
        return /*config("filesystems.disks.panels.root") 'panels' . DIRECTORY_SEPARATOR .*/ $this->id . DIRECTORY_SEPARATOR;
    }
}
