<?php

namespace App\Http\Controllers\Api\WebApp\SliderLogos;

use App\SliderLogos;
use App\EventStyle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SliderLogosController extends Controller
{
    public function updateStylesLogos(Request $request) {    
        return EventStyle::findOrFail($request->id)->update([
            'slider_logos' => $request->slider_logos,
        ]);
    }
    
    public function getAllSliderLogos( $eventId ) {
        $listLogos = SliderLogos::where('event_id', $eventId)->get();
        return response()->json($listLogos);
    }

    public function createNewLogo(Request $request) {
        Log::info("lega...");
        Log::info($request);
        $fileName = $this->saveFile( $request->file_logo );
        Log::info("nombre...");
        Log::info($fileName);

        $newLogo = SliderLogos::create([
            'title_logo' => $request->title_logo,
            'name_logo' => $fileName,
            'event_id' => $request->event_id,
        ]);
        return response()->json($newLogo);
    }

    public function removeItemLogo( $logoId ) {        
        $logo = SliderLogos::findOrFail($logoId);        
        Storage::disk('digitalocean')->delete($logo->name_logo);
        $logo->delete();
        return $this->successResponse(['data' => $logo, 'message' => 'Item eliminado'], 201);
    } 

    public function saveFile( $file )
    {
        try {
            $path = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');
            return $path;
        } catch (Exception $e) {
            return ' Error al subir el archivo '.$file;
        }

    }
}
