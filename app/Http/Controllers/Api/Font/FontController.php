<?php

namespace App\Http\Controllers\Api\Font;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Font;

class FontController extends Controller
{
    public function index(){

        $fonts = Font::all();

        return $this->showAll($fonts,200);
    }

    public function show(Font $font){

        return $this->showOne($font,200);
    }
}
