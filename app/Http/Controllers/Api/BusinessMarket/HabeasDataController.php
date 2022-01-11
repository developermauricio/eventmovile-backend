<?php

namespace App\Http\Controllers\Api\BusinessMarket;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\BMHabeasData;

class HabeasDataController extends Controller
{
    public function index(){

        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        if($role == "super admin"){
            $habeasDatas =BMHabeasData::all();
        }
        if($role == "admin"){

            

        }
        if($role == "guest"){
            
        }

        return $this->showAll($habeasDatas,200);
    }
    public function show($business_id){
        $habeasData = BMHabeasData::where('business_id', $business_id)->orderByDesc('id')->first();
        return $this->showOne($habeasData);
    }

    public function update(Request $request, $id){
        $habeasData = BMHabeasData::find($id);
        $rules = [
            'type'      => 'required',
            'position'  => 'required',
            'content'   => 'required',
        ];

        $this->validate($request, $rules);

        $toSave = $request->all();
        if(is_file($request->content)){
            $file = $request->content;
            $nameFile = $file->getClientOriginalName();
            $toSave['content'] = $nameFile;
        }

        if($request->type == 'file' && $habeasData->url != $request->content){
            $explode = explode(".", $nameFile);
            $number = BMHabeasData::count();            
            $nameFile = $explode[0]."_".$request->model_type."_".$request->model_id."_".$number.".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file));
            $toSave['content'] = $nameFile;
        }

        $habeasData->update($toSave);
        
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
            'content'   => 'required',
            'business_id'  => 'required|exists:business_markets,id',
        ];

        $this->validate($request, $rules);
        $toSave = $request->all();
        if($request->type == 'file'){
            $file = $request->content;
        
            $nameFile = $file->getClientOriginalName();

            $number = BMHabeasData::count();

            $explode = explode(".", $nameFile);
            $toSave['content'] = $explode[0]."_".$request->model_type."_".$request->model_id."_".$number.".".$explode[1];
            
            Storage::disk('local')->put($nameFile,  \File::get($file));

        }else{
            $toSave['content'] = $request->content;
        }

        $habeasData = BMHabeasData::create($toSave);

        return $this->successResponse(['data'=> $habeasData, 'message'=>'HabeasData Created'], 201);
    }

    public function destroy($id)
    {
        $habeasData = BMHabeasData::find($id);
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