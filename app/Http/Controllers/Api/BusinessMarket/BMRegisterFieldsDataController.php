<?php

namespace App\Http\Controllers\Api\BusinessMarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BMRegisterFieldData;

class BMRegisterFieldsDataController extends Controller
{
    public function index() {

        $bMRegisterFieldData = BMRegisterFieldData::all();

        return $this->showAll($bMRegisterFieldData,200);
    }

    public function show(BMRegisterFieldData $bMRegisterFieldData)
    {
    
        return $this->showOne($bMRegisterFieldData);
    }

    public function showFieldsEvent($business_id)
    {
        $bMRegisterFieldData = BMRegisterFieldData::where('business_id', $business_id)->orderBy('id', 'desc')->get();

        return $this->showAll($bMRegisterFieldData);
    }

    public function store(Request $request){

        $rules = [
            'user_id'   => 'required|exists:users,id',
            'bmr_field_id'   => 'required|exists:bm_register_fields,id',
            'value'       => 'required' 
        ];

        $this->validate($request, $rules);

        $tosave = $request->all();
        $bMRegisterFieldData = BMRegisterFieldData::create($tosave);

        return $this->successResponse(['data'=> $bMRegisterFieldData, 'message'=>'Data Created'], 201);
    }

    public function update(Request $request, BMRegisterFieldData $bMRegisterFieldData){
        
        $rules = [
            'business_id'   => 'required|exists:business_markets,id',
            'name'       => 'required',
            'type'       => 'required' 
        ];


        $this->validate($request, $rules);

                
        $bMRegisterFieldData->fill($request->all());
        
        if ($bMRegisterFieldData->isClean()) {
            return $this->successResponse(['data' => $bMRegisterFieldData, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $bMRegisterFieldData->save();

        return $this->successResponse(['data' => $bMRegisterFieldData, 'message' => 'Field Updated'],201);
    }

    public function destroy(BMRegisterFieldData $bMRegisterFieldData)
    {
        $bMRegisterFieldData->delete();   
        return $this->successResponse(['data' => $bMRegisterFieldData, 'message' => 'Field Deleted'], 201);
    }
}
