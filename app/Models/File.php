<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'panel_id',
        'original_filename',
        'filename',
        'file_category_id',
        'type',
        'url',
        'description',
        'mime_type',
        'file_size',
        'version',
        'is_archived'
    ];

    protected $hidden = [
        'filename',
        'deleted_at',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\FileCategory');
    }

    public function panel()
    {
        return $this->belongsTo('App\Models\Panel');
    }
}
