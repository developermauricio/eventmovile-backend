<?php
namespace App\Http\Controllers\Api\MercadoPago;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MercadoPago;
//require_once __DIR__  . '/vendor/autoload.php';
MercadoPago\SDK::setAccessToken("TEST-1763094492931391-050121-ce63a81ef6ab0d90443e6df38b26b4af-39354813");

class MercadoPagoController extends Controller{
    public function store(Request $request){

        $rules = [
            'description'       => 'required',
            'quantity'      => 'required',
            'price'  => 'required',    
        ];

        $this->validate($request, $rules);

        $preference = new MercadoPago\Preference();

        $item = new MercadoPago\Item();
        $item->title = $request->description;
        $item->quantity = $request->quantity;
        $item->unit_price = $request->price;

        $preference->items = array($item);

        $preference->back_urls = array(
            "success" => $request->getHost()."/api/v1/mercadopago/feedback",
            "failure" => $request->getHost()."/api/v1/mercadopago/feedback", 
            "pending" => $request->getHost()."/api/v1/mercadopago/feedback"
        );

        $preference->auto_return = "approved"; 

        $preference->save();

        return $this->showOne(array(
            'id' => '39354813-c0ba3485-d539-4cc5-9b21-d879f88955ca',
        ));
    }

    public function feedback(Request $request){

        $respuesta = array(
            'Payment' => $request->payment_id,
            'Status' => $request->status,
            'MerchantOrder' => $request->merchant_order_id        
        ); 
        
        return $respuesta;
    }
}
