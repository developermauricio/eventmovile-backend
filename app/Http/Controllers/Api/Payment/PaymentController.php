<?php

namespace App\Http\Controllers\Api\Payment;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MercadoPago;
use App\Payment;
use App\Ticket;
use App\PaymentGateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\sendEmail;
use App\EventInvitation;
use App\UrlInvitation;

use App\Event;
use App\EventUser;
use App\payments_payu;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use App\Traits\HelperApp;
use Illuminate\Support\Env;

class PaymentController extends Controller
{
    use sendEmail;
    use HelperApp;

    public function store($type_wallet, Request $request)
    {
        if ($type_wallet == 'Mercadopago') {
            $rules = [
                'description'       => 'required',
                'quantity'      => 'required',
                'price'  => 'required',
                'event_id' => 'required',
                'ticket_id' => 'required',
                'payment_gateway' => 'required',
                'email' => 'required'
            ];

            $this->validate($request, $rules);

            $email_user =  $this->create_user($request);
            if ($email_user == null) {
                return $this->showOne('Hubo un error al realizar la transacción', 401);
            }

            $gateway = PaymentGateway::where('event_id', $request->event_id)->firstOrFail();
            $payment = $this->gatewayAction($gateway, $request);

            $toSave = $request->all();
            $toSave['id_payment_gateway'] = $payment->id;
            $toSave['user_id'] = $email_user->id;
            $payment = Payment::create($toSave);
            return $this->showOne($payment);
        } else {
            $rules = [
                'description'       => 'required',
                'quantity'      => 'required',
                'price'  => 'required',
                'event_id' => 'required',
                'ticket_id' => 'required',
                'payment_gateway' => 'required',
            ];

            $this->validate($request, $rules);
            $gateway = PaymentGateway::where('event_id', $request->event_id)->firstOrFail();
            $payment = $this->transactionPayu($gateway, $request);
            if ($payment == null) {
                return $this->showOne('Hubo un error al realizar la transacción', 401);
            }
            return $this->showOne($payment);
        }
    }

    public function show(Payment $payment)
    {
        return $this->showOne($payment);
    }

    private function gatewayAction($gateway, $request)
    {
        $credentials = null;

        if ($gateway->payment_name == 'Mercadopago') {
            if ($gateway->mode == 1) {
                $credentials = array('key' => $gateway->key_dev, 'token' => $gateway->token_dev);
            } else {
                $credentials = array('key' => $gateway->key, 'token' => $gateway->token);
            }

            MercadoPago\SDK::setAccessToken($credentials['token']);
            $preference = new MercadoPago\Preference();

            $item = new MercadoPago\Item();
            $item->title = $request->description;
            $item->quantity = $request->quantity;
            $item->unit_price = $request->price;
            $preference->items = array($item);
            $preference->back_urls = array(
                "success" => env('URL_BACK') . "/api/v1/payment-callback/Mercadopago",
                "failure" => env('URL_BACK') . "/api/v1/payment-callback/Mercadopago",
                "pending" => env('URL_BACK') . "/api/v1/payment-callback/Mercadopago"
            );
            $preference->auto_return = "approved";
            $preference->save();

            return $preference;
        }
    }

