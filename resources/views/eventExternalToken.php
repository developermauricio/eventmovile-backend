<?php
//incluir json web token
use Firebase\JWT\JWT;
//recibimos el token por get
if(isset($_GET['token'])){
    $jwt = $_GET['token'];
}else{
    echo '<body style="font-size:90%; text-align:center;">';
    echo "<h1 style='margin: 50px 50px 50px 50px;'>404 page not found</h1>";
    echo '</body>';
    exit;
}
//clave secreta
if(isset($_GET['keyToken'])){
    $key = $_GET['keyToken'];        
    $jwt = $_GET['token'];  
    //para decodificar la información 
    try{
        $data =(array)  JWT::decode($jwt, $key, array('HS256'));
        $timeBeginToken = $data['iat'];
        $timeExpireToken = $data['exp'];
        $dataUser = (array) $data['data'];
        $viewOk = true;
    }catch(Exception $e){
        $viewOk = false;        
    }        
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest info </title>
</head>
<body>
    <h1>token</h1>
    <p>
        <?php 
            if(isset($_GET['keyToken'])){
                echo $jwt; 
            }
        ?> 
    </p>
    <form action="" method="get">
        <label>Firma para evento 3D:</label>
        <input type="text" name="keyToken"/>
        <input type="hidden" value="<?php if(isset($_GET['token'])){ echo $jwt; } ?>" name="token">
        <button type="submit">Ingresar</button>
    </form>
    
    <h1>Datos </h1>
    <ul>
        <?php
        if(isset($_GET['keyToken'])){        
            if($viewOk){
                foreach($dataUser as $key => $value){
                ?>
                <li><?php echo $key.' : '.$value; ?></li>        
                <?php
                }
            }else{
                echo "<p style='color:red; background:yellow;'>ERROR EN LA FIRMA</p>";    
            }            
        }else {            
            echo "<p>Ingrese el secreto compartido</p>";
        }
            ?>
    </ul>
    <script>

    </script>
</body>
</html>