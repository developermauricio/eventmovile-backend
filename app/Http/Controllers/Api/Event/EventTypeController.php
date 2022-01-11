<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\EventType;


class EventTypeController extends Controller
{
    public function index (){

        $types = EventType::where('actived', true)->get();
    
        return $this->showAll($types, 201);
    }
}
