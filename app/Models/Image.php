<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
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
        'panel_id'
    ];

    public function panel()
    {
        return $this->belongsTo('App\Models\Panel');
    }
}
