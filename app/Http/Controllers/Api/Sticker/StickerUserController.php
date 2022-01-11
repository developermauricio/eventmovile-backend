<?php

namespace App\Http\Controllers\Api\Sticker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Sticker;
use App\EventUser;
use App\User;
use App\StickerUser;
use App\Traits\imagesTrait;
use Exception;

class StickerUserController extends Controller
{
    use imagesTrait;

    public function listStickerUsers($event){
        $stickerUsers = StickerUser::select('sticker_users.id', 'u.id as user_id', 'u.name as user_name', 'u.email', 'u.uid as no_identification',
            'sticker_users.file', 'e.name as event_name', 'e.id as event_id', 's.id as sticker_id',
            DB::raw('(CASE WHEN sticker_users.printed = 1 THEN "Si" WHEN sticker_users.printed = 0 THEN "No" END) as printed'))
            ->join('users as u', 'sticker_users.user_id', '=', 'u.id')
            ->join('events as e', 'e.id', '=', 'sticker_users.event_id')
            ->leftJoin('stickers as s', 's.event_id', '=', 'sticker_users.event_id')
            ->where('sticker_users.event_id', $event)
            ->get();

        return $this->showAll($stickerUsers);
        
    }

    public function printStatus($stickerUserId){
        $stickerUser = StickerUser::find($stickerUserId);
        $stickerUser->printed = true;
        $stickerUser->save();
        return $this->successResponse(['data'=> $stickerUser, 'message'=>'Sticker was printed'], 201);
    }


    public function userWithoutSticker($event){
        $data = StickerUser::select('eu.user_id', 'ei.event_id', 'eu.id as event_user_id', 'u.name as user_name', 'e.name as event_name',
            DB::RAW("(SELECT ui.id FROM url_invitations as ui WHERE ui.invitation_id = ei.id AND ui.user_id = eu.user_id) as urlinv"))
            ->rightJoin('event_users as eu', 'eu.user_id', '=', 'sticker_users.user_id')
            ->join('event_invitations as ei', 'ei.event_id', '=', 'eu.event_id')
            ->join('events as e', 'e.id', '=', 'eu.event_id')
            ->join('users as u', 'u.id', '=', 'eu.user_id')
            ->whereNull('sticker_users.user_id')
            ->where('eu.event_id', $event)
            ->get();
        
        $alldata = $data->filter(function($d){
            if($d->urlinv != null) {
                return $d;
            }
        });
        
        return $this->showAll($alldata);
            
    }

    public function index(){
        
    }
    public function showForEmail($email, $event){
        $user = User::where('email', $email)->orWhere('uid', $email)->first();
        if(!$user){
            return $this->errorResponse("El usuario no se encuentra en el registro de usuarios", 500);
        }else{
            $eventUser = EventUser::where('user_id', $user->id)->where('event_id', $event)->first();
            if(!$eventUser){
                return $this->errorResponse("El usuario no se encuentra registrado en el evento", 500);
            }
            $stickerUser = DB::table('sticker_users as su')
                ->select('su.id as sticker_user_id', 'u.name', 'u.email', 'su.file', 'su.event_id', 'u.id as user_id', 'su.printed')
                ->join('users as u', 'u.id', '=', 'su.user_id')
                ->join('stickers as s', 's.event_id', '=', 'su.event_id')
                ->where('u.email', $email)
                ->where('su.event_id', $event)
                ->orWhere('u.uid', $email)
                ->where('su.event_id', $event)
                ->first();
                
            if($stickerUser){
                return $this->showOne($stickerUser, 201);
            }
            $sticker = Sticker::where('event_id', $event)->first(); 
            return $this->errorResponse(['data'=>['sticker_id'=>$sticker->id, 'user_id'=>$user->id], 'message'=>'No existe un sticker para este usuario'], 500);
        }
           
        
        
    }
    public function showStikerUser($user, $event){

        $stickerUser = StickerUser::select('sticker_users.*', 's.id as sticker_id')->where('sticker_users.event_id', $event)
            ->join('stickers as s', 's.event_id', '=', 'sticker_users.event_id')
            ->where('sticker_users.user_id', $user)->first();
        if($stickerUser){
            return $this->showOne($stickerUser, 201);
        }

        $sticker = Sticker::where('event_id', $event)->first(); 
        if(!$sticker){
            return $this->errorResponse('No existe una plantilla sticker para este evento', 500);
        }
        return $this->errorResponse(['data'=>['sticker_id'=>$sticker->id, 'user_id'=>$user], 'message'=>'No existe un sticker para este usuario'], 500);
    }

    public function store(Request $request){
        $rules = [
            'event_id'   => 'required|exists:events,id',
            'user_id'   => 'required|exists:users,id',
        ];

        $this->validate($request, $rules);

        $nameFile  = "sticker_user".$request->user_id."_event".$request->event_id;

        $validate = StickerUser::where('event_id', $request->event_id)
            ->where('user_id', $request->user_id)->first();


        if(!$validate){
            $file = $this->convertSaveImageB64($request->file, $nameFile);

            $stickerUser = StickerUser::create([
                'event_id' => $request->event_id,
                'user_id'  => $request->user_id,
                'file'     => $file
            ]);
        }else{
            Storage::delete($validate->file);

            $file = $this->convertSaveImageB64($request->file, $nameFile);
            $validate->file = $file;
            $validate->save();

            return $this->successResponse(['data'=> $validate, 'message'=>'Sticker user updated'], 201);
        }
        

        return $this->successResponse(['data'=> $stickerUser, 'message'=>'Sticker user created'], 201);
    }

}
