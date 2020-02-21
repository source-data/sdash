<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanelAccessToken extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'panel_id',
        'qr_image_name',
        'expires'
    ];

    public function panel()
    {
        return $this->belongsTo('\App\Models\Panel');
    }
}
