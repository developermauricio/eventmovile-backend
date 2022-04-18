<?php

namespace App\Http\Controllers\Api\WebApp\Attendance;

use App\User;
use App\Attendance;
use App\DataRegister;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function getUserRegisterEvent( $eventID ) {
        $user_id = auth()->user()->id;

        $users = User::select('id', 'name', 'lastname', 'email', 'created_at', 'pic')
            ->whereHas('eventUsers', function ($q) use ($eventID) {
                return $q->where('event_id', $eventID);
            })->where('id', '<>', $user_id)
            ->get();
        
        return response()->json($users);
    }

    public function getDataUserForID( $user_id ) {
        $user = User::findOrFail($user_id, ['id', 'name', 'lastname', 'email']);
        return response()->json($user);
    }

    public function updateInfoUser( Request $request ) {
        //Log::debug('user request: ');
        //Log::debug($request);        
        $user = User::findOrFail( $request->user_id )->update([
            "name" => $request->name,
            "lastname" => $request->lastname,
        ]);
        //Log::debug('user update: ');
        //Log::debug($user);

        return response()->json($user);
    }    

    public function getAllAttendance( $event_id ) {
        $listAttendance = Attendance::where('event_id', $event_id)->get();
        return response()->json($listAttendance);
    }

    public function createRegisterAttendance(Request $request) {
        $currentUser = User::where( 'id', $request->user_id )->first();
        
        if ( !$currentUser ) {  
            return response()->json(['status' => 'fail', 'msg' => 'registro fallido el usuario no está registrado.']);         
        } 

        $userAttendance = Attendance::where('user_id', $request->user_id)->
            where('event_id', $request->event_id)->first();
        //Log::debug('userAttendance');
        //Log::debug($userAttendance);
        if ( $userAttendance ) {
            return response()->json(['status' => 'fail', 'msg' => 'registro fallido el usuario ya se encuentra registrado.']);         
        }

        DB::beginTransaction();
        try {  
            $newAttendance = new Attendance;
            $newAttendance->event_id = $request->event_id;
            $newAttendance->user_id = $request->user_id;
            $newAttendance->date_register = Carbon::now('America/Bogota');
            $newAttendance->register_qr = $request->register_qr === 0 ? '0' : '1';
            $newAttendance->save();
            
            DB::commit();
            
            return response()->json(['status' => 'ok', 'msg' => 'registro agredado correctamente.']); 
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());
            return response()->json(['status' => 'fail', 'msg' => 'registro fallido.']);
        }

       
    }

    public function getDataRegister( $user, $event ) {
        $data = DB::table('data_registers as dr')
            ->select('dr.id', 'dr.register_id', 'dr.value', 're.name', 're.type')
            ->join('register_events as re', 're.id', '=', 'dr.register_id')
            ->where('re.event_id', $event)
            ->where('dr.user_id', $user)
            ->get();
        
        return response()->json($data);
    }

    public function updateDataRegister(Request $request) {
        $dataRegister = DataRegister::updateOrCreate(
            [ 'id' => $request->data_register_id ],
            [ 
                'user_id'  => $request->user_id,
                'register_id'      => $request->register_id,
                'value'      => $request->value
            ]
        );

        return response()->json($dataRegister);
    }


}
