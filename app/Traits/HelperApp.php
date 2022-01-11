<?php

namespace App\Traits;

use App\Event;
use App\PaymentGateway;
use App\payments_payu;
use App\Ticket;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HelperApp
{
    protected function validate_payu($url, $apiKey, $apiLogin, $referenceCode)
    {
        $status = 'FALSE';
        $data = [];
        $data['wallet'] = 'FALSE';
        $data['status'] = $status;
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
            Log::info('ERROR_CODE EN referenceCode=' . $referenceCode . ':' . $th);
            $status = 'ERROR_CODE';
        }
        $data['status'] = $status;
        return $data;
    }


    protected function get_data_payment($event_id, $referenceCode)
    {
        $data = [];
        $data['apiKey']  = null;
        $data['apiLogin'] = null;
        $data['mode'] = 'NULL';
        $data['referenceCode'] = null;
        $data['url'] = null;
        $urlProduction = "https://api.payulatam.com/reports-api/4.0/service.cgi";
        $urlPruebas = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";

        $PaymentGateway = PaymentGateway::where('event_id', $event_id)->first();
        if (isset($PaymentGateway->id)) {
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

    protected function verifyYourInvitation($event_id, $user_id, $activity)
    {
        $existInvitation = [];
        $existInvitation['status'] = 'FALSE';
        $existInvitation['data_response'] = [];
        $existInvitation['data_request'] = [];
        $existInvitation['data_request']['event_id'] = $event_id;
        $existInvitation['data_request']['user_id'] = $user_id;
        $existInvitation['data_request']['activity'] = $activity;

        try {
            $invitations = DB::table('event_invitations')
                ->join('url_invitations', 'event_invitations.id', '=', 'url_invitations.invitation_id')
                ->where('event_invitations.event_id', $event_id)
                ->where('url_invitations.user_id', $user_id)
                ->where('url_invitations.actived', 1)
                ->select('event_invitations.id as id_event_invitation', 'event_invitations.activities', 'url_invitations.actived', 'url_invitations.user_id', 'url_invitations.token', 'url_invitations.id')
                ->get();
            if (count($invitations) > 0) {
                foreach ($invitations as  $value) {
                    $activities = json_decode($value->activities);
                    if (gettype($activities) == 'array') {
                        if (in_array($activity, $activities)) {
                            $existInvitation['status'] = 'TRUE';
                            $existInvitation['data_response'] = $value;
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            $existInvitation['status'] = 'ERROR';
        }
        Log::info('verifyYourInvitation');
        Log::info($existInvitation);
        return $existInvitation;
    }
    protected function verifyActivityIfFree($event_id, $activity)
    {
        $activityIsFree = [];
        $activityIsFree['status'] = 'FALSE';
        $activityIsFree['data_response'] = [];
        $activityIsFree['data_request'] = [];
        $activityIsFree['data_request']['event_id'] = $event_id;
        $activityIsFree['data_request']['activity'] = $activity;
        $isInATicket = false;
        $existData = false;

        try {
            $tickets = DB::table('events')
                ->join('tickets', 'events.id', '=', 'tickets.event_id')
                ->join('activities', 'events.id', '=', 'activities.event_id')
                ->where('events.id', $event_id)
                ->where('activities.id', $activity)
                ->select('events.id as event_id', 'tickets.id as ticket_id', 'tickets.activities', 'tickets.unit_price')
                ->get();
            Log::info($tickets);
            if (count($tickets) > 0) {
                $existData = true;
                foreach ($tickets as  $value) {
                    $activities = json_decode($value->activities);
                    if (gettype($activities) == 'array') {
                        if (in_array($activity, $activities)) {
                            $isInATicket = true;
                            if ($value->unit_price <= 0) {
                                $activityIsFree['status'] = 'TRUE';
                                $activityIsFree['data_response'] = $value;
                            }
                        }
                    }
                }
            }

            if ($existData) {
                if ($isInATicket == false) {
                    $activityIsFree['status'] = 'TRUE';
                    $activityIsFree['data_response']['message'] = 'ACTIVITY_ISNT_TICKET';
                }
            }else{
                $activityIsFree['status'] = 'TRUE';
                $activityIsFree['data_response']['message'] = 'EVENT_HASNT_TICKET';
            }
        } catch (\Throwable $th) {
            $activityIsFree['status'] = 'ERROR';
        }
        Log::info('verifyActivityIfFree');
        Log::info($activityIsFree);
        return $activityIsFree;
    }
}
