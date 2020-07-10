<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mime_type',
        'original_filename',
        'filename',
        'preview_filename',
        'panel_id',
        'file_size',
        'is_archived'
    ];

    protected $hidden = [
        'filename',
        'deleted_at',
    ];

    public function panel()
    {
        return $this->belongsTo('App\Models\Panel');
    }
}
