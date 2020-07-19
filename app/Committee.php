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


    public function volunteer()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = student_id, localKey = id)
        return $this->belongsToMany('App\Volunteer','vol_committees','committee_id','vol_id')
        ->withPivot('position','season_id', 'created_at');
    }
    public function chapter()
    {
    	return $this->belongsTo(Chapter::class,'chapter_id');
    }
}
