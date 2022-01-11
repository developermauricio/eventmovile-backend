<?php

namespace App\Http\Controllers\Api\Certificate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Certificate;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\CertificateTrackingModel;
use Illuminate\Support\Facades\DB;
class CertificateController extends Controller
{
    public function saveFile($file){
        try{
            
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $current_timestamp = microtime();
            $nameFile = "Certificate_".$current_timestamp."-".substr(str_shuffle($permitted_chars), 0, 3).".json";
            Storage::disk('local')->put($nameFile, file_get_contents($file)); 
            return $nameFile;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
    
    public function store(Request $request){
        $rules = [
            'model_id'  => 'required',
            'json' => 'required'
        ];
        $this->validate($request, $rules);

        $bm = Certificate::where('model_id',$request->model_id)->first();
        if(!$bm){
            $toSave = $request->all();
            if(isset($request->json) && $request->json != ""){
                $nameFile = $this->saveFile($request->json);
                $toSave['name_file'] = $nameFile;
            }

            $certificate = Certificate::create($toSave);
            return $this->successResponse(['data'=> $certificate, 'message'=>'Created'], 201);
        }
    }

    public function update(Request $request, $certificate){
        $id = $certificate;
        $certificate = Certificate::find($id);
        if(!$certificate){
            $certificate = Certificate::where('model_id', $id)->first();
            if(!$certificate){
                return $this->errorResponse("Certificado no encontrado", 500);
            }
        }
        
        $toSave = $request->all();
        
        //return $toSave;
        $nameFile = $this->saveFile($request->json);
        $toSave['name_file'] = $nameFile;
       

        $certificate->update($toSave);
        return $this->successResponse(['data'=> $certificate, 'message'=>'Updated'], 200);
    }

    public function show($event, $tracking = false){
        $certificate = Certificate::where('model_id',$event)->firstOrFail();
        return $this->showOne($certificate);
    }

    public function trackingDownload($event){
        $certificate = Certificate::where('model_id',$event)->where('model','event')->first();
        if($certificate){
            $user = auth()->user();
            $tracking = CertificateTrackingModel::create([
                'user_id'=>$user->id,
                'certificate_id'=>$certificate->id
            ]);
            if($tracking)
                return $this->successResponse(['data'=> "", 'message'=>'Tracking created'], 200); 
        }

        return $this->errorResponse("Ocurrio error", 500);
    }

    public function trackingReport($event, $type = 'all'){
        $tracking = DB::table('certificate-tracking')
            ->select('certificate-tracking.action as accion','certificate-tracking.created_at as fecha','events.name as evento','users.name as usuario' , 'users.id as id_usuario')
            ->join('certificate','certificate-tracking.certificate_id','certificate.id')
            ->leftJoin('events','certificate.model_id','events.id')
            ->leftJoin('users','certificate-tracking.user_id','users.id')
            ->where('certificate.model_id',$event)
            ->where('certificate.model','event')
            ->get();
        
        if($type == 'all')
            return $this->showAll($tracking,200);
            
        if($type  == 'filter')
            $trackingUnique = $tracking->unique('id_usuario');
            return $this->showAll($trackingUnique,200);        
    }
}
