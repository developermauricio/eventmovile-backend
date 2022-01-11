<?php

namespace App\Http\Controllers\Api\EmailTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Email;
use App\EmailTrackingBM;
use Illuminate\Support\Facades\DB;

class EmailTrackingController extends Controller
{
    public function store($id,$action = "viewed"){
        EmailTrackingBM::create([
            "email_id"=>$id,
            "action"=>$action
        ]);

        $response = Response::make(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII='), 200);
        $response->header("Content-Type", 'image/png');
        return $response;
    }

    public function report($model, $id, $type = "registered", $action = "view"){
        $report = DB::table('email-tracking as et')
        ->select('u.name as nombre', 'e.email', 'e.created_at as enviado', 'et.created_at as abierto')
        ->join('emails as e','e.id','et.email_id')
        ->join('users as u', 'u.email', 'e.email')
        ->where('e.model',$model)
        ->where('e.model_id', $id)
        ->where('e.type', $type)
        ->where('et.action',$action)
        ->get();

        return $this->showAll($report,200);  
    }
}
