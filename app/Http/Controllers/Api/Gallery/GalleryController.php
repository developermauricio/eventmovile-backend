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
    public function uploadImage(Request $request)
    {

        $picture = $request->file('picture');
        $production = env('APP_DEBUG');
//
        if ($production === true) {
            $path = Storage::disk('public')->putFile('gallery', $picture, 'public');
            $urlFinal = '/storage/' . $path;
        } else {
            $urlFinal = Storage::disk('digitalocean')->putFile('gallery-web-app', $picture, 'public');
        }
        return $urlFinal;
    }

    public function removedImage(Request $request)
    {
        $production = env('APP_DEBUG');
        $pathArchive = $request->get('urlPicture');
        $partes_ruta = pathinfo($pathArchive);
        if ($production === true) {
            Storage::delete('gallery/' . $partes_ruta['basename']);
        } else {
            Storage::disk('digitalocean')->delete($pathArchive);
        }
        return response()->json('se eliminó correctamente', 201);
    }

    public function saveImage(Request $request)
    {
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

    public function saveLikeGallery(Request $request)
    {
        $user_id = $request->user_id;
        $gallery_id = $request->gallery_id;

        $galleryLike = GalleryLikeWebApp::create([
            'user_id' => $user_id,
            'gallery_id' => $gallery_id
        ]);
        return response()->json(['Like guardado correctamente', 'idlike' => $galleryLike->id]);
    }

    public function removeLikeGallery($id)
    {

        $galleryLike = GalleryLikeWebApp::where('id', $id)->delete();
        return response()->json('El like se eliminó correctamente', 201);
    }

    public function getDataGallery($event_id, $user_id)
    {


//        $event_id = 100;
//        $user = 16273;
//        $gallery = GalleryWebApp::where('event_id', $event_id)->with('user', 'galleryLike')->latest('created_at')->paginate(10);
//
//        return response()->json($gallery);
        $galleries = GalleryWebApp::where('event_id', $event_id)->with(['user', 'galleryLike' => function ($q) use ($user_id){
            $q->where('user_id', $user_id);
        }])->latest('created_at')->paginate(10);
        $finalData = collect();
//        return  dd($galleries->galleryLike);
        foreach ($galleries as $gallery) {
            $finalData->push([
                "id" => $gallery->id,
                "event_id" => $gallery->event_id,
                "user_id" => $gallery->user_id,
                "picture" => $gallery->picture,
                "description" => $gallery->description,
                "created_at" => $gallery->created_at,
                "updated_at" => $gallery->created_at,
                "is_like" => $gallery->isLiked($user_id),
                "user" => $gallery->user,
                "gallery_like" => $gallery->galleryLike,

            ]);

        }
//        dd($galleries->lastPage());

        return response()->json([ 'data' => $finalData, "lastPage" => $galleries->lastPage()]);
//        return response()->json($galleries);

    }

    public function getDataGalleryHome($id)
    {
        $event_id = $id;
        $gallery = GalleryWebApp::where('event_id', $event_id)->latest()
            ->take(5)
            ->get();

        return response()->json($gallery);
    }

    public function getDatalLikeGallery($id, $user)
    {

//        $galleryLike = DB::table('gallery_like_web_apps')
//            ->where('gallery_id', $id)
//            ->where('user_id', $user)
//            ->first();
//        return response()->json($galleryLike);
    }
}
