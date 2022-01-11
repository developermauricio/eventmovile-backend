<?php

namespace App\Http\Controllers\Api\BusinessMarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BMRegisterField;

class BMRegisterFieldsController extends Controller
{
    public function index() {

        $bMRegisterField = BMRegisterField::all();

        return $this->showAll($bMRegisterField,200);
    }

    public function show(BMRegisterField $bMRegisterField)
    {
    
        return $this->showOne($bMRegisterField);
    }

    public function showFieldsBusiness($business_id)
    {
        $bMRegisterField = BMRegisterField::where('business_id', $business_id)->orderBy('id', 'desc')->get();

        return $this->showAll($bMRegisterField);
    }

    public function store(Request $request){

        $rules = [
            'business_id'   => 'required|exists:business_markets,id',
            'name'       => 'required',
            'type'       => 'required' 
        ];

        $this->validate($request, $rules);
        $toSave = $request->all();
        $bMRegisterField = BMRegisterField::create($toSave);

        return $this->successResponse(['data'=> $bMRegisterField, 'message'=>'Created'], 201);
    }

    public function update(Request $request, $id){
        $bMRegisterField = BMRegisterField::find($id);
        $rules = [
            'business_id'   => 'required|exists:business_markets,id',
            'name'       => 'required',
            'type'       => 'required' 
        ];


        $this->validate($request, $rules);

                
        $bMRegisterField->fill($request->all());
        
        if ($bMRegisterField->isClean()) {
            return $this->successResponse(['data' => $bMRegisterField, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $bMRegisterField->save();

        return $this->successResponse(['data' => $bMRegisterField, 'message' => 'Field Updated'],200);
    }

    public function destroy($id)
    {
        $bMRegisterField = BMRegisterField::find($id);
        $bMRegisterField->delete();   
        return $this->successResponse(['data' => $bMRegisterField, 'message' => 'Field Deleted'], 200);
    }
}
