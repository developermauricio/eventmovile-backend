<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use App\Document;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    public function index()
    {
        return $this->successResponse(['data'=> "", 'message'=>'No method'], 200);
    }
    public function indexById($id)
    {
        //
        $product = Product::where('model_id',$id)->with('documents')->get();
        return $this->showAll($product, 200);
    }

    public function saveFile($file){
        try{
            $nameFile = $file->getClientOriginalName();
            $explode = explode(".", $nameFile);
            $fecha = date_create();
            $nameFile = $explode[0]."_Product_".date_timestamp_get($fecha).".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file)); 
            return $nameFile;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function store(Request $request)
    {
        //
        $rules = [
            'name'  => 'required|min:6',
            'model_id' => 'required'
        ];

        $this->validate($request, $rules);

        $toSave = $request->all();

        if($request->pic){
            $nameFile = $this->saveFile($request->pic);
            $toSave['pic'] = $nameFile;
        }

        $product = Product::create($toSave);

        return $this->successResponse(['data'=> $product, 'message'=>'Product Created'], 201);
    }

    public function show(Product $product)
    {
        //
        if($product){
            $documents = Document::where('model_id',$product->id)->get();
            $product->documents = $documents;
            return $this->showOne($product);}
        
        return $this->errorResponse('That product not exist', 404);
        
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        //
        try{
            $toSave = $request->all();
            //return $request->pic;
            if(isset($request->pic) && $request->hasFile('pic')){
                $nameFile = $this->saveFile($request->pic);
                $toSave['pic'] = $nameFile;
            } else {
                unset($toSave['pic']);
            }

            $product->update($toSave);
            return $this->successResponse(['data'=> $product, 'message'=>'Product Updated'], 200);
        } catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(Product $product)
    {
        //
        try{
            $product->delete();
            return $this->successResponse(['data'=> "", 'message'=>'Product Deleted'], 200); 
        }catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
