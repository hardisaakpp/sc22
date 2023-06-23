

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
        $sentencia2 = $db->query(" select c.*, a.cod_almacen, a.nombre from cica c join Almacen a on c.fk_ID_almacen=a.id where c.id='". $_GET["id"] ."' " );
        $regCodCierre = $sentencia2->fetchObject();

        $whsCica = $regCodCierre->fk_ID_almacen;
        $pFecha =$regCodCierre->fecha;
        $pAlmacen = $regCodCierre->cod_almacen.' '.$regCodCierre->nombre ;
    

        $sentencia = $db->query("      
        select 
                c.CardName as 'forPag'
                , sum(Valor) as 'valSAP'
                , sum(valRec) as 'valRec'
                , sum(valOnline) as 'valOnline'
                , sum(valPinpadOn) as 'valPinpad'
                , sum(valPinpadOff) as 'valMedianet'
                , ( sum(valRec) +sum(valPinpadOff)+ sum(valPinpadOn)+sum(valOnline)-sum(Valor)) as 'Diferencia'
    
            from CiCaSAP c join Almacen a on a.cod_almacen=c.whsCode
            where a.id='". $whsCica ."' and c.fecha='". $pFecha ."'
            group by c.CardName" );
        $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);

        $sentenciax = $db->query("      
        select distinct
                c.caja, c.responsable, c.observacion
            from CiCa c 
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
                        <td class="valPinpad"><?php echo $forpag->valPinpad; ?></td>
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

                    </br>
                    </br>
                    CAJAS:
                    </br>
                <?php

foreach($reponsables as $reponsable){
   
   
  echo  $reponsable->caja.": ".$reponsable->responsable." (".$reponsable->observacion.") </br>" ;
   


}
}



?>
</body>
</html>