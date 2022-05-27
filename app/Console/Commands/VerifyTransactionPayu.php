<?php

namespace App\Console\Commands;

use App\Event;
use App\PaymentGateway;
use App\payments_payu;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PaymentGateways;
use App\Traits\sendEmail;
use App\Traits\HelperApp;
use App\User;
use Illuminate\Support\Facades\URL;

class VerifyTransactionPayu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TRANSACTION:PAYU';
    use sendEmail;
    use HelperApp;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify transaction of buy with payu';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
 
        $this->getTransactionNotProcess();
    }
    public function getTransactionNotProcess(){
        $urlProduction = "https://api.payulatam.com/reports-api/4.0/service.cgi";
        $urlPruebas = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";
        $payments_payu = payments_payu::where('status', 'init')->orwhere('status', 'PENDING')->get();

        if (count($payments_payu) > 0) {
            foreach ($payments_payu as  $value) {
                //1. verify your mode (Production/Development)
                $getDataPayment = $this->get_data_payment($value->event_id, $value->referenceCode);
                if(isset($getDataPayment)){
                    if($getDataPayment['mode']!='NULL'){
                        $get_payu=$this->validate_payu($getDataPayment['url'], $getDataPayment['apiKey'], $getDataPayment['apiLogin'], $getDataPayment['referenceCode']);
                        $status= $get_payu['status'];
                        if($status=='APPROVED'){
                            $user=User::where('id', $value->user_id)->first();
                            $event = Event::where('id', $value->event_id)->first();
                            $email_user = $user->email;
                            $eventName= $event->name;
                            $URL_EVENT = env('URL_BACK') . "/api/v1/payment-callback/payu?referenceCode=".$value->referenceCode;
                            $templete = '<html><head><title>Encuesta</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><table id="Tabla_01" width="600" height="500" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td><img src="'.env("IMAGE_URL").$event->style['email_img_logo'].'" width="600" height="300" alt=""></td></tr><tr><td><table width="60%" align="center" style="border-left: 4px solid #c1c1c1;"><tr><td><span style="padding-left:6px;font-size:17px;color:black;font-family:system-ui;font-weight:600;">'.$eventName.'</span> </td></tr><tr><td style="font-family:Arial,sans-serif"><span style="font-size:16px"><a href="'.$URL_EVENT.'" style="color:#000;display:inline-block;text-decoration:none;border-width:16px 22px 16px 22px;border-color:#c1c1c1;border-style:solid;background-color:#c1c1c1" target="_blank" >COMPLETAR COMPRA</a></span></td></tr></table></td></tr></table></body></html>';
                            $templete = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($templete));
                            $templete = html_entity_decode($templete);

                            $this->sendEmail($email_user, $eventName, $templete, true, "tickets-approved-cron", "Payment-cron", $value->id);
                            $update_payu= payments_payu::find($value->id);
                            $update_payu->status = 'SEND_EMAIL';
                            $update_payu->save();
                        }else{
                            $update_payu= payments_payu::find($value->id);
                            if($update_payu->number_verify_transaction==3){
                                $update_payu->status = $status;
                                $update_payu->save();
                            }else{
                                if($update_payu->number_verify_transaction=='NULL' || $update_payu->number_verify_transaction==NULL){
                                    $update_payu->number_verify_transaction=0;
                                    $update_payu->save();
                                }
                                if(is_numeric($update_payu->number_verify_transaction)){
                                    if($update_payu->number_verify_transaction<3){
                                        $number_verify=$update_payu->number_verify_transaction;
                                        $update_payu->number_verify_transaction = $number_verify+1;
                                        $update_payu->save();
                                    }
                                }
                            }
                            //Log::info($status); 

                        }
                        
                    }
                }
            }
        }
    }

    public function verifyTransaction($url, $apiKey, $apiLogin, $referenceCode)
    {
        $client = new Client(
            [
                'timeout'  => 30,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]
        );

        $data = array(
            'test' => false,
            'language' => 'en',
            'command' => 'ORDER_DETAIL_BY_REFERENCE_CODE',
            'merchant' => [
                'apiLogin' => $apiLogin,
                'apiKey' => $apiKey
            ],
            'details' => [
                'referenceCode' => $referenceCode
            ]
        );
        try {
            $request = $client->post($url, ['json' => $data]);
            $body = json_decode($request->getBody(), true);
            if ($body && $body['code'] == 'SUCCESS' && $body['result'] && $body['result']['payload'] != null) {
                if(isset($body['result']['payload'][0])){
                    //Log::Info($body['result']['payload'][0]['transactions'][0]['transactionResponse']['state']);

                }else{
                    Log::info('No existe array');
                }
                
            }else{
                Log::info('No hay informacion');
            }
        } catch (\Throwable $th) {
            //Log::info('Error consumir datos mrchipa: ' . $th);
        }
    }

    public function verifyTransaction2()
    {
        $client = new Client(
            [
                'timeout'  => 30,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]
        );

        $data = array(
            'test' => false,
            'language' => 'en',
            'command' => 'ORDER_DETAIL_BY_REFERENCE_CODE',
            'merchant' => [
                'apiLogin' => 'pRRXKOl8ikMmt9u',
                'apiKey' => '4Vj8eK4rloUd272L48hsrarnUA'
            ],
            'details' => [
                'referenceCode' => '6112f2575ce48'
            ]
        );
        try {
            $request = $client->post('https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi', ['json' => $data]);
            $body = json_decode($request->getBody(), true);
            if ($body['code'] == 'SUCCESS' && $body['result']['payload'] != null) {
                //Log::Info($body['result']['payload'][0]['transactions'][0]['transactionResponse']);
            }
            // Log::Info($body['result']['payload'][0]['transactions'][0]['transactionResponse']);
        } catch (\Throwable $th) {
            //Log::info('Error consumir datos mrchipa: ' . $th);
        }
    }
}
