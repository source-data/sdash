<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{

    use SoftDeletes;

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

    /**
     * Laravel Accessor for the comment text field - returns a standard
     * message instead of the comment text if the comment is soft-deleted
     *
     * @param [type] $comment
     * @return string
     */
    public function getCommentAttribute($comment)
    {
        return $this->deleted_at ? "Comment deleted by user" : $comment;
    }
}
