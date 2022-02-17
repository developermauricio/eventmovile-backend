<?php

namespace App\Http\Controllers\Api\Gallery;

use App\GalleryLikeWebApp;
use App\GalleryWebApp;
use App\Http\Controllers\Controller;
use App\Traits\response;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function uploadImage(Request $request){

        $picture = $request->file('picture');
        $production = env('APP_DEBUG');
//
        if ($production === true){
            $path = Storage::disk('public')->putFile('gallery', $picture, 'public');
            $urlFinal = '/storage/'.$path;
        }else{
            $urlFinal = Storage::disk('digitalocean')->putFile('gallery-web-app', $picture, 'public');
        }
        return $urlFinal;
    }

    public function removedImage(Request $request){
        $production = env('APP_DEBUG');
        $pathArchive = $request->get('urlPicture');
        $partes_ruta = pathinfo($pathArchive);
        if ($production === true){
            Storage::delete('gallery/' . $partes_ruta['basename']);
        }else{
            Storage::disk('digitalocean')->delete($pathArchive);
        }
        return response()->json('se eliminó correctamente', 201);
    }

    public function saveImage(Request $request){
        $user_id = $request->user_id;
        $event_id = $request->event_id;
        $description = $request->description;
        $picture = $request->picture;

        $gallery = GalleryWebApp::create([
           'user_id' => $user_id,
           'event_id' => $event_id,
           'description' => $description,
           'picture' => $picture
        ]);

        return response()->json('la imagen se guardo correctamente', 201);
    }

    public function saveLikeGallery(Request $request){
        $user_id = $request->user_id;
        $gallery_id = $request->gallery_id;

        $galleryLike = GalleryLikeWebApp::create([
           'user_id' => $user_id,
           'gallery_id' => $gallery_id
        ]);
        return response()->json('Like guardado correctamente', 201);
    }
    public function removeLikeGallery($id){

        $galleryLike = GalleryLikeWebApp::where('id', $id)->delete();
        return response()->json('El like se eliminó correctamente', 201);
    }

    public function getDataGallery($id){
        $event_id = $id;
        $gallery = GalleryWebApp::where('event_id', $event_id)->with('user', 'galleryLike')->latest('created_at')->paginate(10);

        return response()->json($gallery);
    }

    public function getDatalLikeGallery($id, $user){

        $galleryLike = DB::table('gallery_like_web_apps')
            ->where('gallery_id', $id)
            ->where('user_id', $user)
            ->first();
        return response()->json($galleryLike);
    }
}
