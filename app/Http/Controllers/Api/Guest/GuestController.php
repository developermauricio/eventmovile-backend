<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Guest;

class GuestController extends Controller
{
    public function store (Request $request){

        $rules = [
            'name'  => 'required|min:6',
            'email' => 'required',
            'phone' => 'required|min:6',
            'nit'   => 'required|min:6',
        ];

        $this->validate($request, $rules);

        $guest = Guest::create([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nit'   => $request->nit,
        ]);

        return $this->successResponse(['data'=> $guest, 'message'=>'Guest Created'], 201);


    }

    public function update(Request $request, Guest $guest)
    {
        $rules = [
            'name'  => 'required|min:6',
            'email' => 'required',
            'phone' => 'required|min:6',
            'nit'   => 'required|min:6',
        ];

        $this->validate($request, $rules);

        
        $guest->fill($request->all());
        
        if ($guest->isClean()) {
            return $this->successResponse(['data' => $guest, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $guest->save();

        return $this->successResponse(['data' => $guest, 'message' => 'Guest Updated'],201);
    }
}
