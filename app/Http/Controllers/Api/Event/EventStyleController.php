<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\EventStyle;

class EventStyleController extends Controller
{
    public function show($id){
     
        $eventStyle = EventStyle::where('event_id', $id)
            ->with('homeTitlesFont')
            ->with('homeTextFont')
            ->with('sectionTitlesFont')
            ->with('sectionTextFont')
            ->first();

        return $this->showOne($eventStyle, 201);

    }

    public function store(Request $request){
        
        $rules = ['event_id' => 'required|exists:events,id'];
        $this->validate($request, $rules);
        if(isset($request->home_img_logo)){
            $logoHome = $this->saveFile($request->home_img_logo, 'logo', $request->event_id);                
        }else{
            $logoHome = "uploads/250x250.png";
        }
        if(isset($request->home_img_banner)){
            $bannerHome = $this->saveFile($request->home_img_banner, 'banner', $request->event_id);
        }else{
            $bannerHome = "uploads/512x382.png";
        }    
        if(isset($request->home_img_background)){
            $backgroundHome = $this->saveFile($request->home_img_background, 'background', $request->event_id);
        }else{
            $backgroundHome = "uploads/512x382.png";
        }
        if(isset($request->section_img_background)){
            $backgroundSection = $this->saveFile($request->section_img_background, 'backgroundSection', $request->event_id);
        }else{
            $backgroundSection = "uploads/512x382.png";
        }
        if(isset($request->section_banner_register)){
            $bannerRegister = $this->saveFile($request->section_banner_register, 'bannerRegister', $request->event_id);
        }else{
            $bannerRegister = "uploads/512x382.png";
        }
        if(isset($request->email_img_logo)){
            $logoEmail = $this->saveFile($request->email_img_logo, 'logoEmail', $request->event_id);        
        }else{
            $logoEmail = "uploads/250x250.png";
        }
        if(isset($request->email_img_banner)){
            $bannerEmail = $this->saveFile($request->email_img_banner, 'bannerEmail', $request->event_id);
        }else{
            $bannerEmail = "uploads/512x382.png";
        }
        //arreglo problema login landing
        if(isset($request->home_btn_color)){
            $home_btn_color_fix = $request->home_btn_color;
        }else{
            $home_btn_color_fix = "#000000";
        }
        //arreglo enviar invitaciones 
        if(isset($request->email_btn_color)){
            $email_btn_color_fix = $request->email_btn_color;
        }else{
            $email_btn_color_fix = "#000000";
        }
        if(isset($request->email_btn_text_color)){
            $email_btn_text_color_fix = $request->email_btn_text_color;
        }else{
            $email_btn_text_color_fix = "#fff";
        }        

        $eventStyle = EventStyle::create([
            'event_id' => $request->event_id, 
            'home_img_background'=> $backgroundHome,
            'home_img_banner'=> $bannerHome,
            'home_img_logo' => $logoHome, 
            'home_color_background' => $request->home_color_background, 
            'home_titles_color'=> $request->home_titles_color, 
            'home_text_color'=> $request->home_text_color,
            'home_titles_font'=> $request->home_titles_font,
            'home_text_font'=> $request->home_text_font,
            'home_btn_color'=> $home_btn_color_fix,
            'home_btn_color_hover'=> $request->home_btn_color_hover,
            'home_btn_text_color'=> $request->home_btn_text_color,
            'home_btn_text_color_hover'=> $request->home_btn_text_color_hover,
            'home_footer_color'=> $request->home_footer_color,
            'home_div_first_color'=> $request->home_div_first_color, 
            'home_div_second_color'=> $request->home_div_second_color,
            'section_img_background'=> $backgroundSection,
            'section_color_background'=> $request->section_color_background, 
            'section_titles_color'=> $request->section_titles_color, 
            'section_text_color'=> $request->section_text_color, 
            'section_titles_font'=> $request->section_titles_font, 
            'section_text_font'=> $request->section_text_font, 
            'section_btn_color'=> $request->section_btn_color,
            'section_btn_color_hover'=> $request->section_btn_color_hover, 
            'section_btn_text_color'=> $request->section_btn_text_color, 
            'section_btn_text_color_hover'=> $request->section_btn_text_color_hover, 
            'section_footer_color'=> $request->section_footer_color,
            'section_div_first_color'=> $request->section_div_first_color, 
            'section_div_second_color'=> $request->section_div_second_color,
            'link_facebook'=> $request->link_facebook,
            'link_instagram'=> $request->link_instagram, 
            'link_twitter'=> $request->link_twitter,
            'section_banner_register' => $bannerRegister,
            'email_img_logo' => $logoEmail, 
            'email_img_banner' => $bannerEmail, 
            'email_color_background' => $request->email_color_background, 
            'email_btn_color' => $email_btn_color_fix,
            'email_btn_text_color' => $email_btn_text_color_fix, 
            'email_text_color' => $request->email_text_color, 
            'email_titles_color' => $request->email_titles_color, 
            'email_text_font' => $request->email_text_font, 
            'email_titles_font' => $request->email_titles_font
        ]);

        return $this->successResponse(['data'=> $eventStyle, 'message'=>'Event style created'], 201);
    }

