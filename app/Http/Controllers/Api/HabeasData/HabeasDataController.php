<?php

namespace App\Http\Controllers\Api\HabeasData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\HabeasData;

class HabeasDataController extends Controller
{
    public function index(){

        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        if($role == "super admin"){
            $habeasDatas =HabeasData::all();
        }
        if($role == "admin"){

            

        }
        if($role == "guest"){
            
        }

        return $this->showAll($habeasDatas,200);
    }
    public function show($event){
        $habeasData = HabeasData::where('event_id', $event)->first();
        return $this->showOne($habeasData);
    }

    public function update(Request $request, HabeasData $habeasData){

        $rules = [
            'type'      => 'required',
            'position'  => 'required',
            'content'   => 'required',
        ];

        $this->validate($request, $rules);


        if(is_file($request->content)){
            $file = $request->content;
            $nameFile = $file->getClientOriginalName();
            $request->content = $nameFile;
        }

        if($request->type == 'file' && $habeasData->url != $request->content){
            $explode = explode(".", $nameFile);
            $number = HabeasData::count();            
            $nameFile = $explode[0]."_".$request->model_type."_".$request->model_id."_".$number.".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file));
            $request->content = $nameFile;
        }

        $habeasData->type = $request->type;
        $habeasData->url = $request->content;
        $habeasData->position = $request->position;

        
        if ($habeasData->isClean()) {
            return $this->successResponse(['data' => $habeasData, 'message' => 'At least one different value must be specified to update'],201);
        }
        $habeasData->save();

        return $this->successResponse(['data' => $habeasData, 'message' => 'HabeasData Updated'],201);

        
    }

    public function store(Request $request){

        $rules = [
            'type'      => 'required',
            'position'  => 'required',
            'event_id'  => 'required|exists:events,id',
        ];

        $hdValidation = HabeasData::where('event_id', $request->event_id)->first();
        if(isset($hdValidation->id)){
            $hdValidation->delete();
        }

        $this->validate($request, $rules);

        if($request->type == 'file'){
            $file = $request->content;
        
            $nameFile = $file->getClientOriginalName();

            $number = HabeasData::count();

            $explode = explode(".", $nameFile);
            $nameFile = $explode[0]."_".$request->model_type."_".$request->model_id."_".$number.".".$explode[1];
            
            Storage::disk('local')->put($nameFile,  \File::get($file));

        }else{
            $nameFile = $request->content;
        }

        $habeasData = HabeasData::create([
            'content'   => $nameFile,
            'type'      => $request->type,
            'position'  => $request->position,
            'event_id'  => $request->event_id,  
        ]);

        return $this->successResponse(['data'=> $habeasData, 'message'=>'HabeasData Created'], 201);
    }

    public function destroy(HabeasData $habeasData)
    {
        if($habeasData->type == 'text'){
            $habeasData->delete();   
            return $this->successResponse(['data' => $habeasData, 'message' => 'Habeas Data Deleted'], 201);
        }else{
            Storage::delete($habeasData->content);
            
            $habeasData->delete();   
            return $this->successResponse(['data' => $habeasData, 'message' => 'Habeas Data Deleted'], 201);
        }
    }
}
