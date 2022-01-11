<?php

namespace App\Http\Controllers\Api\BusinessMarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\BusinessMarket;
use App\BusinessMarketsRelUsers;
use Illuminate\Support\Facades\Log;

class BusinessMarketController extends Controller
{
    public function index()
    {
        $business= [];

        if (!Auth::user()){
            $date = date('Y-M-d');
            $business =BusinessMarket::where('end_date','>',$date)->orderBy('id', 'desc')->get();
            return $this->showAll($business,200);
        }

        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();
        
        if($role == "super admin" || $role == "admin"){
            $business =BusinessMarket::orderBy('id', 'desc')->get();
        }
        
        if($role == "business market"){
            $business = BusinessMarket::select('business_markets.*')
                ->join('business_markets_rel_users as bmu', 'bmu.business_id', '=', 'business_markets.id')
                ->where('bmu.user_id',  $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return $this->showAll($business,200);
    }

    public function participants($business,$filter = 0){

        $carbon = new \Carbon\Carbon(); 
        $date = $carbon->format('Y-m-d');

        if($filter != 0)
            $participants = DB::table('business_markets as bm')
            ->select('u.name as username','c.name','c.pic','u.id','u.user_type','c.sort_description', DB::raw("(SELECT at.revoked FROM oauth_access_tokens as at WHERE at.user_id = u.id 
                AND at.created_at > '".$date." 00:00:00' order by at.created_at desc limit 1 ) as revoked"))
            ->where('bmru.business_id',$business)
            ->join('business_markets_rel_users as bmru','bmru.business_id','=','bm.id')
            ->join('users as u','u.id','=','bmru.user_id')
            ->leftJoin('companies as c','c.id','=','u.company_id')
            ->where('bmru.user_id','!=',$filter)->get();
        else
            $participants = DB::table('business_markets as bm')
            ->select('u.name as username','c.name','c.pic','u.id','u.user_type','c.sort_description',  DB::raw("(SELECT at.revoked FROM oauth_access_tokens as at WHERE at.user_id = u.id 
                AND at.created_at > '".$date." 00:00:00' order by at.created_at desc limit 1 ) as revoked"))
            ->where('bmru.business_id',$business)
            ->join('business_markets_rel_users as bmru','bmru.business_id','=','bm.id')
            ->join('users as u','u.id','=','bmru.user_id')
            ->leftJoin('companies as c','c.id','=','u.company_id')
            ->get();
       /* $participants = BusinessMarket::where('id',$business)->with(['participants'=>function($query)use($filter){
            $query->where('user_id','<>',$filter);
        }])->get();*/
        return $this->successResponse(['data'=> $participants, 'message'=>"Participants"], 200);
    }

    public function show( $businessMarket)
    {   
        $businessMarket = BusinessMarket::find($businessMarket);
        return $this->showOne($businessMarket);
    }

    public function saveFile($file, $name){
        try{
            $nameFile = $file->getClientOriginalName();
            $explode = explode(".", $nameFile);
            $fecha = date_create();
            $nameFile = $explode[0]."_BusinessMarket_".date_timestamp_get($fecha).".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file)); 
            return $nameFile;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
                
        $rules = [
            'name'                  => 'required|min:6',
            'sort_description'      => 'required|min:6',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date',
            'location_coordinates'  =>'required',
            'guests_limit'          => 'required',
            'type'                  =>'required',
            'mode'                  =>'required'
        ];

        $this->validate($request, $rules);

        $toSave = $request->all();

        if($request->pic){
            $nameFile = $this->saveFile($request->pic, $request->name);
            $toSave['pic'] = $nameFile;
        }

        if($request->background_banner){
            $nameFile = $this->saveFile($request->background_banner, $request->name);
            $toSave['background_banner'] = $nameFile;
        }

        if($request->logo){
            $nameFile = $this->saveFile($request->logo, $request->name);
            $toSave['logo'] = $nameFile;
        }

        $business = BusinessMarket::create($toSave);

        return $this->successResponse(['data'=> $business, 'message'=>'Business Created'], 201);
    }

    public function update(Request $request, $businessMarket)
    {
        $businessMarket = BusinessMarket::find($businessMarket);
        $toSave = $request->all();

        if(isset($request->pic) && $request->hasFile('pic')){
            $nameFile = $this->saveFile($request->pic, $request->name);
            $toSave['pic'] = $nameFile;
        }
        
        if(isset($request->background_banner) && $request->hasFile('background_banner')){
            $nameFile = $this->saveFile($request->background_banner, $request->name);
            $toSave['background_banner'] = $nameFile;
        }

        if(isset($request->logo) && $request->hasFile('logo')){
            $nameFile = $this->saveFile($request->logo, $request->name);
            $toSave['logo'] = $nameFile;
        }
        //return $nameFile;
        $businessMarket->update($toSave);
        
        if ($businessMarket->isClean()) {
            return $this->successResponse(['data' => $businessMarket, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $businessMarket->save();

        return $this->successResponse(['data' => $businessMarket, 'message' => 'Business Updated'],200);
    }

    public function destroy($business)
    {
        $businessMarket = BusinessMarket::find($business);
        $businessMarket->delete();   
        return $this->successResponse(['data' => '', 'message' => 'Business Deleted'], 200);
    }

    public function report($business){
        $users = DB::table('business_markets_rel_users as bmu')
        ->select('u.id','u.name', 'u.lastname','u.email','u.phone','u.position','u.pic',DB::raw('(select name from companies as c where c.id=u.company_id) as empresa'))
        ->join('users as u', 'u.id', 'bmu.user_id')
        ->where('bmu.business_id',$business)
        ->get();

        $bmFields = DB::table('bm_register_fields')
        ->select('*')
        ->where('business_id',$business)
        ->get();


        $users->map(function($item) use($business, $bmFields){
            $email = DB::table('emails as e')
            ->select('et.created_at')
            ->join('email-tracking as et', 'et.email_id', 'e.id')
            ->where('e.model_id',$business)
            ->where('e.email',$item->email)
            ->orderBy('et.id','DESC')
            ->first();

            $bmFields->map(function($item2) use($item){
                $value = DB::table('bm_register_fields_data')
                ->select('value')
                ->where('bmr_field_id',$item2->id)
                ->where ('user_id',$item->id)
                ->first();
                
                if($value)
                    $item->{$item2->name} = $value->value;
                else
                    $item->{$item2->name} = "";
            });

            if($email)
                $item->email_registro = "Visto";
            else
                $item->email_registro = "No";
            
            $lLogin = DB::table('oauth_access_tokens')
            ->select('created_at')
            ->where('user_id',$item->id)
            ->orderBy('id','DESC')
            ->first();

            if($lLogin)
                $item->ultimo_login = $lLogin->created_at;
            else
                $item->ultimo_login = "No registra";

            $meetings = DB::table('meetings as m')
            ->join('meeting_rel_users as mu','mu.meeting_id','m.id')
            ->where('m.business_id',$business)
            ->where('m.state',2)
            ->where(function($query) use($item){
                $query->where('mu.user_id',$item->id)
                ->orWhere('m.creator_id',$item->id);
            })
            ->count();
            
            $item->reuniones_aceptadas = $meetings ;

            $meetings = DB::table('meetings as m')
            ->join('meeting_rel_users as mu','mu.meeting_id','m.id')
            ->where('m.business_id',$business)
            
            ->where(function($query){
                $query->where('m.state',3)
                ->orWhere('m.state',4);
            })
            ->where(function($query) use($item){
                $query->where('mu.user_id',$item->id)
                ->orWhere('m.creator_id',$item->id);
            })
            ->count();

            $item->reuniones_completas = $meetings; 

            
        });

        return $this->showAll($users, 201);

    }
}
