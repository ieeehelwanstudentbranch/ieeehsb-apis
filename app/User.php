<?php

namespace App;
use App\Volunteer;
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
        'firstName','lastName' ,'email', 'password','type' , 'confirmation_code'
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
        if (Volunteer::where('user_id',$this->id)->first() != null)
        {
            return $this->hasOne('App\Volunteer','user_id');
        }
        else{
            return $this->hasOne('App\Participant','user_id');
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
