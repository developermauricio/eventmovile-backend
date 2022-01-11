<?php

namespace App\Http\Controllers\Api\Meeting;

use App\Http\Controllers\Controller;
use App\Meeting;
use App\MeetingRelUsers;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Http\Request;
use App\Events\FinishMeeting;
use App\BusinessMarket;
use App\Events\CancelMeetingEvent;
class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function schedule($user, $idBM, $optional = 0){

        $date = new \DateTime();
        $dateStandar = $date->format('Y-m-d');
        $dateTime = $date->format('H:i:s');
        if($optional == 0){
            $invitations = DB::table('meetings as m')
                ->select('m.start','u.name as username','c.name as company','u.pic','m.id as meeting_id','mru.id as invitation_id', 'm.end', 'm.title', 'm.state')
                ->where(function($q){
                    $q->where('m.state',2)
                    ->orWhere('m.state',3)
                    ->orWhere('m.state',4);
                })
                ->where('m.business_id',$idBM)
                ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'm.id')
                ->where('mru.user_id',$user)
                ->where('mru.acceptance','2')
                /*->whereDate('m.start','>=',$dateStandar)*/
                ->orderBy('m.start')
                ->join('users as u','u.id','=','m.creator_id')
                ->leftJoin('companies as c','c.id','=','u.company_id')
                ->orderBy('m.start')
                ->get();
            $invitations = $invitations->toArray();
            
            
            $meetings = DB::table('meetings as m')
                ->select('m.start','u.name as username','c.name as company','u.pic','m.id as meeting_id','mru.id as invitation_id', 'm.end', 'm.title', 'm.state')
                ->where('m.state',2)
                ->where(function($q){
                    $q->where('m.state',2)
                    ->orWhere('m.state',3)
                    ->orWhere('m.state',4);
                })
                ->where('m.business_id',$idBM)
                ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'm.id')
                ->where('m.creator_id',$user)
                //->whereDate('m.start','>=',$dateStandar)
                ->where('mru.acceptance','2')
                ->orderBy('m.start')
                ->join('users as u','u.id','=','mru.user_id')
                ->leftJoin('companies as c','c.id','=','u.company_id')
                ->orderBy('m.start')
                ->get();
            $meetings = $meetings->toArray();
        }   else {
            $invitations = DB::table('meetings as m')
                ->select('m.start','m.state','u.name as username','c.name as company','u.pic','m.id as meeting_id','mru.id as invitation_id', 'm.end', 'm.title')
                ->where(function($q){
                    $q->where('m.state',2)
                    ->orWhere('m.state',1);
                })
                ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'm.id')
                ->where('mru.user_id',$user)
                ->whereDate('m.start','>=',$dateStandar)
                ->orderBy('m.start')
                ->join('users as u','u.id','=','m.creator_id')
                ->leftJoin('companies as c','c.id','=','u.company_id')
                ->orderBy('m.start')
                ->get();
            $invitations = $invitations->toArray();
            
            
            $meetings = DB::table('meetings as m')
                ->select('m.start', 'm.state','u.name as username','c.name as company','u.pic','m.id as meeting_id','mru.id as invitation_id', 'm.end', 'm.title')
                ->where(function($q){
                    $q->where('m.state',2)
                    ->orWhere('m.state',1);
                })
                ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'm.id')
                ->where('m.creator_id',$user)
                ->whereDate('m.start','>=',$dateStandar)
                ->orderBy('m.start')
                ->join('users as u','u.id','=','mru.user_id')
                ->leftJoin('companies as c','c.id','=','u.company_id')
                ->orderBy('m.start')
                ->get();
            $meetings = $meetings->toArray();
        }

        $schedule = array_merge($invitations,$meetings);

        return $this->successResponse(['data'=> $schedule, 'message'=>"User´s Schedule "], 200);
    }

    public function invitations($id, $bm_id, $acceptance = 0)
    {
        try{
            //$user = User::where('users.id',$id)->first();
            $meetings =  DB::table('meetings as m')
                ->select('m.start','m.id','mru.id as idRel','mru.user_id','m.creator_id')
                ->where('m.business_id',$bm_id)
                ->where('mru.user_id',$id)
                ->where('mru.acceptance',$acceptance)
                ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'm.id')   
                ->get();
            return $this->successResponse(['data'=> $meetings, 'message'=>'List meetings'], 200);
        }  catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function nextMeeting(){
        $user = auth()->user();

        $date = new \DateTime();
        $dateStandar = $date->format('Y-m-d');
        $date->add(new \DateInterval('PT2M'));
        $dateTime = $date->format('H:i:s');

        $res = Meeting::where('meetings.state',2)
        ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'meetings.id')
        ->where(function($q)use($user){
            $q->where('meetings.creator_id',$user->id)
            ->orWhere('mru.user_id',$user->id);
        })
        ->whereDate('meetings.start','=',$dateStandar)
        ->whereTime('meetings.start', '>', $dateTime)
        ->orderBy('meetings.start')
        ->with('guestsUser.company','creator.company')
        ->first();
        if($res)
            $res->currentTime = $dateStandar." ".$dateTime;

        if($res)
            return $this->showOne($res);
        
        $res = Meeting::where('meetings.state',2)
        ->join('meeting_rel_users as mru', 'mru.meeting_id', '=', 'meetings.id')
        ->where(function($q)use($user){
            $q->where('meetings.creator_id',$user->id)
            ->orWhere('mru.user_id',$user->id);
        })
        ->whereDate('meetings.start','>',$dateStandar)
        ->orderBy('meetings.start')
        ->with('guestsUser','creator')
        ->first();
        if($res)
            $res->currentTime = $dateStandar." ".$dateTime;

        if($res)
            return $this->showOne($res);
        
        $res= ["message"=>"NoMeetings"];
        return $this->showOne($res);
    }


    public function store(Request $request)
    {
        try{
            $rules = [
                'title'  => 'required',
                'start' => 'required',
                'end' => 'required',
                'creator_id' => 'required',
            ];
           
            $this->validate($request, $rules);

            $bm = BusinessMarket::find($request->business_id);
            $startBM = strtotime($bm->start_date);
            $endBM = strtotime($bm->end_date);

            $toCompare = strtotime($request->start);
            
            if($toCompare>=$endBM || $toCompare<=$startBM){
                return $this->successResponse(['data'=>$bm->start_date, 'message'=>'FueraDeBM'], 200);
            } 

            $toSave = $request->all();
            $date = new \DateTime($toSave['start']);
            $date->add(new \DateInterval('PT1M'));
            $toSave['start'] = $date->format('Y-m-d H:i:s');

            $date = new \DateTime($toSave['end']);
            $date->sub(new \DateInterval('PT1M'));
            $toSave['end'] = $date->format('Y-m-d H:i:s');
    
            $meet = Meeting::create($toSave);
    
            return $this->successResponse(['data'=> $meet, 'message'=>'Meeting Created'], 201);
        } catch(Exception $e){
                return $this->errorResponse($e->getMessage(), 500);
        }
    
    }


    public function show(Meeting $meeting)
    {
        if($meeting){
            $meeting = Meeting::where('id',$meeting->id)->with('creator.company','guestsUser.company')->first();
            $date = new \DateTime();
            $dateStandar = $date->format('Y-m-d H:i:s');
            $meeting->currentTime=$dateStandar;
            return $this->showOne($meeting);}
        
        return $this->errorResponse('That meeting not exist', 404);
    }

    public function update(Request $request, Meeting $meeting)
    {
        //acceptance = 1 (Waiting), =2 (Accepted), =3 (Cancel);
        //State 0 = Reject , 1 = Active-Waiting, 2 = (Accepted), 3 = finished, 4 = finished before

        try{
            if(isset($request->state) && $request->state == 0){
                $meeting->update($request->all());
                $meetingRelUsers = MeetingRelUsers::where('meeting_id',$meeting->id)->first();
                $meetingRelUsers = MeetingRelUsers::find($meetingRelUsers->id);
                $meetingRelUsers->state = 0;
                $meetingRelUsers->acceptance = 3;
                $meetingRelUsers->save();

                $user = auth()->user();
                if($user->id == $meeting->creator_id)
                    broadcast(new CancelMeetingEvent($meetingRelUsers->user_id,$meeting));
                else
                    broadcast(new CancelMeetingEvent($meeting->creator_id,$meeting));

                return $this->successResponse(['data'=> $meetingRelUsers, 'message'=>'Meeting Updated'], 200);
            }

            if(isset($request->state) && $request->state == 4){
                $meeting->update($request->all());
                $meetingRelUsers = MeetingRelUsers::where('meeting_id',$meeting->id)->first();
                $user = auth()->user();
                if($user->id == $meeting->creator_id)
                    broadcast(new FinishMeeting($meetingRelUsers->user_id,$meeting));
                else
                    broadcast(new FinishMeeting($meeting->creator_id,$meeting));
            }

            $meeting->update($request->all());
            return $this->successResponse(['data'=> $meeting, 'message'=>'Meeting Updated'], 200);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        try{
            $meeting->delete();
            return $this->successResponse(['data'=> "", 'message'=>'Meeting Deleted'], 200); 
        }catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