    public function callback($type_wallet, Request $request)
    {
        if ($type_wallet == 'Mercadopago') {
            Log::info('Entro a mercadopago');
            $urlMercadoPago = $this->callback_mercadopago($request);
            Log::info(5);
            Log::info($urlMercadoPago);
            return redirect($urlMercadoPago);
            // $answer = array(
            //     'Payment' => $request->payment_id,
            //     'Status' => $request->status,
            //     'MerchantOrder' => $request->merchant_order_id,
            //     'Preference_id' => $request->preference_id
            // );

            // $payment = Payment::where('id_payment_gateway', $answer['Preference_id'])->firstOrFail();
            // $payment->status = $answer['Status'];
            // $payment->save();

            // if ($answer['Status'] == 'approved') {

            //     $ticket = Ticket::where('id', $payment->ticket_id)->firstOrFail();
            //     $user = User::where('id', $payment->user_id)->firstOrFail();
            //     $event = Event::where('id', $payment->event_id)->firstOrFail();
            //     $eventInvitation = EventInvitation::create([
            //         'event_id' => $payment->event_id,
            //         'email'  => $user->email,
            //         'name'  => $user->name,
            //         'activities' => $ticket->activities,
            //         'quantity'  => $payment->quantity,
            //         'type' => 'ticket'
            //     ]);



            //     $tokens = '';
            //     $urls = "";
            //     $token = Str::random(5);

            //     $verifyToken = UrlInvitation::select('token')
            //         ->where('token', $token)->first();

            //     while (isset($verifyToken->token)) {
            //         $token = Str::random(5);
            //         $verifyToken = UrlInvitation::select('token')->pluck('token')
            //             ->where('token', $token)->first();
            //     }

            //     $urlInvitation = UrlInvitation::create([
            //         'url' => 'Url',
            //         'token' => $token,
            //         'user_id' => $user->id,
            //         'invitation_id' => $eventInvitation->id,
            //         'actived' => true,
            //     ]);

            //     for ($i = 0; $i < ($eventInvitation->quantity - 1); $i++) {
            //         $token = Str::random(5);

            //         $verifyToken = UrlInvitation::select('token')
            //             ->where('token', $token)->first();

            //         while (isset($verifyToken->token)) {
            //             $token = Str::random(5);
            //             $verifyToken = UrlInvitation::select('token')->pluck('token')
            //                 ->where('token', $token)->first();
            //         }

            //         $urlInvitation = UrlInvitation::create([
            //             'url' => 'Url',
            //             'token' => $token,
            //             'user_id' => null,
            //             'invitation_id' => $eventInvitation->id,
            //             'actived' => false,
            //         ]);
            //         $urls = $urls . "<br>" . env('FRONT') . '#/Register-Event-Token?token=' . $token;
            //         $tokens = $tokens . "<br><a target='_blank'  href='" . env('FRONT') . '#/Register-Event-Token?token=' . $token . "'>
            //     <button style='background-color:" . $event->style->email_btn_color . "; color:" . $event->style->email_btn_text_color . ";padding: 10px 20px;
            //     width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px;margin-top:10px;'>Usar invitación</button><a>";
            //     }

            //     if ($payment->quantity > 1) {
            //         $txtEmail = "Has comprado " . $payment->quantity . " tickets (" . $ticket->name . ")<br>Registramos uno a tu nombre y te enviamos link para quienes quieras invitar:";
            //         $urlsText = "<br><br>o puedes compartir las urls para cada uno de los invitados:" . $urls;
            //     } else {
            //         $txtEmail = "Has Comprado un ticket " . $ticket->name . " para el evento<br><a target='_blank'  href='" . env('FRONT') . '#/Landing-Event?eventId=' . $event->id . "'>
            //     <button style='background-color:" . $event->style->email_btn_color . "; color:" . $event->style->email_btn_text_color . ";padding: 10px 20px;
            //     width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px;margin-top:10px;'>Usar invitación</button><a>";
            //         $urlsText = "";
            //     }

            //     $tokens = $txtEmail . $tokens . $urlsText;
            //     $templete = view('events.standar', ["event" => $event, "message" => $tokens]);
            //     $templete = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($templete));
            //     $templete = html_entity_decode($templete);

            //     $email = $this->sendEmail($eventInvitation->email, $event->name, $templete, true, "tickets-approved", "Payment", $payment->id);
            // }

            // return redirect(env('FRONT') . '#/Landing-Event?eventId=' . $payment->event_id . '&payment=' . $payment->id . '&status=' . $payment->status);
        } else {
            $urlPayu = $this->callback_payu($request);
            return redirect($urlPayu);
        }
    }


