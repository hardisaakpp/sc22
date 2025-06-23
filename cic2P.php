

<?php
  session_start();
  include_once "php/bd_StoreControl.php";
  //include_once "base_de_datos.php";

  // Validating Session
  if(strlen($_SESSION['username'])==0)
  {
    //header('location:index.php');
    exit();
  }
  
    //si tiene un almacen asociado?
    if( !isset($_GET["id"])){  
        echo "<h3 style='color:black';> No tiene un almacen asociado al usuario. </h3>";  
    }else{

        //recupero codigo almacen
        $sentencia2 = $db->query(" select c.*, a.cod_almacen, a.nombre from cic c join Almacen a on c.fk_ID_almacen=a.id where c.id='". $_GET["id"] ."' " );
        $regCodCierre = $sentencia2->Object();

        $whsCica = $regCodCierre->fk_ID_almacen;
        $pFecha =$regCodCierre->fecha;
        $pAlmacen = $regCodCierre->cod_almacen.' '.$regCodCierre->nombre ;
    

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
        $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);

        $sentenciax = $db->query("      
        select distinct
                c.caja, c.responsable, c.observacion
            from CiC c 
            where fk_ID_almacen='". $whsCica ."' and fecha='". $pFecha ."'
          " );
        $reponsables = $sentenciax->fetchAll(PDO::FETCH_OBJ);
?>
<html>
<head>
    <style>
        p.inline {display: inline-block;}
        span { font-size: 13px;}
    </style>
    <style type="text/css" media="print">
        @page 
        {
            size: auto + 2px;   /* auto is the initial value */
            margin: 8mm;  /* this affects the margin in the printer settings */
        }

        body {
            page-break-before: avoid;
             }
        .wrapper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-auto-columns: auto;
        row-gap: 3px;
        column-gap: 5px;
        align-content: center;
        }

        .wrapper > div {

        background-color: rgba(255, 255, 255, 0.8);
        
        padding: 0px 0;

        }
        .box1 {

        grid-column: 1;
        grid-row: 4/1;
        border: 1px solid rgba(0, 0, 0, 0.8);
        padding: 0px;
        margin: 0px;
        font-size: 15px;
        text-align: left;

        }
        .box2 {
            border: 1px solid rgba(0, 0, 0, 0.8);
        padding: 0px;
        margin: 0px;
        
        text-align: center;
        }
        .box3 {
        text-align: center;
        display: flex;
        justify-content: center;
        }

        table {
        width: 100%;
        border: 1px solid #000;
        }
        th, td {
        width: 25%;
        text-align: left;
        vertical-align: top;
        border: 1px solid #000;
        border-collapse: collapse;
        }

      
    </style>
</head>
<body onload="window.print();">
    <h2 class="display-6">CUADRE DE CAJAS</h2>
    <p class="lead">
        FECHA DE CIERRE: <?php echo  $pFecha; ?></br>
        ALMACEN: <?php echo $pAlmacen; ?></br>
    </p> <p class="lead"> 
        USUARIO: <?php echo $_SESSION['username']; ?></br>
        FECHA IMPRESION: <?php echo date('Y-m-d H:i:s') ?>
    </p>
    <div class="table-sm" style="width:100%">
            <table class="table" id="resumentbl" >
                <thead class="thead-dark">
                    <tr>

                        <th>FORMA PAGO</th>
                        <th id='v1'>VALOR.SAP</th>
                        <th id='v2'>RECIBIDO</th>
                        <th id='v3'>ONLINE</th>
                      
                        <th id='v4'>PINPAD</th>
                    
                        <th id='v5'>MEDIANET</th>
      
                        <th id='v6'>DIFERENCIA</th>
                    </tr>
                </thead>
                <tbody>
        <?php 

            foreach($cajas as $forpag){
                ?>
                    <tr>
                        <td><?php echo $forpag->forPag ?></td>
                        <td class="valSAP"><?php echo $forpag->valSAP; ?></td>
                        <td class="valRec"><?php echo $forpag->valRec; ?></td>
                        <td class="valOnline"><?php echo $forpag->valOnline; ?></td>
                        <td class="valPinpad"><?php echo $forpag->valPinpadOn; ?></td>
                        <td class="valMedianet"><?php echo $forpag->valMedianet; ?></td>
                        <td class="Diferencia"><?php echo $forpag->Diferencia; ?></td>
                    </tr>
                <?php } ?>
                            
                        </tbody>
                    </table>
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
                        document.getElementById('resumentbl').innerHTML += '<tr><td>TOTAL:</td><td>' + sum.toFixed(2) + '</td><td>' +  svalRec.toFixed(2) +
                        '</td><td>' +  svalOnline.toFixed(2) +
                        '</td><td>' +  svalPinpad.toFixed(2) +
                        '</td><td>' +  svalMedianet.toFixed(2) +'</td></tr>';
                    </script>
                </div>


                   
             

<h3>Responsables y comentarios:</h3>

<?php



        foreach($reponsables as $reponsable){
            echo  "Caja ".$reponsable->caja.": ".$reponsable->responsable." (".$reponsable->observacion.") </br>" ;
        }

//------------------- adjuntos
        
    $ss1 = $db->query("
    select * from cic 
    where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."'
    " );
    $cajasnums = $ss1->fetchAll(PDO::FETCH_OBJ);   

        echo   '
        <h3>Adjuntos</h3>
                <table class="table">
                <thead>
                    <tr>
                        <th width="30%">Caja</th>
                        <th width="60%">Nombre del Archivo</th>
                    </tr>
                </thead>
                <tbody>';


      foreach($cajasnums as $user){ 
            $auxCAJA=$user->id;
            $path = "films/" . $auxCAJA;

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $archivos = scandir($path);
                $num=0;
                for ($i=2; $i<count($archivos); $i++)
                {$num++;
                    echo '
                    <p>  
                    </p> 
                <tr>
                    <th scope="row">'.$user->caja.'</th>
                    <td>'.$archivos[$i].'</td>
                </tr>';

                } 
            }  
        
        echo  ' </tbody>
                </table>
                </div>
                </div>
             
            </div>
        </div>';



    }



?>
</body>
</html>