    public function update(Request $request, EventStyle $eventStyle)
    {        
        
        $rules = ['event_id' => 'required|exists:events,id'];
        $this->validate($request, $rules);

        $logoHome = $request->home_img_logo;
        $bannerHome =$request->home_img_banner;
        $bgHome =$request->home_img_background;
        $bgSection =$request->section_img_background;
        $bannerRegister = $request->section_banner_register;
        $logoEmail = $request->email_img_logo;
        $bannerEmail =$request->email_img_banner;

        if(is_file($request->home_img_logo)){
            $file = $request->home_img_logo;
            $nameFile = $file->getClientOriginalName();
            $logoHome = $nameFile;
        }
        
        if($eventStyle->home_img_logo <> "" && $eventStyle->home_img_logo != $logoHome){
            $request->home_img_logo = $this->saveFile($request->home_img_logo, 'logo', $request->event_id);
        }
        
        if(is_file($request->home_img_banner)){
            $file = $request->home_img_banner;
            $nameFile = $file->getClientOriginalName();
            $bannerHome = $nameFile;
        }
        if($eventStyle->home_img_banner <> "" && $eventStyle->home_img_banner != $bannerHome){
            $request->home_img_banner = $this->saveFile($request->home_img_banner, 'banner', $request->event_id);
        }
        if(is_file($request->home_img_background)){
            $file = $request->home_img_background;
            $nameFile = $file->getClientOriginalName();
            $bgHome = $nameFile;
        }
        if($eventStyle->home_img_background <> "" && $eventStyle->home_img_background != $bgHome){
            $request->home_img_background = $this->saveFile($request->home_img_background, 'background', $request->event_id);
        }
        if(is_file($request->section_img_background)){
            $file = $request->section_img_background;
            $nameFile = $file->getClientOriginalName();
            $bgSection = $nameFile;
        }
        if($eventStyle->section_img_background <> "" && $eventStyle->section_img_background != $bgSection){
            $request->section_img_background = $this->saveFile($request->section_img_background, 'backgroundSection', $request->event_id);
        }

        if(is_file($request->section_banner_register)){
            $file = $request->section_banner_register;
            $nameFile = $file->getClientOriginalName();
            $bannerRegister = $nameFile;
        }
        if($eventStyle->section_banner_register <> "" && $eventStyle->section_banner_register != $bannerRegister){
            $request->section_banner_register = $this->saveFile($request->section_banner_register, 'bannerRegister', $request->event_id);
        }

        if(is_file($request->email_img_logo)){
            $file = $request->email_img_logo;
            $nameFile = $file->getClientOriginalName();
            $logoEmail = $nameFile;
        }
        
        if($eventStyle->email_img_logo <> "" && $eventStyle->email_img_logo != $logoEmail){
            $request->email_img_logo = $this->saveFile($request->email_img_logo, 'logoEmail', $request->event_id);
        }

        if(is_file($request->email_img_banner)){
            $file = $request->email_img_banner;
            $nameFile = $file->getClientOriginalName();
            $bannerEmail = $nameFile;
        }
        
        if($eventStyle->email_img_banner <> "" && $eventStyle->email_img_banner != $bannerEmail){
            $request->email_img_banner = $this->saveFile($request->email_img_banner, 'bannerEmail', $request->event_id);
        }
        //arreglo enviar invitaciones 
        if(isset($request->email_btn_color)){
            $email_btn_color_fix = $request->email_btn_color;
        }else{
            $email_btn_color_fix = "#000000";
        }
        if(isset($request->email_btn_text_color)){
            $email_btn_text_color_fix = $request->email_btn_text_color;
        }else{
            $email_btn_text_color_fix = "#fff";
        }       
        
        $eventStyle->event_id= $request->event_id; 
        $eventStyle->home_img_background= $request->home_img_background; 
        $eventStyle->home_img_banner=$request->home_img_banner; 
        $eventStyle->home_img_logo=$request->home_img_logo; 
        $eventStyle->home_color_background= $request->home_color_background; 
        $eventStyle->home_titles_color= $request->home_titles_color; 
        $eventStyle->home_text_color= $request->home_text_color;
        $eventStyle->home_titles_font= $request->home_titles_font;
        $eventStyle->home_text_font= $request->home_text_font;
        $eventStyle->home_btn_color= $request->home_btn_color;
        $eventStyle->home_btn_color_hover= $request->home_btn_color_hover;
        $eventStyle->home_btn_text_color= $request->home_btn_text_color;
        $eventStyle->home_btn_text_color_hover= $request->home_btn_text_color_hover;
        $eventStyle->home_footer_color= $request->home_footer_color;
        $eventStyle->home_div_first_color= $request->home_div_first_color; 
        $eventStyle->home_div_second_color= $request->home_div_second_color;
        $eventStyle->section_img_background= $request->section_img_background; 
        $eventStyle->section_color_background= $request->section_color_background; 
        $eventStyle->section_titles_color= $request->section_titles_color; 
        $eventStyle->section_text_color= $request->section_text_color; 
        $eventStyle->section_titles_font= $request->section_titles_font; 
        $eventStyle->section_text_font= $request->section_text_font; 
        $eventStyle->section_btn_color= $request->section_btn_color;
        $eventStyle->section_btn_color_hover= $request->section_btn_color_hover; 
        $eventStyle->section_btn_text_color= $request->section_btn_text_color; 
        $eventStyle->section_btn_text_color_hover= $request->section_btn_text_color_hover; 
        $eventStyle->section_footer_color= $request->section_footer_color;
        $eventStyle->section_div_first_color= $request->section_div_first_color; 
        $eventStyle->section_div_second_color= $request->section_div_second_color;
        $eventStyle->link_facebook= $request->link_facebook;
        $eventStyle->link_instagram= $request->link_instagram;
        $eventStyle->link_twitter= $request->link_twitter;
        $eventStyle->section_banner_register= $request->section_banner_register;
        $eventStyle->email_img_logo = $request->email_img_logo; 
        $eventStyle->email_img_banner = $request->email_img_banner; 
        $eventStyle->email_color_background = $request->email_color_background; 
        $eventStyle->email_btn_color = $email_btn_color_fix;
        $eventStyle->email_btn_text_color = $email_btn_text_color_fix; 
        $eventStyle->email_text_color = $request->email_text_color; 
        $eventStyle->email_titles_color = $request->email_titles_color; 
        $eventStyle->email_text_font = $request->email_text_font; 
        $eventStyle->email_titles_font = $request->email_titles_font; 
        
        $eventStyle->save();
        
        

        return $this->successResponse(['data' => $eventStyle, 'message' => 'Event style updated'],201);
    }
    public function saveFile($pic, $type, $name)
    {
        
        $file = $pic;
        try {
        # Storage::disk('local')->put($nameFile,  \File::get($file));
            # Storage::disk('digitalocean')->put($nameFile, \File::get($file));
            $path = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');
            return $path;
        } catch (Exception $e) {
            return ' Error al subir el archivo '.$file;
        }

    }
    public function saveFileAntes($pic, $type, $name){

        if(!$pic){
            return null;
        }
        if(!is_file($pic))
            return null;
            
        $file = $pic;
    
        $nameFile = $file->getClientOriginalName();

        $number = EventStyle::count();

        $explode = explode(".", $nameFile);
        $nameFile ="event_".$type."_".$name."_".$explode[0].$number.".".$explode[1];
        $nameFile = str_replace(' ', '', $nameFile);
        
        Storage::disk('local')->put($nameFile,  \File::get($file));
       
        return $nameFile;
    }
}
