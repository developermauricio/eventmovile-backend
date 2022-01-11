<?php

namespace App\Http\Controllers\Api\Meeting;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ZoomJWT;

class ZoomMeetingController extends Controller
{
    use ZoomJWT;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function list(Request $request)
    {
        $path = 'users/me/meetings';
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        $data['meetings'] = array_map(function (&$m) {
            $m['start_at'] = $this->toUnixTimeStamp($m['start_time'], $m['timezone']);
            return $m;
        }, $data['meetings']);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }

    public function create($meeting,$bussinesMarket)
    {
        
        $path = 'users/me/meetings'; // Falta el desarrollo de el usuario variable en este path
        $response = $this->zoomPost($path, [
            'topic' => $meeting->title,
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($meeting->start),
            'duration' => $bussinesMarket->business->time_meeting,
            'agenda' => $meeting->title,
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => false,
                'join_before_host' => true,
            ]
        ]);
               

        /*return [
            'success' => $response->status() === 201,
            'data' => json_decode($response->body(), true),
        ];*/

        return $response;
    }

    public function createNetworking($networking)
    {
        $carbon = new \Carbon\Carbon(); 

        $path = 'users/me/meetings';
        $response = $this->zoomPost($path, [
            'topic' => 'Networking heart',
            'type' => 2,
            'start_time' => $carbon,
            'duration' => 60,
            'agenda' => 'Networking heart',
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => false,
                'join_before_host' => true,
            ]
        ]);


        return $response;
    }

    public function get(Request $request, string $id)
    {
        $path = 'meetings/' . $id;
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        if ($response->ok()) {
            $data['start_at'] = $this->toUnixTimeStamp($data['start_time'], $data['timezone']);
        }

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'start_time' => 'required|date',
            'agenda' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }
        $data = $validator->validated();

        $path = 'meetings/' . $id;
        $response = $this->zoomPatch($path, [
            'topic' => $data['topic'],
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => (new \DateTime($data['start_time']))->format('Y-m-d\TH:i:s'),
            'duration' => 30,
            'agenda' => $data['agenda'],
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => false,
                'join_before_host' => true
            ]
        ]);

        return [
            'success' => $response->status() === 204,
            'data' => json_decode($response->body(), true),
        ];
    }

    public function delete(Request $request, string $id)
    {
        $path = 'meetings/' . $id;
        $response = $this->zoomDelete($path);

        return [
            'success' => $response->status() === 204,
            'data' => json_decode($response->body(), true),
        ];
}

}
