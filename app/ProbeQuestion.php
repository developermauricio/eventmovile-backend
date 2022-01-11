<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProbeQuestion extends Model
{
    protected $table = 'probes_questions';
    
    protected $fillable = [
        'probe_id', 'description',  'options', 'required', 'type_question_id', 'required', 'position'
    ];

    public function answers(){
        return $this->hasMany('App\ProbeAnswer','question_id','id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($question) { // before delete() method call this
             $question->answers()->each(function($answer) {
                $answer->delete(); // <-- direct deletion
             });
             
             
        });
    }
}
