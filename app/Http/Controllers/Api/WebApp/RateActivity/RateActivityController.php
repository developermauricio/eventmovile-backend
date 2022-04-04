<?php

namespace App\Http\Controllers\Api\WebApp\RateActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RateActivity;

class RateActivityController extends Controller
{
    public function saveRateActivity(Request $request){

        $rate = RateActivity::create([
            'activity_id' => $request->activity_id,
            'user_id' => $request->user_id,
            'rate' => $request->rate,
        ]);

        return response()->json('Se guardo la calificación correctamente');
    }
}
