<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanelLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'panel_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'panel_id',
        'action_type',
        'license_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function panel()
    {
        return $this->belongsTo('App\Models\Panel');
    }

    public function license()
    {
        return $this->belongsTo('App\Models\License');
    }
}
