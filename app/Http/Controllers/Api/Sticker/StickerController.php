<?php

namespace App\Http\Controllers\Api\Sticker;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sticker;
use Exception;

class StickerController extends Controller
{
    public function index($event){
        $stickers = Sticker::where('event_id',$event)->orderBy('id', 'desc')->orderBy('id')->get();
        if($stickers)
            return $this->showAll($stickers,200);
        
        return $this->errorResponse('Stickers not found', 404);
    }

    public function show($sticker)
    {
        $sticker = Sticker::where('id',$sticker)->with('event')->first();
        if($sticker)
            return $this->showOne($sticker);
        
        return $this->errorResponse('Stickers not found', 404);
    }

    public function showEventSticker($event)
    {
        $sticker = Sticker::where('event_id',$event)->with('event')->first();
        if($sticker)
            return $this->showOne($sticker);
        
        return $this->errorResponse('Stickers not found', 404);
    }

    public function store(Request $request){
        $rules = [
            'event_id'   => 'required|exists:events,id',
        ];

        $this->validate($request, $rules);
        $toSave = $request->all();

        if($toSave['name'] == ""){
            $fecha = date_create();
            $toSave['name'] = "Sticker_".date_timestamp_get($fecha);
        }
        $sticker = Sticker::create($toSave);
        return $this->successResponse(['data'=> $sticker, 'message'=>'Sticker Created'], 201);
    }

    public function update(Request $request, $sticker)
    {
        try{
            $sticker = Sticker::find($sticker);
            $toSave = $request->all();
            if(isset($toSave['name']) && $toSave['name'] == ""){
                $fecha = date_create();
                $toSave['name'] = "Sticker_".date_timestamp_get($fecha);
            }

            
            
            if(isset($request->json) && $request->json != ""){
                if($sticker->file)
                    Storage::disk('local')->delete($sticker->file);
                $nameFile = $this->saveFile($request->json);
                $toSave['file'] = $nameFile;
            }

            $sticker->update($toSave);
            return $this->successResponse(['data'=> $sticker, 'message'=>'Sticker Updated'], 200); 
        } catch(Exception $e){
            return $this->errorResponse("Ocurrio error", 500);
        }

    }

    
    public function destroy($sticker)
    {
        try{
            $sticker = Sticker::find($sticker);
            $sticker->delete();
            return $this->successResponse(['data'=> "", 'message'=>'Sticker Deleted'], 200); 
        }catch(Exception $e){
            //Log::error($e->getMessage());
            return $this->errorResponse("Ocurrio error", 500);
        }
    }

    public function saveFile($file){
        try{ 
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $current_timestamp = microtime();
            $nameFile = "Sticker_".$current_timestamp."-".substr(str_shuffle($permitted_chars), 0, 3).".json";
            Storage::disk('local')->put($nameFile, file_get_contents($file)); 
            return $nameFile;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
