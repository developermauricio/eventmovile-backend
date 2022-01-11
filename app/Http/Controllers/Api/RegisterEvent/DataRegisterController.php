<?php

namespace App\Http\Controllers\Api\RegisterEvent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataRegister;

class DataRegisterController extends Controller
{
    public function showDataRegister($user, $event){

        $data = DB::table('data_registers as dr')->select('dr.*', 're.name as field')
            ->join('register_events as re', 're.id', '=', 'dr.register_id')
            ->where('re.event_id', $event)
            ->where('dr.user_id', $user)
            ->get();
        
        return $this->showAll($data, 201);
    }
    public function index() {

        $dataRegister = DataRegister::all();

        return $this->showAll($dataRegister,200);
    }

    public function show(DataRegister $dataRegister)
    {
    
        return $this->showOne($dataRegister);
    }

    public function showFieldsEvent($event)
    {
        $dataRegister = DataRegister::where('event_id', $event)->orderBy('id', 'desc')->get();

        return $this->showAll($dataRegister);
    }

    /**
     * Registra los datos de un registro de evento
     * @param
     * Request POST con:
     * user_id = id de usuario (recien registrado)
     * register_id = id register_events (campos persoanalizados)
     * value = valor del campo personalizado para el usuario
     * @return
     * success 201 registro creado
     */
    public function store(Request $request){

        $rules = [
            'user_id'   => 'required|exists:users,id',
            'register_id'   => 'required|exists:register_events,id',
            'value'       => 'required' 
        ];

        $this->validate($request, $rules);

        $dataR = DataRegister::where('user_id', $request->user_id)->where('register_id', $request->register_id)->first();

        if($dataR){
            return $this->successResponse(['data'=> $dataR, 'message'=>'Data exists'], 201);
        }

        $dataRegister = DataRegister::create([
            'user_id'  => $request->user_id,
            'register_id'      => $request->register_id,
            'value'      => $request->value
        ]);

        return $this->successResponse(['data'=> $dataRegister, 'message'=>'Data Created'], 201);
    }

    public function update(Request $request, DataRegister $dataRegister){
        
        $rules = [
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required',
            'type'       => 'required' 
        ];


        $this->validate($request, $rules);

                
        $dataRegister->fill($request->all());
        
        if ($dataRegister->isClean()) {
            return $this->successResponse(['data' => $dataRegister, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $dataRegister->save();

        return $this->successResponse(['data' => $dataRegister, 'message' => 'Field Updated'],201);
    }

    public function destroy(DataRegister $dataRegister)
    {
        $dataRegister->delete();   
        return $this->successResponse(['data' => $dataRegister, 'message' => 'Field Deleted'], 201);
    }

    
}
