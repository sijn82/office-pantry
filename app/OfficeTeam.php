<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OfficeTeam extends Authenticatable
{
        use HasApiTokens, Notifiable;

        protected $guard = 'office';
        protected $table = 'office_team';

        protected $fillable = [
            'name', 'email', 'password', 'api_token',
        ];

        protected $hidden = [
            'password', 'remember_token', 'api_token'
        ];
}
