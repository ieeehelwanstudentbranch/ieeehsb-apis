<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{

    use HasApiTokens, Notifiable;

    public $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','type' , 'confirmation_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function ptype()
            {
                switch ($this->type) {
                    case 'volunteer':
                        return $this->hasOne('App\Volunteer','user_id');
                        break;
                    case 'participant':
                        return $this->hasOne('App\Participant','user_id');
                        break;

                    }
            }


    public function notification(){
        return $this->hasMany(Notification::class);
    }
    public function getJWTIdentifier()
            {
                return $this->getKey();
            }
            public function getJWTCustomClaims()
            {
                return [];
            }
    public function hasType($type)
{
    return $this->type;
}
}
