<?php

namespace App\Http\Controllers\Api\Poll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\TypeQuestion;

class TypeQuestionController extends Controller
{
    public function index() {

        $typeQuestion = TypeQuestion::all();

        return $this->showAll($typeQuestion,200);
    }

    public function show(TypeQuestion $typeQuestion)
    {
    
        return $this->showOne($typeQuestion);
    }

    public function store(Request $request){

        $rules = [
            'name'              => 'required|min:6',
            'sort_description'  => 'required|min:10',
            'pic'               => 'required|file' 
        ];

        $this->validate($request, $rules);

        $typeQuestion = TypeQuestion::create([
            'name'              => $request->name,
            'sort_description'  => $request->sort_description,
            'pic'               => $nameFile,
        ]);

        return $this->successResponse(['data'=> $typeQuestion, 'message'=>'Speaker Created'], 201);
    }

    public function update(Request $request, TypeQuestion $typeQuestion){
        
        $rules = [
            'name'              => 'required|min:6',
            'sort_description'  => 'required|min:10',
            'pic'               => 'required|file' 
        ];

        $this->validate($request, $rules);

                
        $typeQuestion->fill($request->all());
        
        if ($typeQuestion->isClean()) {
            return $this->successResponse(['data' => $typeQuestion, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $typeQuestion->save();

        return $this->successResponse(['data' => $typeQuestion, 'message' => 'Poll Question Updated'],201);
    }

    public function destroy(TypeQuestion $typeQuestion)
    {
        $typeQuestion->delete();   
        return $this->successResponse(['data' => $typeQuestion, 'message' => 'Speaker Deleted'], 201);
    }
}
