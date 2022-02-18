<?php
namespace App\Traits;


use GuzzleHttp\Client;
use App\Email;
use Illuminate\Support\Facades\Log;

trait sendEmail{

    protected function injectTrackingPixel($html, $hash)
    {
    
        $tracking_pixel = '<img border=0 width=1 alt="" height=1 src="'.url('api/v1/mail-tracking/'.$hash.'/viewed').'" />';

        if (strpos($html,'</body>')) {
            $parts = explode("</body>", $html);
            return $parts[0].$tracking_pixel.'</body>'.$parts[1];
        } else {
            $html = $html . $tracking_pixel;
        }
       
        return $html;
    }

    public function trackingEmail($email,$subject, $content, $type,  $model, $model_id){
        $email = Email::create([
            "subject"=>$subject,
            "type"   =>$type,
            "email"  =>$email,
            "model"  =>$model,
            "model_id"=>$model_id
        ]);

        return $this->injectTrackingPixel($content, $email->id);

    }

	protected function sendEmail($email,$subject, $content, $tracking = false, $type = "", $model = "", $model_id = ""){
        Log::info("Entro en sendEmails");
        if($tracking){
            $content = $this->trackingEmail($email, $subject, $content, $type,  $model, $model_id);
        }
        //return $content;
		$url = "https://duvapi.tars.dev/arcade/mailerLite/sendIndividual";
		$token = env('TOKEN_DUVA');
		$header = array("Content-Type:application/json");
		$client = new Client([
			'headers' => ['Content-Type' => 'application/json',
			'Accept'=> 'application/json',
			'Authorization' => $token
			],   
        ]);
        $data = array(
            'email' => $email, 
            'subject' => $subject,
            'content' => $content,
            'from' => 'info@eventmovil.com',
            'fromName' => 'EVENT MOVIL'
        );
        try{
            $response = $client->post($url, [
                'json' => $data
            ]);
            Log::info("Se envio:");
        }catch(\Exception $e){
            report($e);
            Log::info("ERror correo:");
            Log::info($e);
            return false;
        }
        
        return true; 
	}

    protected function sendEmailMultiple($email,$subject, $content, $tracking = false, $type = "", $model = "", $model_id = ""){
        
        if($tracking){
            $content = $this->trackingEmail($email, $subject, $content, $type,  $model, $model_id);
        }
        //return $content;
		$url = "https://duvapi.tars.dev/brm/mailerLite/sendMasive";
		$token = env('TOKEN_DUVA_2');
		$header = array("Content-Type:application/json");
		$client = new Client([
			'headers' => ['Content-Type' => 'application/json',
			'Accept'=> 'application/json',
			'Authorization' => $token
			],   
        ]);

        $data = array(
            "name"=> "HEART ONLINE",
            "emails" => array('email' => $email), 
            'type' => 'regular',
            'language' => 'es',
            'subject' => $subject,
            'content' => $content,
            'from' => 'info@eventmovil.com',
            'fromName' => 'EVENT MOVIL',
        );
        
        try{
            $response = $client->post($url, [
                'json' => $data
            ]);
        }catch(\Exception $e){
            report($e);
            return false;
        }
        
        return true; 
	}

}
