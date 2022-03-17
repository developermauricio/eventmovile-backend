<?php

namespace App\Http\Controllers\Api\WebApp\CountriesCities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CountryEvent;
use App\CityEvent;

class CountriesCitiesController extends Controller
{
    public function getCountries(){
        $countries = CountryEvent::all();
        return response()->json(['data' => $countries]);
    }
    
    public function getCities($code){
        $cities = CityEvent::where('country_code', $code)->get();
        return response()->json(['data' => $cities]);
    }
}
