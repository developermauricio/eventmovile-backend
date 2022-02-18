<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','model_id', 'phone', 'pic', 'nit', 'company_id', 'position','lastname', 'uid',
        'actived', 'job_title','image_on_register','user_type', 'online'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','created_at','updated_at','email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function meetings($acceptance = 0)
    {
        return $this->belongsToMany('App\Meeting','meeting_rel_users','user_id','meeting_id')->wherePivot('acceptance',$acceptance)->withPivot('user_id','id','meeting_id');
    }

    public function bm($business_id)
    {
        return $this->belongsToMany('App\BusinessMarket','business_markets_rel_users','user_id','business_id')->wherePivot('business_id',$business_id)->withPivot('user_id','answer_feedback','business_id');
    }

    public function tokens($status = 1)
    {
        return $this->belongsToMany('App\Meeting','meeting_rel_users','user_id','meeting_id')->wherePivot('acceptance',$acceptance)->withPivot('user_id','id','meeting_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function requestSent()
    {
        return $this->hasOne(NetworkingWebApp::class, 'guest_id');
    }

    public function requestReceived()
    {
        return $this->hasOne(NetworkingWebApp::class, 'creator_id');
    }

    public function eventUsers(){
        return $this->hasMany(EventUser::class, 'user_id');
    }

    public function galleryLike(){
        return $this->belongsToMany(GalleryLikeWebApp::class, 'gallery_like_web_apps', 'gallery_id', 'user_id');
    }
}
