<?php
    include_once "header.php";


/*-----------------------------------------------------------
------------------- PERFILES ------------    $userAdmin = $_SESSION["perfil"];--------------------
-------------------0->DESACTIVADO----------------------------
-------------------1->ADMIN----------------------------------
-------------------2->TIENDA---------------------------------
-------------------3->INVENTARIOS----------------------------
-------------------4->ASISTENTE PARA CONTEOS TFT-------------
-------------------5->BODEGA---------------------------------
------------------------------------------------------------->*/

    /*Si viene por parametro se coge sino nada */
    if (isset($_GET["pIdAlmacen"])) {
        $whsCica = $_GET["pIdAlmacen"];
    }
   
    
    //$pFecha= Date('2023-06-14') ;
    $pFecha= Date('Y-m-d') ;
    if (isset($_GET["pFecha"])) {
        $pFecha = $_GET["pFecha"];
    }



    //si no es ADMIN o no se  no abre
if($whsCica==0){  
    echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
    //echo $whsCica;
   // echo $pFecha;
    //exit();
}else{

    $auxCAJA=0;
//echo $whsCica ."-". $pFecha ;
//if ($pFecha==date('Y-m-d')) {
  //  date('Y-m-d', strtotime('yesterday'))
    if ($pFecha>=date('Y-m-d', strtotime('yesterday'))) {
   // echo "mismo dia!";  ///solo actualiza si es el mismo dia
   //EXEC [sp_cicUs_pass_pinpad] '". $whsCica ."', '". $pFecha ."';
    $sentencia = $db->query("
        
    EXEC sp_cic_sincSAPSingle '". $whsCica ."', '". $pFecha ."';
    delete from cicSAP where caja='NE' and fecha='". $pFecha ."';
    EXEC sp_cic_createCajas '". $whsCica ."', '". $pFecha ."';
     EXEC sp_cicUs_create '". $whsCica ."', '". $pFecha ."';


    
    " );
    $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);
}






  //  echo $whsCica;
   // echo $pFecha;

   $senten2 = $db->query("
   select * from almacen where id=".$whsCica."  "  );
   $TEMPa1 = $senten2->fetchObject();

       $almacenCica = $TEMPa1->cod_almacen;
       $nomealmacenCica = $TEMPa1->nombre;

       $s2 = $db->query(" select top 1 status,cerrado from CiC where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."' " );
       $stat = $s2->fetchObject();

       $estado= $stat->cerrado;  ///seteo estado 66664


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
                        WHEN c.CardName LIKE 'Crédito directo - Venta' THEN 'CREDITO DIRECTO CREDICORP'
                        WHEN c.CardName LIKE 'Crédito directo - Pago de abono' THEN 'EFECTIVO'
                        ELSE c.CardName
                    END  as CardName
                    , sum(Valor) as 'valSAP'
                    , sum(valPinpadOn) as 'valPinpadOn'
                /*   , sum(valRec) as 'valRec'
                    , sum(valOnline) as 'valOnline'
                    , sum(valPinpadOn) as 'valPinpadOn'
                    , sum(valPinpadOff) as 'valMedianet'
                    , ( sum(valRec) +sum(valPinpadOff)+ sum(valPinpadOn)+sum(valOnline)-sum(Valor)) as 'Diferencia'
        */
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
                WHEN c.CardName LIKE 'Crédito directo - Venta' THEN 'CREDITO DIRECTO CREDICORP'
                WHEN c.CardName LIKE 'Crédito directo - Pago de abono' THEN 'EFECTIVO'
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
				WHEN CardName LIKE 'Crédito directo - Venta' THEN 'CREDITO DIRECTO CREDICORP'
				WHEN CardName LIKE 'Crédito directo - Pago de abono' THEN 'EFECTIVO'
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
				WHEN CardName LIKE 'Crédito directo - Venta' THEN 'CREDITO DIRECTO CREDICORP'
				WHEN CardName LIKE 'Crédito directo - Pago de abono' THEN 'EFECTIVO'
				ELSE CardName
			END 
		) q2
		on q1.fecha=q2.fecha and q1.whsCode=q2.whsCode and q1.CardName=q2.CardName




    " );
    $consolidados = $sentencia->fetchAll(PDO::FETCH_OBJ);
       
?>

<!-- Breadcrumbs-->
   <!-- <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>CAMBIO DE CLAVE</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                 <li>
                                <button type="button" class="btn btn-outline-success" onclick="chargeTFA();">►</button>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">F5</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'">X</button>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>-->
<!-- /.breadcrumbs-->




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
                    <a href="cicU.php?id=<?php echo $user->id?>">
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

//si esta en estado abierto muestra la opcion cargar
if ($estado==0) {  ?>
    <div class="col-lg-3 col-md-6">
        <div class="card">
        
            <div class="card-body">
            
                        
                            <form method="post" action="hcicImport.php" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input name="tiendaTuremp" value='<?php echo $whsCica; ?>' hidden>
                                    <input name="pFecha" value='<?php echo $pFecha; ?>' hidden>
                                    <!-- <label for="exampleInputFile"><h3>Importar turnos</h3></label> -->
                                    <input type="file" accept=".xlsx" name="file" class="form-control" id="exampleInputFile" required>
                                
                                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-secondary btn-lg" >
                    <i class="fa fa-upload"></i>&nbsp; CARGAR CIERRE HITELL
                </button>
            </div>
            </form>
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
                                <th id='v1'>VALOR SAP</th>
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

                    <button type="button" class="btn btn-secondary btn-lg" onClick=window.open("<?php echo "cicPrint.php?id=" . $auxCAJA ?>","demo","toolbar=0,status=0,")>
                        <i class="fa fa-print"></i>&nbsp; Imprimir
                    </button>
                </div>
            </form> 
        </div>



    </div>



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