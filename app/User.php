<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

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

    public function panels()
    {
        return $this->hasMany('App\Models\Panel');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group');
    }

    public function confirmedGroups()
    {
        return $this->belongsToMany('App\Models\Group')->wherePivot('status','confirmed');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
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
