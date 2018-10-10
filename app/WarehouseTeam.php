<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WarehouseTeam extends Authenticatable
{
        use Notifiable;

        protected $guard = 'warehouse';
        protected $table = 'warehouse_team';

        protected $fillable = [
            'name', 'email', 'password',
        ];

        protected $hidden = [
            'password', 'remember_token',
        ];
}