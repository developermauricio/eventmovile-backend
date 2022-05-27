<?php

namespace App\Http\Controllers\Api\WebApp\Ondemand;

use App\Event;
use App\ContentOnDemand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OnDemandController extends Controller
{
    public function createNewOndemand(Request $request) {     
        $newOnDemand = ContentOnDemand::create([
            'title_video' => $request->title_video,
            'iframe_video' => $request->iframe_video,
            'description_video' => $request->description_video ? $request->description_video : '--',
            'event_id' => $request->event_id,
        ]);
        
        return response()->json($newOnDemand);
    }

    public function getAllOnDemandForIdevent( $eventId ) {        
        $listOnDemand = ContentOnDemand::where('event_id', $eventId)->get();
        return response()->json($listOnDemand);
    }

    public function updateItemOnDemand( Request $request ) {         
        return ContentOnDemand::findOrFail($request->id)->update([
            "title_video" => $request->title_video,
            "iframe_video" => $request->iframe_video,
            "description_video" => $request->description_video ? $request->description_video : '--',
        ]);
    }

    public function removeItemOnDemand( $onDemandID ) {        
        $onDemand = ContentOnDemand::findOrFail($onDemandID);
        $onDemand->delete();
        return $this->successResponse(['data' => $onDemand, 'message' => 'Item eliminado'], 201);
    }
        
}
