<?php

namespace App\Http\Controllers\Api\Agora;

use App\Http\Controllers\Controller;
use App\NetworkingCall;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Agora\RtcTokenBuilder;


class AgoraController extends Controller
{

    public function genToken(Request $request)
    {
        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->meeting_id;;
        $uid = (int)mt_rand(1000000000, 9999999999);
        $uidStr = (string)$uid;
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new \DateTime("now", new \DateTimeZone('America/Bogota')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $test = RtcTokenBuilder::test();

        $token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, 0, $role, $privilegeExpiredTs);
        $token2 = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, 0, $role, $privilegeExpiredTs);

        NetworkingCall::create([
            'channel' => $request->meeting_id,
            'type' => $request->type_call,
            'event_id' => $request->event_id,
            'creator_id' => $request->creator_id,
            'guest_id' => $request->guest_id
        ]);


        return $this->successResponse(['data' => ['test' => $test, 'token' => $token, 'token2' => $token2, 'uid' => $uid], 'message' => 'Created'], 200);
    }
}
