<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
@empty($message_email->message_email_1)
  <title>Registro exitoso</title>
@endempty
@isset($message_email->message_email_1)
  <title>{{$message_email->message_email_1}} Sisas</title>
@endisset
</head>

<body itemscope itemtype="http://schema.org/EmailMessage" style="background-color: #f6f6f6;">

<table class="body-wrap" style="background-color: #f6f6f6; width: 100%;">
	<tr>
		<td class="container" width="480px" style="margin: auto">
			<div class="content">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{$event->style['email_color_background']}};">
					<tr>
						<td class="alert alert-warning" align="center">
              <img style="min-width: 150px; width: 15%;max-width: 250px; max-height:250px; display: block; margin-top:30px; margin-bottom:30px;" src="{{env("IMAGE_URL").$event->style['email_img_logo']}}"/>
              <!--<strong>Registro exitoso</strong>-->
						</td>
          </tr>
          <tr>
						<td class="content-block" >
              <div>
                  <table class="main" width="100%" cellpadding="0" cellspacing="0" >
                      <tr>
                          <td>
                            @if($tracking!=false)
                              <!--<img style="width: 100%;            max-width: 100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}/{{$tracking}}"/>-->
                              <img style="width: 100%; max-width: 100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}"/>
                            @else
                              <img style="width: 100%; max-width: 100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}"/>
                            @endif
                              
                          </td>
                      </tr>
                  </table>
              </div>
						</td>
					</tr>
					<tr>
						<td class="content-wrap">
							<table width="100%" cellpadding="0" cellspacing="0" style="padding: 10px">
								<tr>
									<td class="content-block" style="padding: 10px">
                    <center><h2 style="font-size:24px; color:{{$event->style['email_titles_color']}};">{{$event->name}}</h2><br>
                    <p style="font-size:17px; color:{{$event->style['email_text_color']}};">{{$message}}</p>
									</td>
								</tr>
								<tr>
                  <td style="padding: 10px">
                    <table width="100%" style="padding: 10px">
                      <tr>
                        @empty($message_email->message_email_5)
                          <td style="color:{{$event->style['email_titles_color']}};text-align: center;"><b>Inicia/</b></td>
                        @endempty
                        @isset($message_email->message_email_5)
                          <td style="color:{{$event->style['email_titles_color']}};text-align: center;"><b>{{$message_email->message_email_5}}</b></td>
                        @endisset
                        @empty($message_email->message_email_6)
                          <td style="color:{{$event->style['email_titles_color']}};text-align: center;"><b>Termina/</b></td>
                        @endempty
                        @isset($message_email->message_email_6)
                          <td style="color:{{$event->style['email_titles_color']}};text-align: center;"><b>{{$message_email->message_email_6}}</b></td>
                        @endisset
                        </tr>
                        <tr>
                            <td style="color:{{$event->style['email_text_color']}}; font-size:15px; text-align: center;">{{$event->start_date}}</td>
                            <td style="color:{{$event->style['email_text_color']}} ;font-size:15px; text-align: center;">{{$event->end_date}}</td>
                        </tr>
                        <tr>
                          <td colspan="2" style="text-align: center; color:{{$event->style['email_text_color']}}">
                            
                            @empty($message_email->message_email_2)
                              
                            @endempty
                            @isset($message_email->message_email_2)
                              <!--<span>{{$message_email->message_email_2}}</span>-->
                            @endisset 
                          </td> 
                        </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%"> 
                        <tr>
                            <td class="aligncenter content-block"><center><div style="height: 10px;"></div></td>  
                        </tr>
                    </table>
                  </td>
                </tr>
								<tr>
                  <table width="100%">
                    <tr>
                      <td class="content-block" >
                        <center>
                        <a href="{{env("FRONT")}}#/Landing-Event?eventId={{$event->id}}" class="btn-primary" style="color: {{$event->style['email_btn_text_color']}};  background-color:{{$event->style['email_btn_color']}}; ">
                          <button style="background-color:{{$event->style['email_btn_color']}}; color:{{$event->style['email_btn_text_color']}};padding: 10px 20px;
                            width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px; ">
                            @empty($message_email->message_email_3)
                              <h5>Ir al sitio del evento</h5>
                            @endempty 
                            @isset($message_email->message_email_3)
                              <h5>{{$message_email->message_email_3}}</h5>
                            @endisset
                          </button></a>
                        </center>
                      </td>
                    </tr>
                  </table>
								</tr>
                <tr>
                  <td>
                    <table width="100%"> 
                        <tr>
                            <td class="aligncenter content-block"><center><div style="height: 10px;"></div></td>  
                        </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td class="content-block">
                    @if($qr != false)
                      <img src="https://app.eventmovil.com/api/v1/storage/qr-code{{$qr}}.png" alt="qrcode" style=" max-height:400px; display: block; margin: auto; width: 50%; max-width:400px">
                    @endif
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%">
                      <tr>
                        <td class="content-block">
                            <center>
                              @empty($message_email->message_email_4)
                                <h4 style="color:{{$event->style['email_text_color']}}">Salas</h4>
                              @endempty
                              @isset($message_email->message_email_4)
                                <h4 style="color:{{$event->style['email_text_color']}}">{{$message_email->message_email_4}}</h4>
                              @endisset
                            </center>
                        </td>
                      </tr>
                      @foreach($halls as $hall)
                        <tr>
                            <center>
                                <a href="{{env("FRONT")}}#/login?eventId={{$hall->event_id}}&hallId={{$hall->id}}&typeHall={{$hall->hall_type_id}}" class="btn-primary" style="color: {{$event->style['email_btn_text_color']}};  background-color:{{$event->style['email_btn_color']}}; ">
                                  <button style="background-color:{{$event->style['email_btn_color']}}; color:{{$event->style['email_btn_text_color']}};padding: 10px 20px;
                                    width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px; ">
                                    {{$hall->name}}
                                </a>
                            </center>
                        </tr>
                      @endforeach
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%">
                      <tr>
                          <td class="content-block">
                              <center>
                                @empty($message_email->message_email_7)
                                  <h4 style="color:{{$event->style['email_text_color']}}">Añadir a calendario</h4>
                                @endempty
                                @isset($message_email->message_email_7)
                                  <h4 style="color:{{$event->style['email_text_color']}}">{{$message_email->message_email_7}}</h4>
                                @endisset
                              </center>
                          </td>
                      </tr>  
                    </table>
                  </td>
                </tr>
                <tr>
                  <td class="aligncenter content-block">
                    <center>
                      <a href="{{$outlook_365}}" class="btn-primary width-80" style="width: 90%; background: #2172b9; border:solid 2px#fff; padding:10px 20px ;color:#fff; text-decoration:none">
                        <b>Office 365</b>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 50 50" width="15px" height="15px">    
                          <path d="M44.257,5.333l-12.412-3.3c-0.192-0.051-0.396-0.044-0.582,0.021l-25.588,8.8C5.271,10.993,5,11.373,5,11.8V36v1.2v1.065 v0.01c0,0.363,0.286,0.737,0.675,0.871l25.588,8.8C31.368,47.981,31.478,48,31.588,48c0.086,0,0.173-0.011,0.257-0.033l12.412-3.3 C44.695,44.55,45,44.153,45,43.7V6.3C45,5.847,44.695,5.45,44.257,5.333z M30,10.827v29.532L8.153,37.476l7.191-2.637 C15.738,34.695,16,34.32,16,33.9V13.715L30,10.827z"/>
                        </svg>                        
                      </a>| 
                      <a href="{{$link_google}}" class="btn-primary width-80" style="width: 90%; background: #e94235; border: solid 2px#fff;  padding:10px 20px; color:#fff; text-decoration:none">
                        <b>Google</b>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 50 50" width="15px" height="15px">
                          <path d="M 5.5 7 C 3.019531 7 1 9.019531 1 11.5 L 1 11.925781 L 25 29 L 49 11.925781 L 49 11.5 C 49 9.019531 46.980469 7 44.5 7 Z M 6.351563 9 L 43.644531 9 L 25 22 Z M 1 14.027344 L 1 38.5 C 1 40.980469 3.019531 43 5.5 43 L 44.5 43 C 46.980469 43 49 40.980469 49 38.5 L 49 14.027344 L 43 18.296875 L 43 41 L 7 41 L 7 18.296875 Z"/>
                        </svg>                       
                      </a>|
                      <a href="{{$outlook}}" class="btn-primary width-80" style="width: 90%; background: #2172b9; border:solid 2px#fff; padding:10px 20px ;color:#fff; text-decoration:none">
                        <b>Outlook.com</b>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 48 48" width="15px" height="15px">
                          <path d="M 21.541016 4.0957031 C 21.277323 4.0910623 21.009427 4.1178181 20.740234 4.1777344 L 20.740234 4.1757812 L 7.5234375 7.1132812 C 5.4728994 7.5691225 4 9.4069339 4 11.507812 L 4 36.492188 C 4 38.593573 5.4735774 40.431028 7.5234375 40.886719 L 20.740234 43.824219 L 20.740234 43.822266 C 22.893773 44.301643 25 42.611755 25 40.40625 L 25 7.59375 C 25 5.663933 23.386864 4.1281887 21.541016 4.0957031 z M 28 11.050781 L 28 22.189453 L 31.509766 24.669922 L 44.990234 15.369141 C 44.900234 12.969141 42.92 11.050781 40.5 11.050781 L 28 11.050781 z M 14.5 16.5 C 17.584 16.5 20 20.014 20 24.5 C 20 28.986 17.584 32.5 14.5 32.5 C 11.416 32.5 9 28.986 9 24.5 C 9 20.014 11.416 16.5 14.5 16.5 z M 45 19.009766 L 32.349609 27.730469 C 32.099609 27.910469 31.8 28 31.5 28 C 31.2 28 30.890859 27.910703 30.630859 27.720703 L 28 25.859375 L 28 38.050781 L 40.5 38.050781 C 42.98 38.050781 45 36.030781 45 33.550781 L 45 19.009766 z M 14.5 19.5 C 13.32 19.5 12 21.638 12 24.5 C 12 27.362 13.32 29.5 14.5 29.5 C 15.68 29.5 17 27.362 17 24.5 C 17 21.638 15.68 19.5 14.5 19.5 z"/>
                        </svg>                        
                      </a>|                         
                      <a href="{{$ics}}" class="btn-primary width-80" style="width: 90%; background: #ccc0c0; border:solid 2px#fff; padding:10px 20px; color:#fff; text-decoration:none">
                        <b>Apple</b>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="1 0 30 30" width="23px" height="23px">    
                          <path d="M24,4H6C4.895,4,4,4.895,4,6v18c0,1.105,0.895,2,2,2h18c1.105,0,2-0.895,2-2V6C26,4.895,25.105,4,24,4z M15.743,7.751 c0.563-0.661,1.453-1.11,2.22-1.11c0.049,0.857-0.261,1.698-0.816,2.31c-0.498,0.661-1.355,1.159-2.147,1.061 C14.829,9.155,15.302,8.298,15.743,7.751z M19.963,19.538c-0.596,0.907-1.257,1.821-2.261,1.821c-0.955,0-1.298-0.604-2.4-0.604 c-1.184,0-1.518,0.604-2.424,0.604c-1.004,0-1.714-0.963-2.343-1.861c-0.816-1.175-1.51-3.02-1.534-4.792 c-0.017-0.939,0.163-1.861,0.62-2.645c0.645-1.094,1.796-1.837,3.053-1.861c0.963-0.032,1.82,0.661,2.408,0.661 c0.563,0,1.616-0.661,2.808-0.661c0.514,0.001,1.886,0.155,2.735,1.461C20.559,11.702,19,12.567,19,14.486 c0.073,2.188,1.967,2.955,2,2.955C20.968,17.481,20.714,18.485,19.963,19.538z"/>
                        </svg>                        
                      </a>
                    </center>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%"> 
                        <tr>
                            <td class="aligncenter content-block"><center><div style="height: 10px;"></div></td>  
                        </tr>
                    </table>
                  </td>
                </tr>
							</table>
						</td> 
					</tr>
        </table>
        <div class="creditos">
          <table width="100%">
              <tr>
                  <td class="content-block">
                  </td>
              </tr>   
          </table>
				</div>
      </div>
		</td>
	
	</tr>
</table>

</body>
</html>

