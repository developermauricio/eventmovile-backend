<?php

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Document;

class DocumentController extends Controller
{
    public function index(){

        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        if($role == "super admin"){
            $documents =Document::all();
        }
        if($role == "admin"){

            $documentsEvent = DB::table('documents as d')
                ->select('d.*', 'e.name as event')
                ->join('events as e', 'e.id', '=', 'd.model_id')
                ->where('e.company_id', $user->model_id)
                ->where('model_type', 'Event');

            $documents = DB::table('documents as d')
                ->select('d.*', 'bm.name as business_market')
                ->join('business_markets as bm', 'bm.id', '=', 'd.model_id')
                ->where('model_type', 'BusinessMarket')
                ->where('bm.company_id', $user->model_id)
                ->union($documentsEvent)
                ->get();

        }
        if($role == "guest"){
            $documentsEvent = DB::table('documents as d')
                ->select('d.*', 'e.name')
                ->join('events as e', 'e.id', '=', 'd.model_id')
                ->join('activities as a', 'a.event_id', '=', 'e.id')
                ->join('guest_activities as ga', 'ga.activity_id', '=', 'a.id')
                ->where('ga.guest_id', $user->model_id)
                ->where('ga.payed', '<>', false)
                ->where('model_type', 'Event');


            $documents = DB::table('documents as d')
                ->select('d.*', 'bm.name')
                ->join('business_markets as bm', 'bm.id', '=', 'd.model_id')
                ->join('guest_business_markets as gbm', 'gbm.business_market_id', '=', 'bm.id')
                ->where('gbm.guest_id', $user->model_id)
                ->where('model_type', 'BusinessMarket')
                ->union($documentsEvent)
                ->get();
        }

        return $this->showAll($documents,200);
    }
    public function show(Document $document){

        if($document->type == 'url'){
            return $this->showOne($document);
        }

        $public_path = public_path();
        
        if (Storage::exists($document->url)){
            return Storage::download($document->url, $document->name);
        }else{
            return $this->errorResponse('The document does not exist', 500);
        }
        
    }
    public function showModelDocuements($model, $modelId){
        
        $documents = Document::where('model_type', $model)
            ->where('model_id', $modelId)
            ->orderBy('id', 'desc')
            ->get();
        
        return $this->showAll($documents,200);
        
    }

    public function update(Request $request, Document $document){
        $rules = [
            'url'       => 'required',
            'type'      => 'required',
            'name'      => 'required',
        ];

        $this->validate($request, $rules);

        if(is_file($request->url)){
            $file = $request->url;
            $nameFile = $file->getClientOriginalName();
            $request->url = $nameFile;
        }


        if($request->type == 'local' && $document->url != $request->url){
            $explode = explode(".", $nameFile);
            $number = Document::count();            
            $nameFile = $explode[0]."_".$request->model_type."_".$request->model_id."_".$number.".".$explode[1];
            Storage::disk('local')->put($nameFile,  \File::get($file));
            $request->url = $nameFile;
        }
        $document->type = $request->type;
        $document->url = $request->url;
        $document->name = $request->name;

        
        if ($document->isClean()) {
            return $this->successResponse(['data' => $document, 'message' => 'At least one different value must be specified to update'],201);
        }
        $document->save();

        return $this->successResponse(['data' => $document, 'message' => 'Document Updated'],201);

        
    }

    public function store(Request $request){

        $rules = [
            'model_type'=> 'required',
            'model_id'  => 'required',
            'url'       => 'required',
            'type'      => 'required',
            'name'      => 'required',
        ];

        $this->validate($request, $rules);

        if($request->type == 'local'){
            $file = $request->url;
        
            $nameFile = $file->getClientOriginalName();

            $number = Document::count();

            $explode = explode(".", $nameFile);
            $nameFile = $explode[0]."_".$request->model_type."_".$request->model_id."_".$number.".".$explode[1];
            
            $path = Storage::disk('public')->put('/documents/'.$nameFile,  \File::get($file));

            // $path = Storage::disk('public')->put('documents', $nameFile , 'public');
            $urlFinal = '/storage/documents/'.$nameFile;
            $nameFile = $urlFinal;

        }else{
            $nameFile = $request->url;
        }

        $document = Document::create([
            'model_type'=> $request->model_type,
            'model_id'  => $request->model_id,
            'url'       => $nameFile,
            'type'      => $request->type,
            'name'      => $request->name, 
        ]);

        return $this->successResponse(['data'=> $document, 'message'=>'Document Created'], 201);
    }

    public function destroy(Document $document)
    {
        if($document->type == 'url'){
            $document->delete();   
            return $this->successResponse(['data' => $document, 'message' => 'Event Deleted'], 201);
        }else{
            Storage::delete($document->url);
            
            $document->delete();   
            return $this->successResponse(['data' => $document, 'message' => 'Event Deleted'], 201);
        }
    }
}
