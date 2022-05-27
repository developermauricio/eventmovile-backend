<?php

namespace App\Console\Commands;
namespace App;

use App\Event;
use App\PaymentGateway;
use App\payments_payu;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HelperApp2{
    public function helper_validate_payu($url, $apiKey, $apiLogin, $referenceCode)
    {
        $status = 'FALSE';
        $data=[];
        $data['wallet']='FALSE';
        $data['status']=$status;      
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
                if (isset($body['result']['payload'][0])) {
                    // Log::Info($body['result']['payload'][0]['transactions'][0]['transactionResponse']['state']);
                    $data['wallet'] = $body['result']['payload'][0]['transactions'][0]['transactionResponse'];
                    $status = $body['result']['payload'][0]['transactions'][0]['transactionResponse']['state'];
                } else {
                    $status = 'EMPTY';
                }
            } else {
                $status = 'NULL';
            }
        } catch (\Throwable $th) {
            //Log::info('ERROR_CODE EN referenceCode=' . $referenceCode . ':' . $th);
            $status = 'ERROR_CODE';
        }
        $data['status']=$status;  
        return $data;
    }


    public function hl_get_data_payment($event_id, $referenceCode)
    {
        $data = [];
        $urlProduction = "https://api.payulatam.com/reports-api/4.0/service.cgi";
        $urlPruebas = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";

        $PaymentGateway = PaymentGateway::where('event_id', $event_id)->first();
        if (isset($PaymentGateway->id)) {
            $data['apiKey']  = null;
            $data['apiLogin'] = null;
            $data['mode'] = 'NULL';
            $data['referenceCode'] = null;
            $data['url'] = null;
            if ($PaymentGateway->mode == 0) {
                if (
                    isset($PaymentGateway->key) && $PaymentGateway->key != null && $PaymentGateway->key != ''
                    && isset($PaymentGateway->api_Login) && $PaymentGateway->api_Login != null && $PaymentGateway->api_Login != ''
                ) {
                    $data['mode'] = 'PROD';
                    $data['apiKey'] = $PaymentGateway->key;
                    $data['apiLogin'] = $PaymentGateway->api_Login;
                    $data['referenceCode'] = $referenceCode;
                    $data['url'] = $urlProduction;
                }
            }
            if ($PaymentGateway->mode == 1) {
                if (
                    isset($PaymentGateway->key_dev) && $PaymentGateway->key_dev != null && $PaymentGateway->key_dev != ''
                    && isset($PaymentGateway->api_Login_dev) && $PaymentGateway->api_Login_dev != null && $PaymentGateway->api_Login_dev != ''
                ) {
                    $data['mode'] = 'DEV';
                    $data['apiKey'] = $PaymentGateway->key_dev;
                    $data['apiLogin'] = $PaymentGateway->api_Login_dev;
                    $data['referenceCode'] = $referenceCode;
                    $data['url'] = $urlPruebas;
                }
            }
        }
        return $data;
    }
}
// if (!function_exists('helper_validate_payu')) {
//     function helper_validate_payu($url, $apiKey, $apiLogin, $referenceCode)
//     {
//         $status = 'FALSE';
//         $data=[];
//         $data['wallet']='FALSE';
//         $data['status']=$status;      
//         $client = new Client(
//             [
//                 'timeout'  => 30,
//                 'headers' => [
//                     'Content-Type' => 'application/json',
//                     'Accept' => 'application/json',
//                 ]
//             ]
//         );

//         $data = array(
//             'test' => false,
//             'language' => 'en',
//             'command' => 'ORDER_DETAIL_BY_REFERENCE_CODE',
//             'merchant' => [
//                 'apiLogin' => $apiLogin,
//                 'apiKey' => $apiKey
//             ],
//             'details' => [
//                 'referenceCode' => $referenceCode
//             ]
//         );
//         try {
//             $request = $client->post($url, ['json' => $data]);
//             $body = json_decode($request->getBody(), true);
//             if ($body && $body['code'] == 'SUCCESS' && $body['result'] && $body['result']['payload'] != null) {
//                 if (isset($body['result']['payload'][0])) {
//                     // Log::Info($body['result']['payload'][0]['transactions'][0]['transactionResponse']['state']);
//                     $data['wallet'] = $body['result']['payload'][0]['transactions'][0]['transactionResponse'];
//                     $status = $body['result']['payload'][0]['transactions'][0]['transactionResponse']['state'];
//                 } else {
//                     $status = 'EMPTY';
//                 }
//             } else {
//                 $status = 'NULL';
//             }
//         } catch (\Throwable $th) {
//             Log::info('ERROR_CODE EN referenceCode=' . $referenceCode . ':' . $th);
//             $status = 'ERROR_CODE';
//         }
//         $data['status']=$status;  
//         return $data;
//     }
// }

// if (!function_exists('hl_get_data_payment')) {
//     function hl_get_data_payment($event_id, $referenceCode)
//     {
//         $data = [];
//         $urlProduction = "https://api.payulatam.com/reports-api/4.0/service.cgi";
//         $urlPruebas = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";

//         $PaymentGateway = PaymentGateway::where('event_id', $event_id)->first();
//         if (isset($PaymentGateway->id)) {
//             $data['apiKey']  = null;
//             $data['apiLogin'] = null;
//             $data['mode'] = 'NULL';
//             $data['referenceCode'] = null;
//             $data['url'] = null;
//             if ($PaymentGateway->mode == 0) {
//                 if (
//                     isset($PaymentGateway->key) && $PaymentGateway->key != null && $PaymentGateway->key != ''
//                     && isset($PaymentGateway->api_Login) && $PaymentGateway->api_Login != null && $PaymentGateway->api_Login != ''
//                 ) {
//                     $data['mode'] = 'PROD';
//                     $data['apiKey'] = $PaymentGateway->key;
//                     $data['apiLogin'] = $PaymentGateway->api_Login;
//                     $data['referenceCode'] = $referenceCode;
//                     $data['url'] = $urlProduction;
//                 }
//             }
//             if ($PaymentGateway->mode == 1) {
//                 if (
//                     isset($PaymentGateway->key_dev) && $PaymentGateway->key_dev != null && $PaymentGateway->key_dev != ''
//                     && isset($PaymentGateway->api_Login_dev) && $PaymentGateway->api_Login_dev != null && $PaymentGateway->api_Login_dev != ''
//                 ) {
//                     $data['mode'] = 'DEV';
//                     $data['apiKey'] = $PaymentGateway->key_dev;
//                     $data['apiLogin'] = $PaymentGateway->api_Login_dev;
//                     $data['referenceCode'] = $referenceCode;
//                     $data['url'] = $urlPruebas;
//                 }
//             }
//         }
//         return $data;
//     }
// }
