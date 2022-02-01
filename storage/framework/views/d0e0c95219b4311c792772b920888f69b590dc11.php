<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php if(empty($message_email->message_email_1)): ?>
  <title>Registro exitoso</title>
<?php endif; ?>
<?php if(isset($message_email->message_email_1)): ?>
  <title><?php echo e($message_email->message_email_1); ?></title>
<?php endif; ?>
</head>

<body itemscope itemtype="http://schema.org/EmailMessage" style="background-color: #f6f6f6;">

<table class="body-wrap" style="background-color: #f6f6f6; width: 100%;">
	<tr>
		<td class="container" width="480px" style="margin: auto">
			<div class="content">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" style="background-color:<?php echo e($event->style['email_color_background']); ?>;">
					<tr>
						<td class="alert alert-warning">
                            <img style="min-width: 150px; width: 15%;max-width: 250px; max-height:250px; display: block; margin: auto;" src="<?php echo e(env("IMAGE_URL").$event->style['email_img_logo']); ?>"/>
                            <!--<strong>Registro exitoso</strong>-->
						</td>
          </tr>
          <tr>
						<td class="content-block" >
              <div>
                  <table class="main" width="100%" cellpadding="0" cellspacing="0" >
                      <tr>
                          <td>
                            <?php if($tracking!=false): ?>
                              <img style="width: 100%;            max-width: 100%;"  src="<?php echo e(env("TRACKING_EMAIL").$event->style['email_img_banner']); ?>/<?php echo e($tracking); ?>"/>
                            <?php else: ?>
                              <img style="width: 100%; max-width: 100%;"  src="<?php echo e(env("IMAGE_URL").$event->style['email_img_banner']); ?>"/>
                            <?php endif; ?>
                              
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
                    <center><h2 style="font-size:24px; color:<?php echo e($event->style['email_titles_color']); ?>;"><?php echo e($event->name); ?></h2><br>
                    <p style="font-size:17px; color:<?php echo e($event->style['email_text_color']); ?>;"><?php echo e($message); ?></p>
									</td>
								</tr>
								<tr>
                  <td style="padding: 10px">
                    <table width="100%" style="padding: 10px">
                      <tr>
                        <?php if(empty($message_email->message_email_5)): ?>
                          <td style="color:<?php echo e($event->style['email_titles_color']); ?>;text-align: center;"><b>Inicia/</b></td>
                        <?php endif; ?>
                        <?php if(isset($message_email->message_email_5)): ?>
                          <td style="color:<?php echo e($event->style['email_titles_color']); ?>;text-align: center;"><b><?php echo e($message_email->message_email_5); ?></b></td>
                        <?php endif; ?>
                        <?php if(empty($message_email->message_email_6)): ?>
                          <td style="color:<?php echo e($event->style['email_titles_color']); ?>;text-align: center;"><b>Termina/</b></td>
                        <?php endif; ?>
                        <?php if(isset($message_email->message_email_6)): ?>
                          <td style="color:<?php echo e($event->style['email_titles_color']); ?>;text-align: center;"><b><?php echo e($message_email->message_email_6); ?></b></td>
                        <?php endif; ?>
                          <!-- <td style="color:<?php echo e($event->style['email_titles_color']); ?>;text-align: center;"><b>Inicia/</b></td>
                          <td style="color:<?php echo e($event->style['email_titles_color']); ?>;text-align: center;"><b>Termina/</b></td> -->
                        </tr>
                        <tr>
                            <td style="color:<?php echo e($event->style['email_text_color']); ?>; font-size:15px; text-align: center;"><?php echo e($event->start_date); ?></td>
                            <td style="color:<?php echo e($event->style['email_text_color']); ?> ;font-size:15px; text-align: center;"><?php echo e($event->end_date); ?></td>
                        </tr>
                        <tr>
                          <td colspan="2" style="text-align: center; color:<?php echo e($event->style['email_text_color']); ?>">
                            <!-- <span ><b>Virtual/</b></span> Presencial    -->
                            <?php if(empty($message_email->message_email_2)): ?>
                              <span ><b>Virtual/</b></span> Presencial
                            <?php endif; ?>
                            <?php if(isset($message_email->message_email_2)): ?>
                              <span><?php echo e($message_email->message_email_2); ?></span>
                            <?php endif; ?> 
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
                        <a href="<?php echo e(env("FRONT")); ?>#/Landing-Event?eventId=<?php echo e($event->id); ?>" class="btn-primary" style="color: <?php echo e($event->style['email_btn_text_color']); ?>;  background-color:<?php echo e($event->style['email_btn_color']); ?>; ">
                          <button style="background-color:<?php echo e($event->style['email_btn_color']); ?>; color:<?php echo e($event->style['email_btn_text_color']); ?>;padding: 10px 20px;
                            width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px; ">
                            <?php if(empty($message_email->message_email_3)): ?>
                              <h5>Ir al sitio del evento</h5>
                            <?php endif; ?> 
                            <?php if(isset($message_email->message_email_3)): ?>
                              <h5><?php echo e($message_email->message_email_3); ?></h5>
                            <?php endif; ?>
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
                    <?php if($qr != false): ?>
                      <img src="https://app.eventmovil.com/api/v1/storage/qr-code<?php echo e($qr); ?>.png" alt="qrcode" style=" max-height:400px; display: block; margin: auto; width: 50%; max-width:400px">
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%">
                      <tr>
                        <td class="content-block">
                            <center>
                              <?php if(empty($message_email->message_email_4)): ?>
                                <h4 style="color:<?php echo e($event->style['email_text_color']); ?>">Salas</h4>
                              <?php endif; ?>
                              <?php if(isset($message_email->message_email_4)): ?>
                                <h4 style="color:<?php echo e($event->style['email_text_color']); ?>"><?php echo e($message_email->message_email_4); ?></h4>
                              <?php endif; ?>
                            </center>
                        </td>
                      </tr>
                      <?php $__currentLoopData = $halls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hall): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <center>
                                <a href="<?php echo e(env("FRONT")); ?>#/login?eventId=<?php echo e($hall->event_id); ?>&hallId=<?php echo e($hall->id); ?>&typeHall=<?php echo e($hall->hall_type_id); ?>" class="btn-primary" style="color: <?php echo e($event->style['email_btn_text_color']); ?>;  background-color:<?php echo e($event->style['email_btn_color']); ?>; ">
                                  <button style="background-color:<?php echo e($event->style['email_btn_color']); ?>; color:<?php echo e($event->style['email_btn_text_color']); ?>;padding: 10px 20px;
                                    width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px; ">
                                    <?php echo e($hall->name); ?>

                                </a>
                            </center>
                        </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%">
                      <tr>
                          <td class="content-block">
                              <center>
                                <?php if(empty($message_email->message_email_7)): ?>
                                  <h4 style="color:<?php echo e($event->style['email_text_color']); ?>">Añadir a calendario</h4>
                                <?php endif; ?>
                                <?php if(isset($message_email->message_email_7)): ?>
                                  <h4 style="color:<?php echo e($event->style['email_text_color']); ?>"><?php echo e($message_email->message_email_7); ?></h4>
                                <?php endif; ?>
                              </center>
                          </td>
                      </tr>  
                    </table>
                  </td>
                </tr>
                <tr>
                  <td class="aligncenter content-block"><center><a href="<?php echo e($link_google); ?>" class="btn-primary width-80" style="width: 90%; background: #e94235; border: solid 2px#fff;  padding:10px 20px; color:#fff; text-decoration:none">Google</a>  |  <a href="<?php echo e($outlook); ?>" class="btn-primary width-80" style="width: 90%; background: #2172b9; border:solid 2px#fff; padding:10px 20px ;color:#fff; text-decoration:none">Outlook</a>  |  <a href="<?php echo e($ics); ?>" class="btn-primary width-80" style="width: 90%; background: #000; border:solid 2px#fff; padding:10px 20px; color:#fff; text-decoration:none">Apple</a></td></center></td>
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
<?php /**PATH /var/www/html/resources/views/events/confirmation.blade.php ENDPATH**/ ?>