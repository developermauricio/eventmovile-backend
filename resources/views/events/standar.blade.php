<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Important Data</title>
</head>

<body itemscope itemtype="http://schema.org/EmailMessage" style="background-color: #f6f6f6;">

<table class="body-wrap" style="background-color: #f6f6f6; width: 100%;">
	<tr>
		<td class="container" width="480px" style="margin: auto">
			<div class="content">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{$event->style['email_color_background']}};">
					<tr>
						<td class="alert alert-warning">
                            <img style="min-width: 150px; width: 15%;max-width: 250px; max-height:250px; display: block; margin: auto;" src="{{env("IMAGE_URL").$event->style['email_img_logo']}}"/>
                            <!--<strong>Registro exitoso</strong>-->
						</td>
          </tr>
          <tr>
						<td class="content-block" >
              <div>
                  <table class="main" width="100%" cellpadding="0" cellspacing="0" >
                      <tr>
                          <td>
                            
                              <img style="width: 100%; max-width: 100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}"/>
                         
                              
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
                    <div style="font-size:17px; color:{{$event->style['email_text_color']}};">{!!$message!!}</div></center>
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
                    <td class="content-block">
                        <!--<center>
                        <a href="{{env("FRONT")}}#/Landing-Event?eventId={{$event->id}}" class="btn-primary" style="color: {{$event->style['email_btn_text_color']}};  background-color:{{$event->style['email_btn_color']}}; ">
                            <button style="background-color:{{$event->style['email_btn_color']}}; color:{{$event->style['email_btn_text_color']}};padding: 10px 20px;
                            width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px; ">Ir al sitio del evento</button></a>
                        </center>-->

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
