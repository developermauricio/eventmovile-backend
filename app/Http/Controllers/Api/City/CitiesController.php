<?php

namespace App\Http\Controllers\Api\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{
    public function deparments(){
        return DB::table('department')
            ->select('id', 'code','name')
            ->get();
    }

    public function citiesByDeparment($id){
        return DB::table('cities')
            ->select('code','name')
            ->where('department_id',$id)
            ->get();
    }
}
