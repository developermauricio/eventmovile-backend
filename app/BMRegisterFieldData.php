<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BMRegisterFieldData extends Model
{
    //
    protected $table = 'bm_register_fields_data';

    protected $fillable = [ 'bmr_field_id', 'user_id',  'value'];
}
