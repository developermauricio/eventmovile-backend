<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LaravelQRCode\Facades\QRCode;
use Spatie\CalendarLinks\Link;

trait formatRegistrationEmail{
    public function formatRegistrationEmail($user, $businessData) {
            
        if($businessData->message_email == "")
            $businessData->message_email = " *u se a registrado exitosamente en la rueda de negocios: *e";
        if($businessData->subject_email == "")
            $businessData->subject_email = "Registro exitoso - rueda de negocios";

        $start = new \Carbon\Carbon($businessData->start_date);
        $start = $start ->format('Y-m-d H:i');
        $end =  new \Carbon\Carbon($businessData->end_date);
        $end = $end ->format('Y-m-d H:i'); 

        $from = \DateTime::createFromFormat('Y-m-d H:i', $start);
        $to = \DateTime::createFromFormat('Y-m-d H:i', $end);

        $link = Link::create($businessData->name, $from, $to)
            ->description($businessData->sort_description);

        $link_google = $link->google();
        $outlook = $link->webOutlook();
        $ics = $link->ics();

        $message1 = str_replace("*u", $user->name, $businessData->message_email);
        $message2 = str_replace("*e", $businessData->name, $message1);
        $message = str_replace("*c", $businessData->password, $message2);
        $businessData->message_email = $message;
        $templete = view('business-market.confirmation', ["bussines"=>$businessData, "user" => $user, 'link_google' => $link_google, 'outlook' => $outlook, 'ics' => $ics]);
        return ['subject'=>$businessData->subject_email, 'template'=>$templete];
    }

    public function formatEmailEvent($message, $event, $qr, $tracking, $halls= null,$message_email=null) {
        if($qr != false){
            // cambiar la ruta donde se guarda el qr
            // $path = Storage::disk('public')->put('/documents/'.$nameFile,  \File::get($file));
            QRCode::text($qr)->setSize(15)->setOutfile('../storage/storage/qr-code'.$qr.'.png')->png();
        }

        if($message_email!=null){
            $message_email = json_decode($message_email);
        }
        $start = new \Carbon\Carbon($event->start_date);
        $start = $start ->format('Y-m-d H:i');
        $end =  new \Carbon\Carbon($event->end_date);
        $end = $end ->format('Y-m-d H:i'); 

        $from = \DateTime::createFromFormat('Y-m-d H:i', $start);
        $to = \DateTime::createFromFormat('Y-m-d H:i', $end);

        $link = Link::create($event->name, $from, $to)
            ->description($event->description);        

        $link_google = $link->google();//todo google, android, dispositivos con cuentas google asociadas
        $outlook = $link->webOutlook();//para outlook web no funciona para aplicacion desktop de outlook, se abrira siempre la web se inicia sesion si no tiene sesion iniciada
        $ics = $link->ics();       //para iphone, navegadores y cuentas apple
        //office 365
        $start_to_365 = date("c", strtotime($event->start_date));
        $end_to_365  = date("c", strtotime($event->end_date));      
        $desc_365 = $rest = str_replace("<p>", "", $event->description);
        $desc_365 = $rest = str_replace("</p>", "", $desc_365);
        $outlook_365 = "https://outlook.office.com/calendar/0/deeplink/compose?body=".$desc_365."&enddt=".$end_to_365."&path=%2Fcalendar%2Faction%2Fcompose&rru=addevent&startdt=".$start_to_365."&subject=".$event->name;
        
        $templete = view('events.confirmation', [
            "message"=>$message,
            "event" => $event, 
            "qr" => $qr, 
            'tracking' => $tracking, 
            'link_google' => $link_google, 
            'outlook' => $outlook, 
            'outlook_365'=>$outlook_365,
            'ics' => $ics,
            'halls' =>$halls,
            'message_email'=>$message_email ]
        );

        return ['template'=>$templete];
    }    
}