    public function createMD5($apiKey, $merchantId, $reference, $value, $currency)
    {
        return md5($apiKey . '~' . $merchantId . '~' . $reference . '~' . $value . '~' . $currency);
    }
    public function transactionPayu($gateway, $request)
    {
        $referenceCode = uniqid();
        $value = $request->quantity * $request->price;
        $currency = 'COP';
        $email_user =  $this->create_user($request);
        if (!isset($email_user->id)) {
            return null;
        }
        $signature = '';
        if ($gateway->mode == 1) {
            $signature = $this->createMD5($gateway->key_dev, $gateway->merchantId_dev, $referenceCode, $value, $currency);
            $credentials = array(
                'merchantId' => $gateway->merchantId_dev,
                'accountId' => $gateway->accountId_dev,
                'referenceCode' => $referenceCode,
                'description' => $request->description,
                'amount' => $value,
                'tax' => 0,
                'taxReturnBase' => 0,
                'currency' => $currency,
                'signature' => $signature,
                'test' => 1,
                'buyerEmail' => $email_user->email,
                'responseUrl' => env('URL_BACK') . "/api/v1/payment-callback/payu",
                'confirmationUrl' => env('URL_BACK') . "/api/v1/payment-callback/payu"
            );
        } else {
            $signature = $this->createMD5($gateway->key, $gateway->merchantId, $referenceCode, $value, $currency);
            $credentials = array(
                'merchantId' => $gateway->merchantId,
                'accountId' => $gateway->accountId,
                'referenceCode' => $referenceCode,
                'description' => $request->description,
                'amount' => $value,
                'tax' => 0,
                'taxReturnBase' => 0,
                'currency' => $currency,
                'signature' => $signature,
                'test' => 0,
                'buyerEmail' => $email_user->email,
                'responseUrl' => env('URL_BACK'). "/api/v1/payment-callback/payu",
                'confirmationUrl' => env('URL_BACK') . "/api/v1/payment-callback/payu"
            );
        }

        $toSave = array(
            'quantity' => $request->quantity,
            'ticket_id' => $request->ticket_id,
            'event_id' => $request->event_id,
            'user_id' => $email_user->id,
            'signature' => $signature,
            'referenceCode' => $referenceCode
        );
        payments_payu::create($toSave);
        return $credentials;
    }
    public function create_user($request)
    {
        $user_email = trim($request->email);
        $user_exist = User::where('email', $user_email)->first();
        $user = null;
        $event = Event::where('id', $request->event_id)->first();
        if (!isset($user_exist)) {
            Log::info('Hay que crearlo');
            $toCreate = [];
            $toCreate['name'] = '';
            $toCreate['email'] = $user_email;
            $toCreate['password'] = Hash::make($event->password);
            $toCreate['actived'] = 0;
            $user = User::create($toCreate);
        } else {
            Log::info('No hay que crearlo');
            $user = $user_exist;
        }
        Log::info($user);
        return $user;
    }
    public function create_user_in_event($payment)
    {
        $user_exist_in_event = EventUser::where('user_id', $payment->user_id)->where('event_id', $payment->event_id)->first();
        if (!isset($user_exist_in_event->id)) {
            $toCreate = [];
            $toCreate['user_id'] = $payment->user_id;
            $toCreate['event_id'] = $payment->event_id;
            $eventUser = EventUser::create($toCreate);
        }
    }
    public function callback_payu($request)
    {
        $status = 'rejected';
        if (isset($request->referenceCode)) {
            $payment = payments_payu::where('referenceCode', $request->referenceCode)->firstOrFail();
            $getDataPayment = $this->get_data_payment($payment->event_id, $payment->referenceCode);
            if (isset($getDataPayment)) {
                if ($getDataPayment['mode'] != 'NULL') {
                    $get_payu = $this->validate_payu($getDataPayment['url'], $getDataPayment['apiKey'], $getDataPayment['apiLogin'], $getDataPayment['referenceCode']);
                    Log::info($get_payu['wallet']);
                    $status = $get_payu['status'];
                    if ($get_payu['wallet']) {
                        $payment->status = $get_payu['wallet']['state'];
                        $payment->processingDate = $get_payu['wallet']['operationDate'];
                        $payment->save();
                        $status = $get_payu['status'];
                        if ($status == 'APPROVED') {
                            // $this->create_user_in_event($payment);
                            $this->generateInvitation($payment);
                            // $user_exist = User::where('id', $payment->user_id)->first();
                            // if (isset($user_exist->id)) {
                            //     if ($user_exist->actived == 0) {
                            //         return env('FRONT') . '#/Register-Event?eventId=' . $payment->event_id . '&email=' . $user_exist->email;
                            //     }
                            // } else {
                            //     return env('FRONT') . '#/Register-Event?eventId=' . $payment->event_id . '&estatus=2';
                            // }
                            $status = 'approved';
                        }
                    }
                }
            }
            if ($payment->status == 'PENDING') {
                $status = 'in_process';
            }
        }
        return env('FRONT') . '#/Landing-Event?eventId=' . $payment->event_id . '&payment=' . $payment->id . '&status=' . $status;
    }

