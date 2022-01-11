<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\TypeActivity;

class TypeActivityController extends Controller
{
    public function index(){

        $typesActivity = TypeActivity::all();
        
        return $this->showAll($typesActivity,200);

    }
}
