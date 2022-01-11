<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Event;
use App\User;
use App\EventInvitation;
use Illuminate\Support\Facades\DB;
use App\Traits\sendEmail;
use App\Traits\formatRegistrationEmail;

class SendEmails {
    use sendEmail, formatRegistrationEmail;
    
    function principal(){
        $path = base_path()."/app/Console/Commands/emails.json"; // ie: /var/www/laravel/app/storage/json/filename.json
        //echo $path;
        $json = [];
        //$json = json_decode(file_get_contents($path), true); 
        $i=0;

        $event = Event::with('style')->findOrFail(44);
        foreach($json as $data){
            $i++;
            $user = DB::table('users')->where('email',$data[key($data)])->first();

            if($user){
                $invitation = EventInvitation::where("event_id", $event->id)->where("email", $user->email)->first();
                $message1 = str_replace("*u", $user->name, $event->message_email);
                $message = str_replace("*e", $event->name, $message1);

                $format = $this->formatEmailEvent($message, $event, $invitation->id, $user->id);
                //$email = $this->sendEmail($user->email, $event->subject_email, "'".$format['template']."'");
                echo "Correo registrado ".$i.": ".$data[key($data)]. "\n";
                sleep(2);
            }   else {
                echo "Correo NO registrado ".$i.": ".$data[key($data)]. "\n";
            }
            

            
            
        }
    }
}
//$sendmails = new SendEmails();
//$sendmails->principal();


//exit(0);