    public function callback_mercadopago($request)
    {
        $answer = array(
            'Payment' => $request->payment_id,
            'Status' => $request->status,
            'MerchantOrder' => $request->merchant_order_id,
            'Preference_id' => $request->preference_id
        );
        $payment = Payment::where('id_payment_gateway', $answer['Preference_id'])->firstOrFail();
        $payment->status = $answer['Status'];
        $payment->save();
        if ($answer['Status'] == 'approved') {
            // $this->create_user_in_event($payment);
            $this->generateInvitation($payment);
            // $user_exist = User::where('id', $payment->user_id)->first();
            // if (isset($user_exist->id)) {
            //     if ($user_exist->actived == 0) {
            //         return env('FRONT') . '#/Register-Event?eventId=' . $payment->event_id . '&email=' . $user_exist->email;
            //     }
            // } else {
            //     return env('FRONT') . '#/Register-Event?eventId=' . $payment->event_id . '&estatus=2';
            // }
        }
        return env('FRONT') . '#/Landing-Event?eventId=' . $payment->event_id . '&payment=' . $payment->id . '&status=' . $payment->status;
    }
    public function generateInvitation($payment)
    {
        Log::info("payment:");
        Log::info($payment);
        $ticket = Ticket::where('id', $payment->ticket_id)->firstOrFail();
        Log::info("ticket:");
        Log::info($ticket);
        $user = User::where('id', $payment->user_id)->firstOrFail();
        $event = Event::where('id', $payment->event_id)->firstOrFail();
        $eventInvitation = EventInvitation::create([
            'event_id' => $payment->event_id,
            'email'  => $user->email,
            'name'  => $user->name,
            'activities' => $ticket->activities,
            'quantity'  => $payment->quantity,
            'type' => 'ticket'
        ]);
        $IsEventInvitations = EventInvitation::where('event_id', $payment->event_id)->get();

        //Init validate If the user_id has a invitation
        // $activities_of_ticket = json_decode($ticket->activities);
        // $UserHasTicket = [];
        // if (gettype($activities_of_ticket) == 'array') {
        //     foreach ($activities_of_ticket as $activity_of_ticket) {
        //         foreach ($IsEventInvitations as $IsEventInvitation) {
        //             $activities_of_eventInvitation = json_decode($IsEventInvitation->activities);
        //             if (gettype($activities_of_eventInvitation) == 'array') {
        //                 if (in_array($activity_of_ticket, $activities_of_eventInvitation)) {
        //                     array_push($UserHasTicket, $IsEventInvitation->id);
        //                 }
        //             }
        //         }
        //     }
        // }
        // $isUserInvited = false;
        // foreach ($UserHasTicket as  $value) {
        //     $userInvited = UrlInvitation::where('invitation_id', $value)->where('user_id', $payment->user_id)->first();
        //     if (isset($userInvited->id)) {
        //         $isUserInvited = true;
        //     }
        // }
        //Finish validate If the user_id has a invitation



        $tokens = '';
        $urls = "";
        $token = Str::random(5);
        $numbers_invitations = $eventInvitation->quantity;
        // if ($isUserInvited == false) {
        //     $verifyToken = UrlInvitation::select('token')
        //         ->where('token', $token)->first();

        //     while (isset($verifyToken->token)) {
        //         $token = Str::random(5);
        //         $verifyToken = UrlInvitation::select('token')->pluck('token')
        //             ->where('token', $token)->first();
        //     }

        //     $urlInvitation = UrlInvitation::create([
        //         'url' => 'Url',
        //         'token' => $token,
        //         'user_id' => $user->id,
        //         'invitation_id' => $eventInvitation->id,
        //         'actived' => true,
        //     ]);
        //     $numbers_invitations = $numbers_invitations - 1;
        // }


        for ($i = 0; $i < ($numbers_invitations); $i++) {
            $token = Str::random(5);

            $verifyToken = UrlInvitation::select('token')
                ->where('token', $token)->first();

            while (isset($verifyToken->token)) {
                $token = Str::random(5);
                $verifyToken = UrlInvitation::select('token')->pluck('token')
                    ->where('token', $token)->first();
            }

            $urlInvitation = UrlInvitation::create([
                'url' => 'Url',
                'token' => $token,
                'user_id' => null,
                'invitation_id' => $eventInvitation->id,
                'actived' => false,
            ]);
            $urls = $urls . "<br><a target='_blank'  href='" . env('FRONT') . "#/Register-Event-Token?token=" . $token . "'>" . env('FRONT') . '#/Register-Event-Token?token=' . $token . "</a>";

            // $tokens = $tokens . "<br><a target='_blank'  href='" . env('FRONT') . '#/Register-Event-Token?token=' . $token . "' style='background-color:" . $event->style->email_btn_color . "; color:" . $event->style->email_btn_text_color . ";padding: 10px 20px;
            // width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px;margin-top:10px;'>Usar invitación<a>";
        }

        // if ($isUserInvited == false) {
        //     if ($payment->quantity > 1) {
        //         $txtEmail = "Has comprado " . $payment->quantity . " tickets " . $ticket->name . "<br>";
        //         $urlsText = "<br><br>Comparte uno de los siguientes enlaces para cada invitado:" . $urls;
        //     } else {
        //         $txtEmail = "Has Comprado un ticket " . $ticket->name . " <br><a target='_blank'  href='" . env('FRONT') . '#/login?eventId=' . $event->id . "'>
        //     <button style='background-color:" . $event->style->email_btn_color . "; color:" . $event->style->email_btn_text_color . ";padding: 10px 20px;
        //     width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px;margin-top:10px;'>Ir al evento</button></a>";
        //         $urlsText = "";
        //     }
        // } else {
        //     if ($payment->quantity > 1) {
        //         $txtEmail = "Has comprado " . $payment->quantity . " tickets (" . $ticket->name . ")";
        //         $urlsText = "<br><br>Comparte uno de los siguientes enlaces para cada invitado:" . $urls;
        //     } else {
        //         $txtEmail = "Has Comprado un ticket " . " (" . $ticket->name . ")";
        //         $urlsText = "<br><br>Comparte el siguiente enlace con su invitado:" . $urls;
        //     }
        // }

        // if ($isUserInvited == false) {
        //     if ($payment->quantity > 1) {
        //         $txtEmail = "Has comprado " . $payment->quantity . " tickets " . $ticket->name . "<br>";
        //         $urlsText = "<br><br>Comparte uno de los siguientes enlaces para cada invitado:" . $urls;
        //     } else {
        //         $txtEmail = "Has Comprado un ticket " . $ticket->name . " <br><a target='_blank'  href='" . env('FRONT') . '#/login?eventId=' . $event->id . "'>
        //     <button style='background-color:" . $event->style->email_btn_color . "; color:" . $event->style->email_btn_text_color . ";padding: 10px 20px;
        //     width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px;margin-top:10px;'>Ir al evento</button></a>";
        //         $urlsText = "";
        //     }
        // } else {
        if ($payment->quantity > 1) {
            $txtEmail = "¡Tu compra ha sido exitosa! Has comprado " . $payment->quantity . " tickets (" . $ticket->name . ")";
            $urlsText = "<br><br>Comparte estos enlaces con el titular de cada ticket para iniciar registro:" . $urls;
        } else {
            $txtEmail = "¡Tu compra ha sido exitosa! Has Comprado un ticket " . " (" . $ticket->name . ")";
            $urlsText = "<br><br>Comparte estos enlaces con el titular de cada ticket para iniciar registro:" . $urls;
        }
        // }

        $tokens = $txtEmail . $tokens . $urlsText;
        $templete = view('events.standar', ["event" => $event, "message" => $tokens]);
        $templete = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($templete));
        $templete = html_entity_decode($templete);

        $email = $this->sendEmail($eventInvitation->email, $event->name, $templete, true, "tickets-approved", "Payment", $payment->id);
    }

    public function showforuser($event_id, $user_id)
    {
    }
}
