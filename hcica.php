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
}else{

    $auxCAJA=0;

    //CREO CABECERAS DE CAJAS
        $sentencia = $db->query("
            EXEC sp_cicaH_createCajas '". $whsCica ."', '". $pFecha ."';
        " );
        $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    //Consulto cajas cabeceras 
        $s1 = $db->query("
        SELECT distinct c.*
        from cica c join CiCaHitell ch on c.fecha=ch.fecha and c.caja=ch.caja
        where c.fk_ID_almacen=".$whsCica."	and c.fecha= '".$pFecha."'
        " );
        $cajas = $s1->fetchAll(PDO::FETCH_OBJ);   

        $qtyCajas = count($cajas);

    //Si no hay cajas no consulto si hay cjas realizo consultas


        $estado = 'INI';




       
?>


<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->




<!-- Widgets  -->
<div class="row">




    <?php  
    
    
    
    if ($qtyCajas==0) {
        $estado = 'INI';
    } else {

        $s2 = $db->query(" select top 1 status from CiCa where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."' " );
        $stat = $s2->fetchObject();

        $estado= $stat->status;  ///seteo estado 
        

        $senten2 = $db->query("
        select * from almacen where id=".$whsCica."  "  );
        $TEMPa1 = $senten2->fetchObject();

        $almacenCica = $TEMPa1->cod_almacen;
        $nomealmacenCica = $TEMPa1->nombre;

        $sentencia = $db->query("      
        select 
                c.CardName as 'forPag'
                , sum(Valor) as 'valSAP'
                , sum(valRec) as 'valRec'
                , sum(valOnline) as 'valOnline'
                , sum(valPinpadOn) as 'valPinpad'
                , sum(valPinpadOff) as 'valMedianet'
                , ( sum(valRec) +sum(valPinpadOff)+ sum(valPinpadOn)+sum(valOnline)-sum(Valor)) as 'Diferencia'
    
            from CiCaHitell c join Almacen a on a.cod_almacen=c.whsCode
            where a.id='". $whsCica ."' and c.fecha='". $pFecha ."'
            group by c.CardName" );
        $consolidados = $sentencia->fetchAll(PDO::FETCH_OBJ);


   

    
    
    
        foreach($cajas as $user){ 
            $auxCAJA=$user->id;
        
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-3">
                            <a href="hcicaU.php?id=<?php echo $user->id?>">
                                <i class="pe-7s-browser"></i>
                                </a>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                            
                                  CAJA  <div class="stat-text"><?php echo $user->caja?></div>
                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } 
    
    }
    //si esta en estado abierto muestra la opcion cargar
    if ($estado=='INI') {  ?>
        <div class="col-lg-3 col-md-6">
            <div class="card">
            
                <div class="card-body">
                
                            
                                <form method="post" action="hcicaImport.php" enctype="multipart/form-data">
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
    
     
    if ($qtyCajas>0) {
    
    
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
                                <th id='v1'>VALOR HITELL</th>
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
                                <td class="valPinpad"><?php echo $forpag->valPinpad; ?></td>
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

                    <button type="button" class="btn btn-secondary btn-lg" onClick=window.open("<?php echo "hcicaPrint.php?id=" . $auxCAJA ?>","demo","toolbar=0,status=0,")>
                        <i class="fa fa-print"></i>&nbsp; Imprimir
                    </button>

                    <?php if ($estado=='INI') {  ?>

                    <button type="button" class="btn btn-secondary btn-lg" onclick="window.location.href='php/hcica_send.php?whsCica=<?php echo $whsCica?>&fec=<?php echo $pFecha ?>'">
                        <i class="fa fa-share-square-o"></i>&nbsp; Enviar
                    </button>

                    <?php }  ?>

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
    }else { ?>
        
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-3">
                    
                        <i class="pe-7s-close"></i>
                 
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                    
                            <div class="stat-text">No hay datos</div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php   }
?>


  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>