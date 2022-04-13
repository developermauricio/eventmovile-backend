<?php

namespace App\Http\Controllers\Api\Certificate;

use App\User;
use App\RegisterEvent;
use App\CertificateModel;
use Dompdf\Dompdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateManagementController extends Controller
{
    public function viewCertificate( $event ) {
        //Log::debug('id del evento ');
        //Log::debug($event);

        $eventRegistrationFields = RegisterEvent::where('event_id', $event)->get();

        return view('certificate')->with([
            'eventRegistrationFields' => $eventRegistrationFields,
            'event' => $event
        ]); 
    }

    public function downloadCertificate( $event, $user ) {
        $certificateData = CertificateModel::where('event_id', $event)->first();
        //$certificateData = CertificateModel::where('id', 4)->first();

        if ( !$certificateData ) {
            return response()->json(['status' => 'fail', 'msg' => 'No hay certificados para este evento.']);
        }
                
        $currentUser = User::findOrFail($user, ["id", "name", "lastname", "email"]);

        if ( !$currentUser ) {
            return response()->json(['status' => 'fail', 'msg' => 'El usuario no se encuentra registrado.']);
        }

        $fields = json_decode( $certificateData->text_fields );
        $urlBase = env('APP_URL');
        $imgBackground = $urlBase . $certificateData->background_image;       

        $html = "<html>
            <head>
            <style>
            *{margin:0}
            </style>
            </head>
            <div style='width:792px; height:612px; background-image:url($imgBackground); background-repeat:no-repeat; margin:0;'>";

        foreach ( $fields as $item ) {           
            if ( $item->type == 'text' ) {
                
                $data = $this->getDataUser( $item->column, $currentUser ); 
                $html .= "<p style='display:block; width:{$item->width}px; position:absolute; top:{$item->y}px; left:{$item->x}px; text-align:{$item->align}; font-size:{$item->size}px;'>" . $data . "</p>";
                
            } else {  

                $urlImages = $urlBase . '/storage' . $item->src;
                $html .= "<img style='display:block; width:{$item->width}px; height:{$item->height}px; position:absolute; top:{$item->y}px; left:{$item->x}px; object-fit:contain;' src={$urlImages} />";
            
            }            
        }      
        
        $html .= "</div> </html>";
        
        // $html = preg_replace('/>\s+</', "><", $html);
        //return response()->json(['status' => 'ok']);

        // falta guardar la persona que descargo el certificado

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $customPaper = array(0,0,594,459);
        $dompdf->set_paper($customPaper); 
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->render();
        $time = time();
        $dompdf->stream("certificado-" . $time . ".pdf");
       
        return $dompdf->stream("certificado-" . $time . ".pdf");
    }

    public function  getDataUser( $dataUser, $currentUser ) {
        $data = explode(",", $dataUser);

        if ( $data[0] == -1 ) {
            $result = '';

            switch ( $data[1] ) {
                case 'name':
                    $result = $currentUser->name;
                    break;
                case 'lastname':
                    $result = $currentUser->lastname;
                    break;
                case 'fullname':
                    $result = $currentUser->name . " " . $currentUser->lastname;
                    break;
            }

            return $result;            
        } else {
            // falta consultar datos del user
            Log::debug('datos extra del usuario... ');
        }
    }

    public function getCertifictesForEvent( $event ) {
        $listCertificates = CertificateModel::where('event_id', $event)->get();
        //$listCertificates = CertificateModel::where('event_id', $event)->first();
        return response()->json($listCertificates); 
    }

    public function saveCertificate( Request $request ) {        
        $background = $request->file;
        $urlImg = '';

        if ( $background ) {
            $nameFile = $background->getClientOriginalName();
            $explode = explode(".", $nameFile);
            $nameFile = $explode[0] . "-" . $request->event_id . "." . $explode[1];
            $path = Storage::disk('public')->put('/imgcertificates/'.$nameFile,  \File::get($background));
            $urlImg = '/storage/imgcertificates/' . $nameFile;
        }

        DB::beginTransaction();
        try {
            $certificate = CertificateModel::create([
                'name_certificate' => $request->name_certificate,
                'background_image' => $urlImg,
                'text_fields' => $request->components,
                'event_id' => $request->event_id,
            ]);

            DB::commit();
            return response()->json(['status' => 'ok', 'msg' => 'registro agredado correctamente.']); 
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());
            return response()->json(['status' => 'fail', 'msg' => 'registro fallido.']);
        }        
    }

    public function saveAddImage( Request $request ) {
        $img = $request->file;
        $urlImg = '';

        if ( $img ) {
            $nameFile = $img->getClientOriginalName();
            $explode = explode(".", $nameFile);
            $nameFile = $explode[0] . "-" . $request->event_id . "." . $explode[1];
            $path = Storage::disk('public')->put('/imgAddCertificates/'.$nameFile,  \File::get($img));
            $urlImg = '/imgAddCertificates/' . $nameFile;
        }

        return $urlImg;
    }

    public function removeAddImage( Request $request ) {
        $url = $request->url_img;

        if ( $url ) {
            Storage::delete( $url );
        }

        return response()->json(['status' => 'ok', 'msg' => 'Imagen eliminada correctamente.']);
    }

}
