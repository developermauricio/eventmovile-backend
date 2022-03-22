<?php

namespace App\Http\Controllers\Api\Speaker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Speaker;

class SpeakerController extends Controller
{
    public function index() {

        $speakers = Speaker::orderBy('id', 'desc')->get();

        return $this->showAll($speakers,200);
    }

    public function show(Speaker $speaker)
    {
    
        return $this->showOne($speaker);
    }

    public function store(Request $request){

        $country = json_decode($request->country_id);

        $rules = [
            'name'              => 'required',
            'country_id'        => 'required',
            'sort_description'  => 'required',
            'pic'               => 'required|file' 
        ];

        $this->validate($request, $rules);

        $file = $request->pic;
    
        $nameFile = $file->getClientOriginalName();

        $number = Speaker::count();

        $explode = explode(".", $nameFile);
        //$nameFile = $explode[0]."_speaker_".$request->name."_".$number.".".$explode[1];
        
        //Storage::disk('local')->put($nameFile,  \File::get($file));
        $nameFile = $this->saveFile($file, 'banner', 'hall');
        $speaker = Speaker::create([
            'name'              => $request->name,
            'sort_description'  => $request->sort_description,
            'pic'               => $nameFile,
            'country_id'        => $country->id,
        ]);

        return $this->successResponse(['data'=> $speaker, 'message'=>'Speaker Created'], 201);
    }

    public function update(Request $request, Speaker $speaker){
        
        $rules = [
            'name'              => 'required',
            'sort_description'  => 'required',
            'pic'               => 'required|file' 
        ];

        $this->validate($request, $rules);

        $file = $request->pic;
    
        $nameFile = $file->getClientOriginalName();

        $number = Speaker::count();

        $explode = explode(".", $nameFile);
        $nameFile = $explode[0]."_speaker_".$request->name."_".$number.".".$explode[1];
        
        $this->validate($request, $rules);

        $request->pic = $nameFile;
                
        $speaker->fill($request->all());
        
        if ($speaker->isClean()) {
            return $this->successResponse(['data' => $speaker, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $speaker->save();

        return $this->successResponse(['data' => $speaker, 'message' => 'Speaker Updated'],201);
    }

    public function destroy(Speaker $speaker)
    {
        $speaker->delete();   
        return $this->successResponse(['data' => $speaker, 'message' => 'Speaker Deleted'], 201);
    }

    public function saveFile($pic, $type, $name)
    {
        
        $file = $pic;
        try {
        # Storage::disk('local')->put($nameFile,  \File::get($file));
            # Storage::disk('digitalocean')->put($nameFile, \File::get($file));
            $path = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');
            return $path;
        } catch (Exception $e) {
            return ' Error al subir el archivo '.$file;
        }

    }
}
