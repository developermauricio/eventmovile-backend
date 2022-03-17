<?php

namespace App\Http\Controllers\Api\WebApp\Probe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProbeAnswer;

class ProbeController extends Controller
{
    public function probeSaveWebApp(Request $request){

        $probeAnswer = json_decode($request->probe_answer);
        
        foreach ($probeAnswer as $value) {
        
                $answer = new ProbeAnswer;
                $answer->probe_id = $request->probe_id;
                $answer->question_id = $value->id;
                $answer->value = $value->value;
                $answer->user_id = $request->user_id;
                $answer->save();
        
        }
        return response()->json('Las respuestas se guardaron correctamente');

    }

    public function verifyUserProbe($id){
        $probeUser = ProbeAnswer::where('user_id', $id)->get();
        return $probeUser; 
    }
}
