<!-- Author: Alex Toasa -->

<?php
        require_once "vendor/autoload.php";
        use PHPMailer\PHPMailer\PHPMailer;

$contraseña = "Datos.22";
$usuario = "consultas";
$nombreBaseDeDatos = "STORECONTROL";
$rutaServidor = "10.10.100.12";
try {
    $db = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    $s11 = $db->query("
            SELECT  a.cod_almacen, a.nombre
                , us.email , [date] as fec , emailSuper
            FROM StockCab s 
                    JOIN Almacen a on s.FK_ID_almacen=a.id
                    JOIN  users us on a.id=us.fk_ID_almacen_invs
                    join vw_stockDet_pivotStatus p on s.id=p.FK_id_StockCab
                    left join StockCab_TFA da on s.id=da.fk_id_StockCab
            WHERE tipo='TF' and INI>0 AND [date]  = DATEADD(dd,DATEDIFF(dd,0,GETDATE()),-1)
                and us.fk_ID_almacen_invs>0
    " );

    $regs = $s11->fetchAll(PDO::FETCH_OBJ);   


    $s1 = $db->query("
        SELECT s.id as id_cab, FK_ID_almacen as id_alm,  [date] as fec,
            da.responsable,  a.cod_almacen, a.nombre--, isnull(nov.NOVEDADES,0) as NOVEDADES
            , us.email, us.emailSuper
        FROM StockCab s 
            JOIN Almacen a on s.FK_ID_almacen=a.id
            JOIN  users us on a.id=us.fk_ID_almacen_invs
            left join StockCab_TFA da on s.id=da.fk_id_StockCab
            LEFT JOIN (select FK_id_StockCab AS idcab, COUNT(*) as NOVEDADES from StockDet WHERE estado='FIN' AND reconteo<>stock group by FK_id_StockCab) nov on s.id=nov.idcab
        WHERE tipo='TF' AND  [date]  = DATEADD(dd,DATEDIFF(dd,0,GETDATE()),-1) and isnull(nov.NOVEDADES,0)<>0
    " );

    $novedades = $s1->fetchAll(PDO::FETCH_OBJ);   


    function ZendMail($Xcode, $Xnome, $Xmail, $Xfec, $XmailSuper)
    {

        
        
        $dia =  $Xfec;
        $almacen= $Xcode;
        $nome = $Xnome;
        $xmail = $Xmail;
        
        $mail = new PHPMailer;
        
        //Enable SMTP debugging.
        
        $mail->SMTPDebug = 1;                           
        
        //Set PHPMailer to use SMTP.
        
        $mail->isSMTP();        
        
        //Set SMTP host name                      
        
        $mail->Host = "smtp.office365.com";
        
        //Set this to true if SMTP host requires authentication to send email
        
        $mail->SMTPAuth = true;                      
        
        //Provide username and password
        
        $mail->Username = "cs@sunsetcorpholding.com";             
        
        $mail->Password = "Mabel@2024";                       
        
        //If SMTP requires TLS encryption then set it
        
        $mail->SMTPSecure = "STARTTLS";                       
        
        //Set TCP port to connect to
        
        $mail->Port = 587;  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        

        $mail->From = "cs@sunsetcorpholding.com";
        
        $mail->FromName = "Centro de Servicios";
        
        $mail->addAddress($xmail);
        //$mail->AddAddress("destino2@correo.com","Nombre 02");
        $mail->AddCC("ainventarios@mabeltrading.com.ec");
        $mail->AddCC("areyes@cosmec.com.ec");
        $mail->AddCC("ainventarios2@mabeltrading.com.ec");
        $mail->AddCC("analistainventario@mabeltrading.com.ec");
        $mail->AddCC( $XmailSuper);
        
        $mail->isHTML(true);
        
        $mail->Subject = "INVENTARIO DIARIO NO REALIZADO " . $almacen;
        
        //$body = file_get_contents('../templatesMails/NoRealizoConteo.php');
        //$mail->Body = $body;
        
        $contenidoMail= "<html>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <p>Estimados ".$nome.",</p>

                        <b> <p>El d&iacutea ". $dia ." no se registra realizado el conteo aleatorio asignado. </p></b>
        
                        <p style='text-align: justify;'>El presente tiene como finalidad recordarles que es obligatorio la realizaci&oacuten del inventario diario en la plataforma dispuesta para el efecto y 
                        se encuentra habilitada durante todo el d&iacutea para la ejecuci&oacuten, para as&iacute no afectar las operaciones normales de los Puntos de Ventas (recepci&oacuten, perchado y arreglo de mercader&iacutea).
        En el caso de no realizar hasta el cierre se considera como un incumplimiento del proceso que se encuentra ya establecido.</p>
                        ";
        
        $contenidoMail= $contenidoMail. "
                    <p>Saludos, <br>
                        Control de Inventarios<br>
                        MABEL TRADING S.A. |  COSMECECUADOR S.A  <br>
                        Telf: +593 2 393-0940 Ext.2143  <br>
                        Garc&iacutea Moreno S/N y Panamericana Norte Km 12<br>
                        QUITO - ECUADOR
                        </p>
        
        
                    </html>";
        
        
        $mail->Body = $contenidoMail;
        
        
        $mail->AltBody = "This is the plain text version of the email content";
        
        if(!$mail->send())
        
        {
        
        echo "Mailer Error: " . $mail->ErrorInfo;
        
        }
        
        else
        
        {
        
        echo "Message has been sent successfully";
        
        }
    }
    function ZendMailNovedades($Xcode, $Xnome, $Xmail, $Xfec, $XmailSuper, $id_cab)
    {

        
        
        $dia =  $Xfec;
        $almacen= $Xcode;
        $nome = $Xnome;
        $xmail = $Xmail;
        
        $mail = new PHPMailer;
        
        //Enable SMTP debugging.
        
        $mail->SMTPDebug = 1;                           
        
        //Set PHPMailer to use SMTP.
        
        $mail->isSMTP();        
        
        //Set SMTP host name                      
        
        $mail->Host = "smtp.office365.com";
        
        //Set this to true if SMTP host requires authentication to send email
        
        $mail->SMTPAuth = true;                      
        
        //Provide username and password
        
        $mail->Username = "cs@sunsetcorpholding.com";             
        
        $mail->Password = "Mabel@2024";                       
        
        //If SMTP requires TLS encryption then set it
        
        $mail->SMTPSecure = "STARTTLS";                       
        
        //Set TCP port to connect to
        
        $mail->Port = 587;  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        

        $mail->From = "cs@sunsetcorpholding.com";
        
        $mail->FromName = "Centro de Servicios";
        
        $mail->addAddress($xmail);
        
        $mail->AddCC("ainventarios@mabeltrading.com.ec");
        $mail->AddCC("areyes@cosmec.com.ec");
        $mail->AddCC("ainventarios2@mabeltrading.com.ec");
        $mail->AddCC("analistainventario@mabeltrading.com.ec");
        $mail->AddCC( $XmailSuper);
        
        $mail->isHTML(true);
        
        $mail->Subject = "DIFERENCIAS DE INVENTARIO ALEATORIO " . $almacen;
        
        $contenidoMail= "<html>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <p>Estimados ".$nome.",</p>

        <b> <p>El d&iacutea ". $dia ." , se registra las siguientes novedades en el conteo aleatorio asignado: </p></b>

        <p style='text-align: justify;'></p>


        <table border='1'>

            <thead>
            <tr>


            <th>Codigo</th>
            <th>Articulo</th>

            <th>Diferencia</th>

            

            </tr>

            </thead>

            <tbody>
                    ";

                    $contraseña = "Datos.22";
$usuario = "consultas";
$nombreBaseDeDatos = "STORECONTROL";
$rutaServidor = "10.10.100.12";
                    $dba = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
                    $dba->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $sa1 = $dba->query("
                                                (select a.ID_articulo, a.descripcion AS Articulo, (reconteo-stock) as Diferencia 
                        from StockDet sd 
                            join Articulo a on sd.FK_ID_articulo=a.id
                        WHERE estado='FIN' AND reconteo<>stock 
                                AND FK_id_StockCab=".$id_cab.")	
                        " );
                    



                        $articles = $sa1->fetchAll(PDO::FETCH_OBJ);   

 foreach($articles as $row){  
    $contenidoMail= $contenidoMail. "
    <tr>


    <td>".$row->ID_articulo."</td>
      <td>".$row->Articulo."</td>

      <td>".$row->Diferencia."</td>

      </tr>
  



";
    } 

        
        $contenidoMail= $contenidoMail. "

        </tbody>
        </table>
                      <p>Por favor validar a la brevedad posible, caso contrario estas diferencias seran ajustadas y/o facturadas como lo indica la politica.</p><br>

                    <p>Saludos, <br>
                        Control de Inventarios<br>
                        MABEL TRADING S.A. |  COSMECECUADOR S.A  <br>
                        Telf: +593 2 393-0940 Ext.2143  <br>
                        Garc&iacutea Moreno S/N y Panamericana Norte Km 12<br>
                        QUITO - ECUADOR
                        </p>
        
        
                    </html>";
        
        
        $mail->Body = $contenidoMail;
        
        
        $mail->AltBody = "This is the plain text version of the email content";
        
        if(!$mail->send())
        
        {
        
        echo "Mailer Error: " . $mail->ErrorInfo;
        
        }
        
        else
        
        {
        
        echo "Message has been sent successfully";
        
        }
    }


   foreach($regs as $row){  
        ZendMail($row->cod_almacen,$row->nombre,$row->email,$row->fec,$row->emailSuper);
    } 


    foreach($novedades as $row){  
        ZendMailNovedades($row->cod_almacen,$row->nombre,$row->email,$row->fec,$row->emailSuper,$row->id_cab);
    } 

} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos: " . $e->getMessage();
}
  








?>
