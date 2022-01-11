<?php

namespace App\Http\Controllers\Api\BusinessMarket;

use App\Http\Controllers\Controller;
use App\BusinessMarketsRelUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Traits\sendEmail;
use App\Traits\formatRegistrationEmail;
use App\User;
use App\BusinessMarket;
class BusinessMarketsRelUsersController extends Controller
{
    use sendEmail, formatRegistrationEmail;
    
    public function store(Request $request)
    {
        //
        try{
            if(!isset($request->action)){
                $rules = [
                    'user_id'  => 'required',
                    'business_id' => 'required',
                ];
        
                $this->validate($request, $rules);
                $user = BusinessMarketsRelUsers::create($request->all());
                return $this->successResponse(['data'=> $user, 'message'=>'Registro exitoso'], 201);
            } else{
               $Ucreated = 0;
                    foreach($request->data as $data){
                        $business = BusinessMarketsRelUsers::where('business_id',$request->business_id)
                            ->where('user_id', $data['id'])->get();
                        if(count($business)== 0){
                            $bmCreate = new BusinessMarketsRelUsers;
                            $bmCreate->business_id = $request->business_id;
                            $bmCreate->user_id = $data['id'];
                            $bmCreate->relation = "participant";
                            $bmCreate->save();
                            $Ucreated++;

                            $user = User::find($data['id']);
                            $businessData = BusinessMarket::find($request->business_id);
                            $format = $this->formatRegistrationEmail($user, $businessData );
                            $this->sendEmail($user->email, $format['subject'], "'". $format['template'] . "'", true, "registered", "bm", $request->business_id);
                        }                            
                    } 
                    
                    $business = BusinessMarketsRelUsers::where('business_id',$request->business_id)->get();
                    $deleted = 0;
                    foreach($business as $b){
                        $exist = 0;
                        foreach($request->data as $d){
                            if($b->user_id == $d['id'])
                                $exist = 1;
                        }

                        if($exist == 0){
                            $toDelete = BusinessMarketsRelUsers::where('id',$b->id)->first();
                            //return $toDelete;
                            $toDelete->delete();
                            $deleted++;
                        }
                    }

                    return $this->successResponse(['data'=> Array("Created"=>$Ucreated,"Deleted"=>$deleted), 'message'=>'Registro exitoso'], 201);
            }
            
              
            } catch(Exception $e){
                return $this->errorResponse($e->getMessage(), 500);
        }

    }

    public function show(BusinessMarketsRelUsers $businessMarketsRelUsers)
    {
        //
    }


    public function edit(BusinessMarketsRelUsers $businessMarketsRelUsers)
    {
        //
    }

   
    public function update(Request $request, BusinessMarketsRelUsers $businessMarketsRelUsers)
    {
        try{
            $businessMarketsRelUsers->update($request->all());
            return $this->successResponse(['data'=> $businessMarketsRelUsers, 'message'=>'registration Updated'], 200);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(BusinessMarketsRelUsers $businessMarketsRelUsers)
    {
        try{
            $businessMarketsRelUsers->delete();
            return $this->successResponse(['data'=> "", 'message'=>'registration Deleted'], 200); 
        }catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    } 
}
