<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Registro exitoso</title>
<style>
    /* -------------------------------------
    GLOBAL
    A very basic CSS reset
------------------------------------- */
* {
  margin: 0;
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  box-sizing: border-box;
  font-size: 14px;
}

img {
  width: 100%;
  max-width: 100%;
}

body {
  -webkit-font-smoothing: antialiased;
  -webkit-text-size-adjust: none;
  width: 100% !important;
  height: 100%;
  line-height: 1.6em;
  /* 1.6em * 14px = 22.4px, use px to get airier line-height also in Thunderbird, and Yahoo!, Outlook.com, AOL webmail clients */
  /*line-height: 22px;*/
}


table td {
  vertical-align: top;
}

/* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
body {
  background-color: #f6f6f6;
}

.body-wrap {
  background-color: #f6f6f6;
  width: 100%;
}

.container {
  display: block !important;
  max-width: 600px !important;
  margin: 0 auto !important;
  /* makes it centered */
  clear: both !important;
}

.content {
  max-width: 600px;
  margin: 0 auto;
  display: block;
  padding: 20px;
}

/* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
.main {
  background-color: #fff;
  border: 1px solid #e9e9e9;
  border-radius: 3px;
}

.content-wrap {
  padding: 20px;
}

.content-block {
  padding: 0 0 20px;
}

.header {
  width: 100%;
  margin-bottom: 20px;
}


.footer {
  width: 100%;
  clear: both;
  color: #999;
  padding: 20px;
}
.footer p, .footer a, .footer td {
  color: #999;
  font-size: 12px;
}

/* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
h1, h2, h3 {
  font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  color: #000;
  margin: 40px 0 0;
  line-height: 1.2em;
  font-weight: 400;
}

h1 {
  font-size: 32px;
  font-weight: 500;
  /* 1.2em * 32px = 38.4px, use px to get airier line-height also in Thunderbird, and Yahoo!, Outlook.com, AOL webmail clients */
  /*line-height: 38px;*/
}

h2 {
  font-size: 24px;
  /* 1.2em * 24px = 28.8px, use px to get airier line-height also in Thunderbird, and Yahoo!, Outlook.com, AOL webmail clients */
  /*line-height: 29px;*/
}

h3 {
  font-size: 18px;
  /* 1.2em * 18px = 21.6px, use px to get airier line-height also in Thunderbird, and Yahoo!, Outlook.com, AOL webmail clients */
  /*line-height: 22px;*/
}

h4 {
  font-size: 14px;
  font-weight: 600;
}

p, ul, ol {
  margin-bottom: 10px;
  font-weight: normal;
}
p li, ul li, ol li {
  margin-left: 5px;
  list-style-position: inside;
}

/* -------------------------------------
    LINKS & BUTTONS
------------------------------------- */
a {
  color: #348eda;
  text-decoration: underline;
}

.btn-primary {
  text-decoration: none;
  color: #FFF;
  background-color: #68B90F;
  border: solid #68B90F;
  border-width: 10px 20px;
  line-height: 2em;
  /* 2em * 14px = 28px, use px to get airier line-height also in Thunderbird, and Yahoo!, Outlook.com, AOL webmail clients */
  /*line-height: 28px;*/
  font-weight: bold;
  text-align: center;
  cursor: pointer;
  display: inline-block;
  border-radius: 5px;
  text-transform: capitalize;
}

/* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
.last {
  margin-bottom: 0;
}

.first {
  margin-top: 0;
}

.aligncenter {
  text-align: center;
}

.alignright {
  text-align: right;
}

.alignleft {
  text-align: left;
}

.clear {
  clear: both;
}

/* -------------------------------------
    ALERTS
    Change the class depending on warning email, good email or bad email
------------------------------------- */
.alert {
  font-size: 16px;
  color: #fff;
  font-weight: 500;
  padding: 20px;
  text-align: center;
  border-radius: 3px 3px 0 0;
}
.alert a {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  font-size: 16px;
}

.alert.alert-bad {
  background-color: #D0021B;
}
.alert.alert-good {
  background-color: #68B90F;
}

/* -------------------------------------
    INVOICE
    Styles for the billing table
------------------------------------- */
.invoice {
  margin: 40px auto;
  text-align: left;
  width: 80%;
}
.invoice td {
  padding: 5px 0;
}
.invoice .invoice-items {
  width: 100%;
}
.invoice .invoice-items td {
  border-top: #eee 1px solid;
}
.invoice .invoice-items .total td {
  border-top: 2px solid #333;
  border-bottom: 2px solid #333;
  font-weight: 700;
}

/* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }

  h1, h2, h3, h4 {
    font-weight: 800 !important;
    margin: 20px 0 5px !important;
  }

  h1 {
    font-size: 22px !important;
  }

  h2 {
    font-size: 18px !important;
  }

  h3 {
    font-size: 16px !important;
  }

  .container {
    padding: 0 !important;
    width: 100% !important;
  }

  .content {
    padding: 0 !important;
  }

  .content-wrap {
    padding: 10px !important;
  }

  .invoice {
    width: 100% !important;
  }
  .logo-email{
    margin-top: 3%;
    width: 15%;
    height: 15%;
    margin-left: 45%;  
  }
}

/*# sourceMappingURL=styles.css.map */
</style>
</head>

<body itemscope itemtype="http://schema.org/EmailMessage">

<div style="background-color:{{$event->style['email_color_background']}}; width:100%; max-width:480px; border-radius:10px;">
  <img style="width: 15%; height: 15%; margin-left: 42%; margin-top: 5%; margin-bottom: 5%;" src="{{env("IMAGE_URL").$event->style['email_img_logo']}}"/>
  @if($tracking!=false)
    <!--<img style="width:100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}/{{$tracking}}"/>-->
    <img style="width:100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}"/>
  @else
    <img style="width:100%;"  src="{{env("IMAGE_URL").$event->style['email_img_banner']}}"/>
  @endif
  <h2 style="color:{{$event->style['email_titles_color']}}; text-align: center; font-size: 30px; padding-top: 2%; padding-bottom: 2%;">{{$event->name}}</h2>

  <p style="color:{{$event->style['email_text_color']}}; margin: 5%; padding-bottom:1%; font-size:15px; text-align: center; margin-left: 10px; margin-right:10px;">{{$message}}</p><br>
  <table style="margin:5%;padding-bottom: 3%;font-size:15px; width:90%; text-align:center;">
    <tbody>
      <tr>
        <td style="color:{{$event->style['email_titles_color']}}"><b>Inicia/</b></td>
        <td style="color:{{$event->style['email_titles_color']}}"><b>Termina/</b></td>
      </tr>
      <tr>
          <td style="color:{{$event->style['email_text_color']}}; font-size:15px;">{{$event->start_date}}</td>
          <td style="color:{{$event->style['email_text_color']}}">{{$event->end_date}}</td>
      </tr>
      <tr>
          
      </tr>
    </tbody>
  </table>
  <a target="_blank"  href="{{env("FRONT")}}#/Landing-Event?eventId={{$event->id}}">
  <button style="margin-left: 35%; background-color:{{$event->style['email_btn_color']}}; color:{{$event->style['email_btn_text_color']}};margin-bottom: 10%;
  width: 30%; height: 40px; border:none; border-radius: 5px; border-color: #9e3dff;">Ir al sitio del evento</button><a>
  @if($qr != false)
  
  <img src="https://app.eventmovil.com/api/v1/storage/qr-code{{$qr}}.png" alt="qrcode" style="height: 50%; margin-left: 25%; margin-bottom: 5%; width: 50%;">
  
  @endif
</div>

</body>
</html>
