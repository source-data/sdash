<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The roles that a user can have on a panel
     */
    const PANEL_ROLE_AUTHOR = 'author';
    const PANEL_ROLE_CURATOR = 'curator';
    const PANEL_ROLE_CORRESPONDING_AUTHOR = 'corresponding';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'surname',
        'email',
        'password',
        'role',
        'institution_name',
        'institution_address',
        'department_name',
        'linkedin',
        'twitter',
        'orcid'
    ];

    public function is_superadmin()
    {
        return $this->role === 'superadmin';
    }

    public function is_admin()
    {
        return $this->role === 'admin';
    }

    /**
     * The panels that were uploaded by this user
     */
    public function panels()
    {
        return $this->hasMany('App\Models\Panel');
    }

    /**
     * All panels where this user has an authorial role - whether author, curator or corresponding author
     */
    public function authoredPanels()
    {
        return $this->belongsToMany('App\Models\Panel')->withPivot('role')->as('role');;
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group');
    }

    public function confirmedGroups()
    {
        return $this->belongsToMany('App\Models\Group')->wherePivot('status', 'confirmed');
    }

    public function pendingGroups()
    {
        return $this->belongsToMany('App\Models\Group')->wherePivot('status', 'pending');
    }

    public function panelLogRecords()
    {
        return $this->hasMany('App\Models\PanelLog');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
