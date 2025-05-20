<?php
    include_once "header.php";
    include_once "php/f_consumo.php";
    /*Si viene por parametro se coge sino nada */
    $tiendaCica=0;
    if (isset($_GET["pIdAlmacen"])) {
        $tiendaCica = $_GET["pIdAlmacen"];
    }else if (isset($_SESSION["whsCica"])) {
        $tiendaCica=$_SESSION["whsCica"];
    }else {
        exit('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA O PERMISOS');
    }   
    
    //Si existe Get se coge la fecha
    //sino se coge la fecha de hoy
    $pFecha= Date('Y-m-d') ;
    if (isset($_GET["pFecha"])) {
        $pFecha = $_GET["pFecha"];
    }
    

   
    //obtener token
    $token=get_token($tiendaCica, $db, $userName);    

		$sentencia2 = $db->query("select * from almacen where id=".$tiendaCica."  "  );
		$TEMP1 = $sentencia2->fetchObject();
			$h_cod_neg = $TEMP1->hit_cod_neg;
			$h_cod_local = $TEMP1->hit_cod_local;
			$emp=$TEMP1->fk_emp;
    


  
  
  
//si no es ADMIN o no se  no abre
if($whsCica==0){  
    echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
    //echo $whsCica;
   // echo $pFecha;
    //exit();
}else{


    //SI ES HOY ACTUALIZA 
    if ($pFecha == date('Y-m-d')) {
        //proceso
            //encerar formas de pago
                for ($i=1; $i < 8; $i++) { 
                    $sentencia1 = $db->prepare("exec sp_cicH_clearCaja  ?, ?, ?;" );
                    $resultado1 = $sentencia1->execute([$tiendaCica, $pFecha, $i]);
                }
            //registros de arqueo de caja
                //importo de hitell
                for ($i=1; $i < 8; $i++) { 
                    // arqueo de formas de pago  
                    $data2=consumo_arqueo($token,$h_cod_neg,$h_cod_local,$i,$pFecha);
                    // arqueo de abonos
                    $data3=consumo_arqueo_abonos($token,$h_cod_neg,$h_cod_local,$i,$pFecha);
                //  echo '<pre>';
                //  print_r($data3["total_amount"]);
                //  echo '</pre>';
                    if (($data2)<>null) {
                        foreach ($data2 as $key => $value) {
                            $sentencia1 = $db->prepare("exec sp_cic2_insertLine  ?, ?, ?, ?,?, ?;" );
                            $resultado1 = $sentencia1->execute([$tiendaCica, $pFecha, $i, $key, $value["total_amount"], $value["count"]]);
                        // echo $tiendaCica." - ".$pFecha." - " .$i." - " .$key." - " .$value["total_amount"]." - " .$value["count"]."</br> ";
                        }
                    }
                    if (($data3)<>null and $data3["total_amount"]>0) {
                            $sentencia1 = $db->prepare("exec sp_cic2_insertLine  ?, ?, ?, ?,?, ?;" );
                            $resultado1 = $sentencia1->execute([$tiendaCica, $pFecha, $i, 'Crédito directo - Pago de abono', $data3["total_amount"], $data3["count"]]);
                    }
                    //pasa los pinpads online de hitell a la columna de pinpad
                    $sentencia31 = $db->prepare("exec sp_cicH_updateLine_pinpad  ?, ?, ?;" );
                    $resultado31x = $sentencia31->execute([$tiendaCica, $pFecha, $i]);
                // echo $tiendaCica." - ". $pFecha." - ". $i;
                }
            

                $auxCAJA=0;
                ///solo actualiza si es el mismo dia
                if ($pFecha>=date('Y-m-d', strtotime('yesterday'))) {
                // echo "mismo dia!";  
                //-- delete from cicSAP where caja='NE' and fecha='". $pFecha ."';
                //EXEC [sp_cicUs_pass_pinpad] '". $whsCica ."', '". $pFecha ."';
                $sentencia = $db->query("
                EXEC sp_cic_sincSAPSingle '". $whsCica ."', '". $pFecha ."';
                EXEC sp_cic_createCajas '". $whsCica ."', '". $pFecha ."';
                EXEC sp_cicUs_create '". $whsCica ."', '". $pFecha ."';


                
                " );
                $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);
                }
    }

   $senten2 = $db->query("
   select * from almacen where id=".$whsCica."  "  );
   $TEMPa1 = $senten2->fetchObject();

       $almacenCica = $TEMPa1->cod_almacen;
       $nomealmacenCica = $TEMPa1->nombre;

       $s2 = $db->query(" select top 1 status,cerrado from CiC where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."' " );
       $stat = $s2->fetchObject();

       if ($stat) {
        $estado= $stat->cerrado;  ///seteo estado 66664
       }else {
        $estado= null;  
        error_log("Error: No se encontró el estado de la caja para la fecha $pFecha y el almacén $whsCica");
       }
       


    $s1 = $db->query("
    select * from cic 
    where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."'
    " );
    $cajas = $s1->fetchAll(PDO::FETCH_OBJ);   

    $sentencia = $db->query("      
   
    
      select q1.id as almacen, q1.fecha, q1.CardName as forPag, Q1.valSAP,  q2.[valRec]
			  ,q2.[valOnline]
			  ,q1.[valPinpadOn] as valPinpadOn
			  ,q2.[valPinpadOff] as valMedianet 
			   , ( (q2.valRec) +(q2.valPinpadOff)+ (q1.valPinpadOn)+(q2.valOnline)-(q1.valSAP)) as 'Diferencia'
			  from
            (
            select 
            c.fecha,c.whsCode,a.id,
                    CASE 
                        WHEN c.CardName LIKE 'Nota de crédito' THEN 'Nota de Crédito'
                        WHEN c.CardName LIKE '%VISA' THEN 'Visa'
                        WHEN c.CardName LIKE '%MASTERCARD' THEN 'MasterCard'
                        WHEN c.CardName LIKE '%DISCOVER' THEN 'Diners'
                        WHEN c.CardName LIKE '%DINERS' THEN 'Diners'
                        WHEN c.CardName LIKE '%AMERICAN EXPRESS' THEN 'American Express'
                        WHEN c.CardName LIKE 'Efectivo - Venta' THEN 'EFECTIVO'
                        WHEN c.CardName LIKE 'Efectivo' THEN 'EFECTIVO'
                        WHEN c.CardName LIKE 'Crédito directo' THEN 'CREDITO DIRECTO CREDICORP'
                        WHEN c.CardName LIKE '%Pago de abono' THEN 'EFECTIVO'
                        ELSE c.CardName
                    END  as CardName
                    , sum(Valor) as 'valSAP'
                    , sum(valPinpadOn) as 'valPinpadOn'
               
                from cicSAP c join Almacen a on a.cod_almacen=c.whsCode
                where c.origen not like 'H'  and a.id='". $whsCica ."' and c.fecha='". $pFecha ."'
                group by a.id,
                (
                CASE 
                        WHEN c.CardName LIKE 'Nota de crédito' THEN 'Nota de Crédito'
                        WHEN c.CardName LIKE '%VISA' THEN 'Visa'
                        WHEN c.CardName LIKE '%MASTERCARD' THEN 'MasterCard'
                        WHEN c.CardName LIKE '%DISCOVER' THEN 'Diners'
                        WHEN c.CardName LIKE '%DINERS' THEN 'Diners'
                        WHEN c.CardName LIKE '%AMERICAN EXPRESS' THEN 'American Express'
                        WHEN c.CardName LIKE 'Efectivo - Venta' THEN 'EFECTIVO'
                        WHEN c.CardName LIKE 'Efectivo' THEN 'EFECTIVO'
                        WHEN c.CardName LIKE 'Crédito directo' THEN 'CREDITO DIRECTO CREDICORP'
                        WHEN c.CardName LIKE '%Pago de abono' THEN 'EFECTIVO'
                ELSE c.CardName
            END  
                ) , c.fecha,c.whsCode
            )q1
	 join
	(
		SELECT [fecha]
			  ,[whsCode]
			  , CASE 
				WHEN CardName LIKE 'Nota de crédito' THEN 'Nota de Crédito'
				WHEN CardName LIKE '%VISA' THEN 'Visa'
				WHEN CardName LIKE '%MASTERCARD' THEN 'MasterCard'
				WHEN CardName LIKE '%DISCOVER' THEN 'Diners'
				WHEN CardName LIKE '%DINERS' THEN 'Diners'
				WHEN CardName LIKE '%AMERICAN EXPRESS' THEN 'American Express'
				WHEN CardName LIKE 'Efectivo - Venta' THEN 'EFECTIVO'
                WHEN CardName LIKE 'Efectivo' THEN 'EFECTIVO'
                WHEN CardName LIKE 'Crédito directo' THEN 'CREDITO DIRECTO CREDICORP'
                WHEN CardName LIKE '%Pago de abono' THEN 'EFECTIVO'
				ELSE CardName
			END   as CardName
			  ,sum([valRec]) as [valRec]
			  ,sum([valOnline]) as [valOnline]
			  ,0 as [valPinpadOn]
			  ,sum([valPinpadOff]) as [valPinpadOff]
		  FROM [dbo].[cicUs]
		  group by [fecha]
			  ,[whsCode]
			  , CASE 
				WHEN CardName LIKE 'Nota de crédito' THEN 'Nota de Crédito'
				WHEN CardName LIKE '%VISA' THEN 'Visa'
				WHEN CardName LIKE '%MASTERCARD' THEN 'MasterCard'
				WHEN CardName LIKE '%DISCOVER' THEN 'Diners'
				WHEN CardName LIKE '%DINERS' THEN 'Diners'
				WHEN CardName LIKE '%AMERICAN EXPRESS' THEN 'American Express'
				WHEN CardName LIKE 'Efectivo - Venta' THEN 'EFECTIVO'
                WHEN CardName LIKE 'Efectivo' THEN 'EFECTIVO'
                WHEN CardName LIKE 'Crédito directo' THEN 'CREDITO DIRECTO CREDICORP'
                WHEN CardName LIKE '%Pago de abono' THEN 'EFECTIVO'
				ELSE CardName
			END 
		) q2
		on q1.fecha=q2.fecha and q1.whsCode=q2.whsCode and q1.CardName=q2.CardName




    " );
    $consolidados = $sentencia->fetchAll(PDO::FETCH_OBJ);
       
?>

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->




<!-- Widgets  -->
<div class="row">

<?php   foreach($cajas as $user){ 
    $auxCAJA=$user->id;
  
    ?>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-3">
                    <a href="cic2U.php?id=<?php echo $user->id?>">
                        <i class="pe-7s-browser"></i>
                        </a>
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                       
                            <div class="stat-text"><?php echo $user->caja?></div>
                            <div><?php echo $user->responsable."-".$user->observacion?></div>
                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } 

?> 

    

</div>
<!------------------------------------------------------------------------------------------------>
<!------------------------------------------------------------------------------------------------>



<!------------------------------------------------------------------------------------------------>

    <!--//conteo-->
    <div class="col-md-12">
        
       

        <div class="card">
            <form id="frmConteo" >
                <div class="card-header">
                    <?php echo "<strong>[".$pFecha."] CIERRE DE CAJA CONSOLIDADO DE ".$nomealmacenCica." </strong>" ?>                     
                </div>
                <div class="card-body card-block">
                    <table id="resumentbl" class="table table-hover">
                        <thead class="thead-dark">
                            <tr>

                                <th>FORMA PAGO</th>
                                <th id='v1'>TOTAL CIERRE</th>
                                <th id='v2'>RECIBIDO</th>
                                <th id='v3'>ONLINE</th>
                            
                                <th id='v4'>PINPAD</th>
                            
                                <th id='v5'>DATAFAST/ MEDIANET</th>
            
                                <th id='v6'>DIFERENCIA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach($consolidados as $forpag){
                            ?>
                            <tr>
                                <td><?php echo $forpag->forPag ?></td>
                                <td class="valSAP"><?php echo $forpag->valSAP; ?></td>
                                <td class="valRec"><?php echo $forpag->valRec; ?></td>
                                <td class="valOnline"><?php echo $forpag->valOnline; ?></td>
                                <td class="valPinpad"><?php echo $forpag->valPinpadOn; ?></td>
                                <td class="valMedianet"><?php echo $forpag->valMedianet; ?></td>
                               <?php 
                                $difz=$forpag->Diferencia;
                                if ($difz<0) {
                                    echo '<td class="Diferencia"  style="color:red;">'.$difz.' </td>';
                                } elseif ($difz>0) {
                                    echo '<td class="Diferencia"  style="color:green;">'.$difz.' </td>';
                                }else{
                                    echo '<td class="Diferencia" >'.$difz.' </td>';
                                }
                                
                                 ?>
                            </tr>
                            <?php } ?>
                        
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">

                    <button type="button" class="btn btn-secondary btn-lg" onClick=window.open("<?php echo "cic2P.php?id=" . $auxCAJA ?>","demo","toolbar=0,status=0,")>
                        <i class="fa fa-print"></i>&nbsp; Imprimir
                    </button>
                </div>
            </form> 
        </div>



    </div>

<!---------ADJUNTOS--------->
    <div class="card">
            <div class="card-header"><strong>ADJUNTOS</strong></div>
            <div class="card-body card-block">
 
                <!--tabla-->
                <div class="panel panel-primary">
                   
                    <div class="panel-body">
                
                <table class="table">
                <thead>
                    <tr>
                    <th width="30%">Caja</th>
      <th width="60%">Nombre del Archivo</th>
      <th width="10%">Descargar</th>
                    </tr>
                </thead>
                <tbody>
    <?php   foreach($cajas as $user){ 
    $auxCAJA=$user->id;
    ?>
    <?php 
         $path = "films/" . $auxCAJA;
        ?> 
    
                <?php
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $archivos = scandir($path);
                $num=0;
                for ($i=2; $i<count($archivos); $i++)
                {$num++;
                ?>
                <p>  
                </p> 
                <tr>
      <th scope="row"><?php echo $user->caja;?></th>
      <td><?php echo $archivos[$i]; ?></td>
      <td><a
      href="<?php echo $path . "/" . $archivos[$i]; ?>" download="<?php echo $archivos[$i]; ?>"
      > 💾  </a>  </td>
      </tr>
                <?php }?> 
                <?php } ?> 
                </tbody>
                </table>
                </div>
                </div>
             
            </div>
        </div>

<!-- Fin Adjuntos--> 


    <script language="javascript" type="text/javascript">
        var tds = document.getElementById('resumentbl').getElementsByTagName('td');
        var sum = 0.0;
        var svalRec = 0.0;
        var svalOnline = 0.0;
        var svalPinpad = 0.0;
        var svalMedianet = 0.0;
        for(var i = 0; i < tds.length; i ++) {
            if(tds[i].className == 'valSAP') {
                sum += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valRec') {
                svalRec += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valOnline') {
                svalOnline += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valPinpad') {
                svalPinpad += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valMedianet') {
                svalMedianet += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }

        }
        document.getElementById('resumentbl').innerHTML += '<tr class="table-secondary"><td>TOTAL:</td><td>' + sum.toFixed(2) + '</td><td>' +  svalRec.toFixed(2) +
        '</td><td>' +  svalOnline.toFixed(2) +
        '</td><td>' +  svalPinpad.toFixed(2) +
        '</td><td>' +  svalMedianet.toFixed(2) +'</td></tr>';
    </script>

        <?php
    
?>

<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>