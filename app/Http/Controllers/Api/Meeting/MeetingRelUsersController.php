<?php

namespace App\Http\Controllers\Api\Meeting;

use App\Http\Controllers\Controller;
use App\MeetingRelUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Meeting;
use App\Http\Controllers\Api\Meeting\ZoomMeetingController;
use App\BusinessMarket;
use App\BusinessMarketsRelUsers;
use App\Events\CreateMeetingEvent;
use App\Events\CancelMeetingEvent;

class MeetingRelUsersController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
        try{
            $rules = [
                'user_id'  => 'required',
                'meeting_id' => 'required',
            ];
    
            $this->validate($request, $rules);
            $toCreate = $request->all();
            $meeting = Meeting::find($toCreate['meeting_id']);
            broadcast(new CreateMeetingEvent($toCreate['user_id'],$meeting));
            $meet = MeetingRelUsers::create($request->all());
    
            return $this->successResponse(['data'=> $meet, 'message'=>'Rel Meeting Created'], 201);
        } catch(Exception $e){
                return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(MeetingRelUsers $meetingRelUsers)
    {
        
    }

    public function partnerCompanies($meet)
    {
        $partners = DB::table('meeting_rel_users as mu')
            ->select('mu.user_id', 'u.company_id', 'c.name as company')
            ->join('users as u', 'u.id', '=', 'mu.user_id')
            ->join('companies as c', 'c.id', '=', 'u.company_id')
            ->where('mu.meeting_id', $meet)
            ->where('mu.user_id', '!=', Auth()->id())
            ->get();

        return $this->showAll($partners, 200);
    }

    public function update(Request $request, $meetingRelUsers)
    {
        //acceptance = 0 (Waiting), =2 (Accepted), =3 (Cancel);
        //State 0 = Inactive or reject, 1 = Active, 2 = (Accepted)
        try{
            $meetingRelUsers = MeetingRelUsers::find($meetingRelUsers);
            if($request->acceptance == 2)
                {
                    $zoom = new ZoomMeetingController;
                    $meeting = Meeting::find($meetingRelUsers->meeting_id);
                    $user = auth()->user();
                    //return $user;
                    $BMRU = BusinessMarketsRelUsers::where('user_id',$user->id)->with('business')->first();
                    //return $BMRU;
                    $res = $zoom->create($meeting,$BMRU, $user);
                    //return $res;
                    $res = json_decode($res->body(), true);
                    $meeting->zoom_meeting_id = $res['id'];
                    $meeting->state = 2;
                    $meeting->save();
                }
            if($request->acceptance == 3)
                {
                    $meeting = Meeting::find($meetingRelUsers->meeting_id);
                    $meeting->state = 0;
                    $meeting->save();
                    broadcast(new CancelMeetingEvent($meeting->creator_id,$meeting));
                }

            $meetingRelUsers->update($request->all());
            return $this->successResponse(['data'=> $meetingRelUsers, 'message'=>'Rel meeting Updated'], 200);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(MeetingRelUsers $meetingRelUsers)
    {
        //
    }
}
