<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Registro exitoso</title>
</head>

<body itemscope itemtype="http://schema.org/EmailMessage" style="background-color: #f6f6f6;">

<table class="body-wrap" style="background-color: #f6f6f6; width: 100%;">
	<tr>
		<td class="container" width="480px" style="margin: auto">
			<div class="content">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff;">
          <tr>
						<td class="content-block" >
              <div>
                  <table class="main" width="100%" cellpadding="0" cellspacing="0" >
                      <tr>
                          <td>
                            
                              <img style="width: 100%; max-width: 100%;"  src="{{env("IMAGE_URL").$bussines->pic}}"/>
                       
                              
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
                    <center><h2 style="font-size:24px; color:#000">{{$bussines->name}}</h2><br>
                    <p style="font-size:17px; color:#000">{{$bussines->message_email}}</p>
									</td>
								</tr>
								<tr>
                  <td style="padding: 10px">
                    <table width="100%" style="padding: 10px">
                      <tr>
                          <td style="color:color:#000;text-align: center;"><b>Inicia/</b></td>
                          <td style="color:color:#000;text-align: center;"><b>Termina/</b></td>
                        </tr>
                        <tr>
                            <td style="color:color:#000; font-size:15px; text-align: center;">{{$bussines->start_date}}</td>
                            <td style="color:color:#000;font-size:15px; text-align: center;">{{$bussines->end_date}}</td>
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
									<td class="content-block">
                    <center>
                    <a href="{{env("FRONT")}}#/landing-business-market/{{$bussines->id}}" class="btn-primary" style="color: #fff;  background-color:#71047a; ">
                      <button style="background-color:#71047a; color:#fff;padding: 10px 20px;
                        min-width:200px; width: 30%; max-width: 300px; min-height: 40px; border:none; border-radius: 5px; ">Ir al sitio</button></a>
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
                
                <tr>
                  <td>
                    <table width="100%">
                      <tr>
                          <td class="content-block">
                              <center>
                                  <h4 style="color:#000">Añadir a calendario</h4>
                              </center>
                          </td>
                      </tr>  
                    </table>
                  </td>
                </tr>
                <tr>
                  <td class="aligncenter content-block"><center><a href="{{$link_google}}" class="btn-primary width-80" style="width: 90%; background: #e94235; border: solid 2px#fff;  padding:10px 20px; color:#fff; text-decoration:none">Google</a>  |  <a href="{{$outlook}}" class="btn-primary width-80" style="width: 90%; background: #2172b9; border:solid 2px#fff; padding:10px 20px ;color:#fff; text-decoration:none">Outlook</a>  |  <a href="{{$ics}}" class="btn-primary width-80" style="width: 90%; background: #000; border:solid 2px#fff; padding:10px 20px; color:#fff; text-decoration:none">Apple</a></td></center></td>
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
