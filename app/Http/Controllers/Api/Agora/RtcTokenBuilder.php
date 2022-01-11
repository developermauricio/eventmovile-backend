<?php
namespace App\Http\Controllers\Api\Agora;


use App\Http\Controllers\Api\Agora\Message;
use App\Http\Controllers\Api\Agora\AccessToken;


class RtcTokenBuilder
{
    const RoleAttendee = 0;
    const RolePublisher = 1;
    const RoleSubscriber = 2;
    const RoleAdmin = 101;

    # appID: The App ID issued to you by Agora. Apply for a new App ID from 
    #        Agora Dashboard if it is missing from your kit. See Get an App ID.
    # appCertificate:	Certificate of the application that you registered in 
    #                  the Agora Dashboard. See Get an App Certificate.
    # channelName:Unique channel name for the AgoraRTC session in the string format
    # uid: User ID. A 32-bit unsigned integer with a value ranging from 
    #      1 to (232-1). optionalUid must be unique.
    # role: Role_Publisher = 1: A broadcaster (host) in a live-broadcast profile.
    #       Role_Subscriber = 2: (Default) A audience in a live-broadcast profile.
    # privilegeExpireTs: represented by the number of seconds elapsed since 
    #                    1/1/1970. If, for example, you want to access the
    #                    Agora Service within 10 minutes after the token is 
    #                    generated, set expireTimestamp as the current 
    #                    timestamp + 600 (seconds)./
    public static function buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs){
        return RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs);
    }

    # appID: The App ID issued to you by Agora. Apply for a new App ID from 
    #        Agora Dashboard if it is missing from your kit. See Get an App ID.
    # appCertificate:	Certificate of the application that you registered in 
    #                  the Agora Dashboard. See Get an App Certificate.
    # channelName:Unique channel name for the AgoraRTC session in the string format
    # userAccount: The user account. 
    # role: Role_Publisher = 1: A broadcaster (host) in a live-broadcast profile.
    #       Role_Subscriber = 2: (Default) A audience in a live-broadcast profile.
    # privilegeExpireTs: represented by the number of seconds elapsed since 
    #                    1/1/1970. If, for example, you want to access the
    #                    Agora Service within 10 minutes after the token is 
    #                    generated, set expireTimestamp as the current 
    public static function buildTokenWithUserAccount($appID, $appCertificate, $channelName, $userAccount, $role, $privilegeExpireTs){
        $token = AccessToken::init($appID, $appCertificate, $channelName, $userAccount);
        $Privileges = AccessToken::Privileges;
        $token->addPrivilege($Privileges["kJoinChannel"], $privilegeExpireTs);
        if(($role == RtcTokenBuilder::RoleAttendee) ||
            ($role == RtcTokenBuilder::RolePublisher) ||
            ($role == RtcTokenBuilder::RoleAdmin))
        {
            $token->addPrivilege($Privileges["kPublishVideoStream"], $privilegeExpireTs);
            $token->addPrivilege($Privileges["kPublishAudioStream"], $privilegeExpireTs);
            $token->addPrivilege($Privileges["kPublishDataStream"], $privilegeExpireTs);
        }
        return $token->build();
    }

    public static function test(){
        $appID = "970CA35de60c44645bbae8a215061b33";
        $appCertificate = "5CFd2fd1755d40ecb72977518be15d3b";
        $channelName = "7d72365eb983485397e3e3f9d460bdda";
        $ts = 1111111;
        $salt = 1;
        $uid = "2882341273";
        $expiredTs = 1446455471;


        $expected = "006970CA35de60c44645bbae8a215061b33IACV0fZUBw+72cVoL9eyGGh3Q6Poi8bgjwVLnyKSJyOXR7dIfRBXoFHlEAABAAAAR/QQAAEAAQCvKDdW";
        $builder = AccessToken::init($appID, $appCertificate, $channelName, $uid);
        $builder->message->salt = $salt;
        $builder->message->ts = $ts;
        $builder->addPrivilege(AccessToken::Privileges["kJoinChannel"], $expiredTs);
        $result = $builder->build();

        return assertEqual($expected, $result);
    }

    
}

function assertEqual($expect, $actual)
    {
        if ($expect != $actual) {
            return false;
        } else {
            return true;
        }
    }