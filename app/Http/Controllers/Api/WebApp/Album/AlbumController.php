<?php

namespace App\Http\Controllers\Api\WebApp\Album;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Album;
use App\Photo;
use App\Interaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
     /**
     *obtiene el listado de data para photos publicas y privadas
     *
     *@param int  event_id, 
     * int user_id = si se quiere las de un usuario solamente
     */
    public function getPhotos($event_id, $user_id)
    {
        if($user_id!=0){
            try {
                $msg='photos by user';
                $photos = Album::from('album as al')
                    ->select('ph.id','ph.path_photo','ph.description','ph.upload_at','ph.id_album')
                    ->join('photo as ph','al.id','ph.id_album')                    
                    ->where('al.id_event','=',$event_id)
                    ->where('ph.id_user','=',$user_id)
                    ->where('ph.privacidad','=','private')
                    ->get();                                         
            } catch (\Throwable $th) {
                return $this->errorResponse('Error get photo', 500);  
            }            
        }else{
            try {
                $msg='photos by event';
                $photos = Album::from('album as al')
                    ->select('ph.id','ph.path_photo','ph.description','ph.upload_at','ph.id_album')
                    ->join('photo as ph','al.id','ph.id_album')                    
                    ->where('al.id_event','=',$event_id)
                    ->where('ph.privacidad','=','public')
                    ->get();                                
            } catch (\Throwable $th) {
                return $this->errorResponse('Error get photo', 500);              
            }
        }
        //get interaction
        foreach($photos as $key => $val){
            //SELECT id_user, description FROM interaction i where id_photo = 2 and description ='happy-face'
            $happyFace = Interaction::select('id_user','description')
            ->where('id_photo','=',$val->id)
            ->where('description','=','happy-face')
            ->get();
            $loveFace = Interaction::select('id_user','description')
            ->where('id_photo','=',$val->id)
            ->where('description','=','love-face')
            ->get();
            $val->happyFace = $happyFace; 
            $val->loveFace = $loveFace;
        }
        return $this->successResponse(['data'=> $photos, 'message'=>$msg], 201);
    }

    /**
     * inserta los datos de una foto
     *
     * @param
     * POST= id_user
     * @return \Illuminate\Http\Response
     */
    public function createPhoto(Request $request)
    {
        /* try { */
            $path_photo = $this->saveFile($request->path_photo);
            $dataToCreate = [
                'id_user' => $request->id_user,
                'id_album'=> $request->id_album,
                'description'=> $request->description,
                'path_photo' => $path_photo,
                'privacidad' => $request->privacidad,
                'upload_at' => date('Y-m-d H:i:s'),
                'status'=>1,
            ];
            $createPhoto = Photo::create($dataToCreate);
            if($createPhoto){
                return $this->successResponse(['data'=> $createPhoto, 'message'=>'photo Created'], 201);
            }            
        /* } catch (\Throwable $th) {
            return $this->errorResponse('Error insert photo', 500);  
        }       */  
    }

    /**
     * obtiene el id del album para el evento
     * @param int id evento
     */
    public function getIdAlbum($id){
        Log::info('getIdAlbum');
        Log::info($id);
        $idAlbum = Album::select('id')
            ->where('id_event','=',$id)
            ->get();            
        if(count($idAlbum)>0){
            return $idAlbum[0]->id;
        }else {
            Log::info($idAlbum);
            return $this->errorResponse('Error en la configuración de web app/album no creado', 500);      
        }
    }
    /**
     * carga un archivo a digitalocean cloud
     * @param FILE $file archivo
     * @return path
     */
    private function saveFile($file)
    {
        try {
            $path = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');
            return $path;
        } catch (Exception $e) {
            return $this->errorResponse('Error upload photo', 500);  
        }

    }

    /**
     * realiza un registro para asociar una interacción a una foto
     * @param int id_photo, int id_user (usuario que interactua)
     * la interacción por el momento esta solo una carita feliz, pero se podría 
     * crear más
     * @return 
     * true or false  
     */
    public function interactionPhoto(Request $request){
        $idPhoto = $request->id_photo;
        $idUser = $request->id_user;        
        $interaction = $request->interaction;
        $insert = Interaction::create(
            [
                'id_user'=>$idUser,
                'id_photo'=>$idPhoto,
                'description'=>$interaction,
                'register_at'=>date('Y-m-d H:i:s')
            ]
        );
        if($insert){
            return true;
        }else{
            return false;
        }
    }

    /**
     * borra un registro de datos acerca de una foto     
     * @param  int  $id     
     */
    public function deletePhoto(Request $request)
    {        
        $id = $request->id;        
        $res=Photo::where('id',$id)->delete();        
        if($res){
            return $this->successResponse(['data'=> [], 'message'=>'photo delete'], 201);
        }else{
            return $this->errorResponse('Error delete photo', 500);  
        }
    }
}
