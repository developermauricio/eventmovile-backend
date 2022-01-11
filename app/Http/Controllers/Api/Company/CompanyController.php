<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use Illuminate\Support\Facades\Storage;
class CompanyController extends Controller
{
    public function index()
    {   
        $companies = Company::all();

        return $this->showAll($companies);

    }

    public function create()
    {
                
    }
    

    public function store(Request $request)
    {
        
        $rules = [
            'name'  => 'required|min:3',
            'sort_description' => 'required',
            'email' => 'required',
            'phone'=>'required',
            'address'=>'required',
        ];

        $this->validate($request, $rules);
           
        $toSave = $request->all();
        if(!isset($request->city_id)){
            $toSave['city_id'] = 1;
        }
        if(!isset($request->location_coordinates)){
            $toSave['location_coordinates'] = '-';
        } 

        if($request->pic){
            $nameFile = $this->saveFile($request->pic);
            $toSave['pic'] = $nameFile;
        }
        $company = Company::create($toSave);

        return $this->successResponse(['data'=> $company, 'message'=>'Company Created'], 201);
    }

    public function saveFile($file){
        try{
            $nameFile = $file->getClientOriginalName();
            $explode = explode(".", $nameFile);
            $fecha = date_create();
            $nameFile = $explode[0]."_Company_".date_timestamp_get($fecha).".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file)); 
            return $nameFile;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function show(Company $company)
    {
        if($company){
            $company = Company::where('id',$company->id)->with('producs','business_user')->get();
            return $this->showOne($company);}
        
        return $this->errorResponse('That product not exist', 404);
    }

    public function update(Request $request, Company $company)
    {
        try{
            $toSave = $request->all();

            if(isset($request->pic) && $request->hasFile('pic')){
                $nameFile = $this->saveFile($request->pic);
                $toSave['pic'] = $nameFile;
            }
            else {
                unset($toSave['pic']);
            }

            $company->update($toSave);
            return $this->successResponse(['data'=> $company, 'message'=>'Product Updated'], 200);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(Company $company)
    {
        try{
            $company->delete();
            return $this->successResponse(['data'=> "", 'message'=>'Company Deleted'], 200); 
        }catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
