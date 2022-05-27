<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventStyle extends Model
{
    protected $table = 'event_styles';

    protected $fillable = [
        'event_id', 'home_img_background', 'home_img_banner', 'home_img_logo', 'home_color_background', 
        'home_titles_color', 'home_text_color', 'home_titles_font', 'home_text_font', 'home_btn_color',
        'home_btn_color_hover', 'home_btn_text_color', 'home_btn_text_color_hover', 'home_footer_color',
        'home_div_first_color', 'home_div_second_color', 'section_img_background', 'section_color_background', 
        'section_titles_color', 'section_text_color', 'section_titles_font', 'section_text_font', 'section_btn_color',
        'section_btn_color_hover', 'section_btn_text_color', 'section_btn_text_color_hover', 'section_footer_color',
        'section_div_first_color', 'section_div_second_color', 'link_facebook', 'link_instagram', 'link_twitter',
        'section_banner_register', 'email_img_logo', 'email_img_banner', 'email_color_background', 'email_btn_color',
        'email_btn_text_color', 'email_text_color', 'email_titles_color', 'email_text_font', 'email_titles_font',
        'wa_banner_one','wa_banner_two', 'slider_logos'
    ];

    public function homeTitlesFont()
    {
        return $this->belongsTo('App\Font', 'home_titles_font', 'id');
    }

    public function homeTextFont()
    {
        return $this->belongsTo('App\Font', 'home_text_font', 'id');
    }

    public function emailTitlesFont()
    {
        return $this->belongsTo('App\Font', 'email_titles_font', 'id');
    }

    public function emailTextFont()
    {
        return $this->belongsTo('App\Font', 'email_text_font', 'id');
    }

    public function sectionTextFont()
    {
        return $this->belongsTo('App\Font', 'section_text_font', 'id');
    }

    public function sectionTitlesFont()
    {
        return $this->belongsTo('App\Font', 'section_titles_font', 'id');
    }

    public $timestamps = true;
}

