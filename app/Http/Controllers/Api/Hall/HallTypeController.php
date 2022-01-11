<?php

namespace App\Http\Controllers\Api\Hall;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HallType;

class HallTypeController extends Controller
{
    public function index (){

        $types = HallType::where('actived', true)->get();
    
        return $this->showAll($types, 201);
    }
}
