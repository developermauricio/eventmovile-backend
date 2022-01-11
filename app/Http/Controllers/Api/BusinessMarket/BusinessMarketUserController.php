<?php

namespace App\Http\Controllers\Api\BusinessMarket;

use App\User;
use App\BusinessMarketsRelUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
class BusinessMarketUserController extends Controller
{

    public function index()
    {
        return $this->successResponse(['data'=> "", 'message'=>'No method'], 200);
    }

    public function indexByBusinnesMarket($id)
    {
        //
        $users = BusinessMarketsRelUsers::where('business_id',$id)->with('users')->get();
        return $this->showAll($users, 200);
    }

    public function create()
    {
        //
    }

    public function saveFile($file){
        try{
            $nameFile = $file->getClientOriginalName();
            $explode = explode(".", $nameFile);
            $fecha = date_create();
            $nameFile = $explode[0]."_BMUser_".date_timestamp_get($fecha).".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file)); 
            return $nameFile;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function store(Request $request)
    {
        try{
        $rules = [
            'name'  => 'required|min:6',
            'email' => 'required',
            'phone' => 'required|min:6',
        ];

        $this->validate($request, $rules);

        $toSave = $request->all();

        if($request->pic){
            $nameFile = $this->saveFile($request->pic);
            $toSave['pic'] = $nameFile;
        }

        $user = User::create($toSave);

        return $this->successResponse(['data'=> $user, 'message'=>'User Created'], 201);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }

    }


    public function show($user,$bm = 0)
    {
        //return $user;
        if($bm==0)
            $user = User::where('id',$user)->with('company')->get();
        else $user = User::where('id',$user)->with(['company','bm' => function($query) use($bm){
            return $query->where('business_id',$bm);
        }])->get();
        
        if($user)
            return $this->showOne($user);

        return $this->errorResponse('That product not exist', 404);
    }

    public function update(Request $request, $user)
    {
        try{

            $user = User::find($user);
    
            $toSave = $request->all();
            

        if(isset($request->pic) && $request->hasFile('pic')){
            $nameFile = $this->saveFile($request->pic);
            $toSave['pic'] = $nameFile;
        }   else {
            unset($toSave['pic']);
        }
            $user->update($toSave);
            return $this->successResponse(['data'=> $user, 'message'=>'User Updated'], 200);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(User $user)
    {
        //
        try{
            $user->delete();
            return $this->successResponse(['data'=> "", 'message'=>'User Deleted'], 200); 
        }catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
