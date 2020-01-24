<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'comment',
        'reply_to',
        'panel_id',
        'group_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function panel()
    {
        return $this->belongsTo('App\Models\Panel');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }


}
