<?php

namespace App;
use App\Volunteer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    public $table = 'committees';
    public $created_at= false;
    public $updated_at=false;


    public function volunteer(){
        return $this->hasMany(Volunteer::class,'id','vol_id');
    }
}
