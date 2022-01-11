<?php

namespace App\Http\Controllers\Api\Hall;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Hall;
use App\Activity;
use Illuminate\Support\Facades\Log;
use App\Traits\HelperApp;

class HallController extends Controller
{
    use HelperApp;
    public function index()
    {

        $role = auth()->user()->getRoleNames()->first();
        if ($role == "super admin") {
            $hall = Hall::orderBy('id', 'desc')->get();
        } else {
            $hall = Hall::where('creator_id', Auth()->id())->orderBy('id', 'desc')->get();
        }

        return $this->showAll($hall, 200);
    }

    public function show(Hall $hall)
    {
        return $this->showOne($hall);
    }

    /**
     * retorna las salas por evento
     * @param
     * $event = id del evento    
     * @return
     * showAll= las salas paginadas
     */
    public function showHallsEvent($event)
    {
        $hall = Hall::where('event_id', $event)->get();

        return $this->showAll($hall, 200);
    }
    /**
     * $inLanding = true para traer solo id, title   
     */
    public function showHallsInLineTime($event, $user_id)
    {
        //salas
        $hall = Hall::select('id', 'name', 'hall_type_id', 'domain_external', 'activities', 'event_id')
            ->with(['activities1' => function ($activities) {
                $activities->orderBy('start_date', 'asc');
            }])
            ->where('event_id', $event)
            ->get();
        foreach ($hall as $key => $value) {
            $activitiesForHall = Hall::select('activities')->where('id', $value->id)->get();
            $arrayofActivities = json_decode($activitiesForHall[0]->activities);
            $arrayForDelete = [];

            if (is_array($arrayofActivities)) {
                foreach ($value->activities1 as $key1 => $value1) {
                    $verifyYourInvitation = $this->verifyYourInvitation($event, $user_id, $value1->id);
                    $value1->authorization=false;
                    if ($verifyYourInvitation['status'] == 'TRUE') {
                        $value1->authorization=true;

                    } else {
                        $activityIsFree = $this->verifyActivityIfFree($event, $value1->id);
                        if ($activityIsFree['status'] == 'TRUE') {
                            $value1->authorization=true;
                        }
                    }
                    if (in_array($value1->id, $arrayofActivities) == false) {
                        array_push($arrayForDelete, $key1);
                    }
                }
            }
            foreach ($arrayForDelete as $key2 => $value2) {
                unset($value->activities1[$value2]);
            }
        }

        return $this->successResponse(['data' => $hall, 'message' => 'time line halls'], 200);
        //halls in line time
        // $hallLineTime = [];
        // foreach($hall as $k =>$h){            
        //     $hallLineTime[$k]['hall'] = array(
        //         'id'=>$h->id,
        //         'name'=>$h->name,
        //         'hall_type_id'=>$h->hall_type_id,
        //         'domain_external'=>$h->domain_external
        //     );
        //     //obtener ids actividades
        //     $activities_ids = $h->activities;            
        //     $toArrIds = substr($activities_ids,1,-1);
        //     //cuando la sala no tiene actividades
        //     if($toArrIds!=""){
        //         $arrIds = explode(',',$toArrIds);                           
        //         for($i = 0; $i<count($arrIds); $i++){                           
        //             //$hallLineTime[$k]['hall']['activities'] = (array) Activity::select(
        //             $hallLineTime[$k]['hall']['activities'][$i] = Activity::select(
        //                 'id',
        //                 'name',
        //                 'start_date',
        //                 'end_date',
        //                 'duration_minutes',
        //                 'sort_description',                        
        //             )->where('id',$arrIds[$i])
        //             ->get();
        //             //verificamos permisos
        //             foreach($hallLineTime[$k]['hall']['activities'][$i] as $key => $val){
        //                 $val['authorization'] = false;                    
        //             }
        //             //$hallLineTime[$k]['hall']['activities']->authorization = false;                    
        //             $verifyYourInvitation = $this->verifyYourInvitation($event, $user_id, $arrIds[$i]);
        //             if ($verifyYourInvitation['status'] == 'TRUE') {
        //                 foreach($hallLineTime[$k]['hall']['activities'][$i] as $key => $val){
        //                     $val['authorization'] = true;                    
        //                 }
        //                 //$hallLineTime[$k]['hall']['activities']->authorization = true;
        //             } else {
        //                 $activityIsFree = $this->verifyActivityIfFree($event, $arrIds[$i]);
        //                 if ($activityIsFree['status'] == 'TRUE') {
        //                     foreach($hallLineTime[$k]['hall']['activities'][$i] as $key => $val){
        //                         $val['authorization'] = true;                    
        //                     }
        //                 }
        //             }                    
        //             //este code es para saber si la actividad esta en vivo ahora, la logica se paso al front *SI PARA ENERO/2022 no se ha pedido cambiarlo borrar*
        //             /*$fecha_actual = strtotime(date("Y-m-d h:i:s",time()));                                        
        //             foreach($hallLineTime[$k]['hall']['activities'] as $fecha_ini){
        //                 $fecha_inicio_act = strtotime(date($fecha_ini->start_date));
        //             }
        //             foreach($hallLineTime[$k]['hall']['activities'] as $fecha_fin){
        //                 $fecha_fin_act = strtotime(date($fecha_fin->end_date));
        //             }              
        //             //validación de rango de tiempo      
        //             if($fecha_actual > $fecha_inicio_act && $fecha_fin_act > $fecha_actual){
        //                 //ponemos que esta en vivo
        //                 foreach($hallLineTime[$k]['hall']['activities'] as $key => $val){
        //                     $val['now'] = true;                    
        //                 }
        //             }else{
        //                 foreach($hallLineTime[$k]['hall']['activities'] as $key => $val){
        //                     $val['now'] = false;                    
        //                 }
        //             }*/
        //         }                
        //     }else{
        //         $hallLineTime[$k]['hall']['activities'] = [];
        //     }                        
        // }                
        // //actividades
        // //var_dump($hall);
        // //return $this->showAll($hall, 200);        
        // //return json_encode($hallLineTime);
        // //$hallLineTime = (Object) $hallLineTime;
        // //return $this->showOne($hallLineTime,200);
        // return $this->successResponse(['data' => $hallLineTime, 'message' => 'time line halls'], 200);
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
            return ' Error al subir el archivo ' . $file;
        }
    }

    public function updateTres(Request $request)
    {
        $rules = [
            'name'       => 'required',
            'description' => 'required',
            'activities'  => 'required',
            'event_id'    => 'required|exists:events,id',
            'pic'         => 'required',
            'pic_banner'  => 'required',
            'hall_type_id'  => 'required',
            'location' => 'required'
        ];
        $this->validate($request, $rules);

        $pic = $this->saveFile($request->pic, 'background', 'hall');
        return response()->json([
            'la imagen https://eventmovil.sfo3.digitaloceanspaces.com/' . $pic
        ], 422);
    }


    public function store(Request $request)
    {
        $picBaner = "";
        $activities = "[]";
        $domain_external = null;
        if (isset($request->hall_type_id)) {
            if ($request->hall_type_id == "1") {
                $rules = [
                    'name'       => 'required',
                    'description' => 'required',
                    'activities'  => 'required',
                    'event_id'    => 'required|exists:events,id',
                    'pic'         => 'required',
                    'pic_banner'  => 'required',
                    'hall_type_id'  => 'required',
                    'location' => 'required'
                ];
                if (isset($request->activities)) {
                    $activities =  $request->activities;
                }
            } else {
                if ($request->hall_type_id == "2") {
                    $rules = [
                        'name'       => 'required',
                        'description' => 'required',
                        'event_id'    => 'required|exists:events,id',
                        'pic'         => 'required',
                        'hall_type_id'  => 'required',
                        'domain_external' => 'required',
                        'location' => 'required'
                    ];
                    if (isset($request->domain_external)) {
                        $domain_external = $request->domain_external;
                    }
                } else {
                    return response()->json([
                        'La opción del tipo de sala no es valida'
                    ], 422);
                }
            }
        } else {
            return response()->json([
                'Es necesario el tipo de sala'
            ], 422);
        }
        $this->validate($request, $rules);

        $pic = $this->saveFile($request->pic, 'background', 'hall');

        if ($request->hall_type_id == "1") {
            if (isset($request->pic_banner)) {
                $picBaner = $this->saveFile($request->pic_banner, 'banner', 'hall');
            }
        }

        $hall = Hall::create([
            'name'        => $request->name,
            'description' => $request->description,
            'activities'  => $activities,
            'creator_id'  => Auth()->id(),
            'event_id'    => $request->event_id,
            'pic'         => $pic,
            'pic_banner'  => $picBaner,
            'hall_type_id'  => $request->hall_type_id,
            'domain_external'  => $domain_external,
            'location' => $request->location
        ]);

        return $this->successResponse(['data' => $hall, 'message' => 'Hall Created'], 201);
    }

    public function update(Request $request, Hall $hall)
    {
        $picBaner = "";
        $activities = "[]";
        $domain_external = null;
        $rules = [];
        try {
            if (isset($request->hall_type_id)) {
                if ($request->hall_type_id == "1") {
                    $rules = [
                        'name'       => 'required',
                        'description' => 'required',
                        'activities'  => 'required',
                        'event_id'    => 'required|exists:events,id',
                        'pic'         => 'required',
                        'pic_banner'  => 'required',
                        'hall_type_id'  => 'required',
                        'location' => 'required'
                    ];

                    if (isset($request->activities)) {
                        $activities =  $request->activities;
                        $arrayactivities = json_decode($activities);
                        if (sizeof($arrayactivities) <= 0) {
                            return response()->json([
                                'Es necesario las actividades'
                            ], 422);
                        }
                    } else {
                        return response()->json([
                            'Es necesario las actividades'
                        ], 422);
                    }
                } else {
                    if ($request->hall_type_id == "2") {
                        $rules = [
                            'name'       => 'required',
                            'description' => 'required',
                            'event_id'    => 'required|exists:events,id',
                            'pic'         => 'required',
                            'hall_type_id'  => 'required',
                            'domain_external' => 'required',
                            'location' => 'required'
                        ];
                        if (isset($request->domain_external)) {
                            $domain_external = $request->domain_external;
                        }
                    } else {
                        return response()->json([
                            'La opción del tipo de sala no es valida'
                        ], 422);
                    }
                }
            } else {
                return response()->json([
                    'Es necesario el tipo de sala'
                ], 422);
            }
            $this->validate($request, $rules);

            $imageNameBanner = "";
            $imageName = "";
            //image bg
            /*  if (is_file($request->pic)) {
                $file = $request->pic;
                $nameFile = $file->getClientOriginalName();
                $imageName = $nameFile;
            } */

            if ($hall->pic != $request->pic) {
                $imageName = $this->saveFile($request->pic, 'background', 'hall');
            } else {
                $imageName = $hall->pic;
            }

            if ($request->hall_type_id == "1") {
                if (isset($request->pic_banner)) {
                    //image banner
                    /* if (is_file($request->pic_banner)) {
                        $file = $request->pic_banner;
                        $nameFileBanner = $file->getClientOriginalName();
                        $imageNameBanner = $nameFileBanner;
                    } */

                    if ($hall->pic_banner != $request->pic_banner) {
                        $imageNameBanner = $this->saveFile($request->pic_banner, 'banner', 'hall');
                    } else {
                        $imageNameBanner = $hall->pic_banner;
                    }
                }
            }

            $hall->name = $request->name;
            $hall->description = $request->description;
            $hall->activities = $activities;
            $hall->pic = $imageName;
            $hall->pic_banner = $imageNameBanner;
            $hall->event_id = $request->event_id;
            $hall->domain_external = $domain_external;
            $hall->hall_type_id = $request->hall_type_id;
            $hall->location = $request->location;
            $hall->save();

            return $this->successResponse(['data' => $hall, 'message' => 'Hall Updated'], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'Hubo un error en la actualización'
            ], 422);
        }
    }

    public function destroy(Hall $hall)
    {
        $hall->delete();
        return $this->successResponse(['data' => $hall, 'message' => 'Hall Deleted'], 201);
    }
}
