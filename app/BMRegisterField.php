<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BMRegisterField extends Model
{
    protected $table = 'bm_register_fields';

    protected $fillable = [ 'name', 'type',  'options', 'required', 'business_id'];

    public function data()
    {
        return $this->hasMany('App\BMRegisterFieldData', 'bmr_field_id', 'id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($field) { // before delete() method call this
             $field->data()->each(function($data) {
                $data->delete(); // <-- direct deletion
             });
             
        });
    }
    
}
