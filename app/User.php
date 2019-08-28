<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use HasApiTokens, Notifiable;

    public $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function ex_com_option(){
        return $this->hasOne(Ex_com_options::class);
    }

    public function high_board_option(){
        return $this->hasOne(HighBoardOptions::class);
    }

    public function committee(){
        return $this->belongsTo(Committee::class);
    }
    public function comment(){
        return $this->hasMany(Comment::class);
    }
    public function post(){
        return $this->hasMany(Post::class);
    }
    public function task(){
        return $this->hasMany(Task::class);
    }
    public function notification(){
        return $this->hasMany(Notification::class);
    }
}
