<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class ExternalAuthor extends Model
{
    use Notifiable;

    protected $fillable = [
        'firstname',
        'surname',
        'email',
        'institution_name',
        'department_name',
        'orcid'
    ];

    public function panels()
    {
        return $this->belongsToMany('App\Models\Panel');
    }
}
