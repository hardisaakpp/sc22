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

if ($pFecha==date('Y-m-d')) {
   // echo "mismo dia!";  ///solo actualiza si es el mismo dia
    $sentencia = $db->query("
        
    EXEC sp_cica_sincSAPSingle '". $whsCica ."', '". $pFecha ."';

    EXEC sp_cica_createCajas '". $whsCica ."', '". $pFecha ."';
    
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


    $s1 = $db->query("
    select * from CiCa 
    where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."'
    " );
    $cajas = $s1->fetchAll(PDO::FETCH_OBJ);   

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
                        <a href="cicaU.php?id=<?php echo $user->id?>">
                            <i class="pe-7s-browser"></i>
                            </a>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                        
                                <div class="stat-text"><?php echo $user->caja?></div>
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?> 

    

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

                    <button type="button" class="btn btn-secondary btn-lg" onClick=window.open("<?php echo "cicaPrint.php?id=" . $auxCAJA ?>","demo","toolbar=0,status=0,")>
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
<!----------------- DEPOSITOS -------------------->

<?php
    $s1 = $db->query("
        dep.id as 'id_dep', dep.fec_dep, ctas.id as 'id_banco', dep.nro_dep, dep.valor, dep.observacion
        where fk_ID_almacen='". $whsCica ."' and fecha_cica='".$fecha."' " );
    $consolidados = $s1->fetchAll(PDO::FETCH_OBJ);   
?>

<!---------------------------------------------->


<div class="col-md-12">
    <div class="card">
        <div class="card-body card-block">
            <strong> DEPOSITOS </strong>
        </div>
        <div class="card-body card-block">
            <form action="php/cicaSave.php" id="frmConteo" method="post" onSubmit="return validate()">  
                <table id="resumentbl" class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th id='idcicasap' style='display: none;'>idcicasap</th>
                                <th id='v1'>FECHA DEPOSITO</th>
                                <th id='v2'>CUENTA DE BANCO</th>
                                <th id='v3'>NRO DEPOSITO </th>
                                <th id='n1'>VALOR</th>
                                <th id='n2'>OBSERVACION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach($consolidados as $forpag){
                            ?>
                            <tr>
                                <td style='display: none;'>
                                    <input name="idcicasap[]" type="number"  value="<?php echo $forpag->id_dep ?>" >
                                </td>

                               
                                <td class="valSAP">  
                                    <input type="date" id="valSAP" name="valSAP[]" value="<?php echo $forpag->fec_dep; ?>" readonly>
                                </td>


                                <?php
                                if ($cerrado==0) {
                                ?> 
                                <div style="width: 20%; float:left" class="input-group">
                                    <td class="valRec">
                                        <input name="valRec[]" onkeyup="calc(this);"  step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valRec ?>" required >
                                    </td>
                                    <td class="valOnline">
                                        <input name="valOnline[]" onkeyup="calc(this);" step="any"  onchange="calc(this);" type="number"  value="<?php echo $forpag->valOnline ?>" required >
                                    </td>
                                    <td style='display: none;'>
                                        <input name="refOnline[]" type="text" maxlength="30" value="<?php echo $forpag->refOnline ?>" >
                                    </td>
                                    <td class="valPinpad">
                                        <input name="valPinPad[]"  onkeyup="calc(this);" step="any" type="number" value="<?php echo $forpag->valPinpadOn ?>"  readonly>
                                    </td>
                                    <td style='display: none;'>
                                        <input name="refPinPad[]" type="text" maxlength="30" value="<?php echo $forpag->refPinpadOn ?>" >
                                    </td>
                                    <td class="valMedianet">
                                        <input name="valDatMed[]" onkeyup="calc(this);" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valPinpadOff ?>" required >
                                    </td>
                                    <td style='display: none;'>
                                        <input name="refDatMed[]" type="text" maxlength="30" value="<?php echo $forpag->refPinpadOff ?>" >
                                    </td>
                                <?php
                                }else{
                                ?>
                                    <td class="valRec">
                                        <input name="valRec[]" onkeyup="calc(this);"  step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valRec ?>" readonly>
                                    </td>
                                    <td class="valOnline">
                                        <input name="valOnline[]" onkeyup="calc(this);" step="any"  onchange="calc(this);" type="number"  value="<?php echo $forpag->valOnline ?>" readonly>
                                    </td>
                                    <td style='display: none;'>
                                        <input name="refOnline[]" type="text"  value="<?php echo $forpag->refOnline ?>" readonly>
                                    </td>
                                    <td class="valPinpad">
                                        <input name="valPinPad[]"  onkeyup="calc(this);" step="any" type="number" value="<?php echo $forpag->valPinpadOn ?>"  readonly>
                                    </td>
                                    <td style='display: none;'>
                                        <input name="refPinPad[]" type="text"  value="<?php echo $forpag->refPinpadOn ?>" readonly>
                                    </td>
                                    <td class="valMedianet">
                                        <input name="valDatMed[]" onkeyup="calc(this);" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valPinpadOff ?>" readonly>
                                    </td>
                                    <td style='display: none;'>
                                        <input name="refDatMed[]" type="text"  value="<?php echo $forpag->refPinpadOff ?>" readonly>
                                    </td>
                                <?php   
                                }
                                ?>


                                







                                <td class="Diferencia"> 
                                <?php 
                                    
                                    $difz=$forpag->valRec+$forpag->valOnline+$forpag->valPinpadOn+$forpag->valPinpadOff-$forpag->Valor ;

                                    if ($difz<0) {
                                        echo '<input type="number" id="valSAP" style="color:red;" name="Dif[]" value="'.$difz.'" readonly>';
                                    } elseif ($difz>0) {
                                        echo '<input type="number" id="valSAP" style="color:green;" name="Dif[]" value="'.$difz.'" readonly>';
                                    }else{
                                        echo '<input type="number" id="valSAP" name="Dif[]" value="'.$difz.'" readonly>';
                                    }
                                    ?>
                                </td>
                            
                            </tr>
                            <?php } ?>
                            
                        </tbody>
                    </table>
                

            </div>

        </div>
        <div class="card-footer">


                <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> GUARDAR
                </button>
                <button type="reset" class="btn btn-danger btn-lg">
                    <i class="fa fa-ban"></i> RESET
                </button>




                <button type="button" class="btn btn-secondary btn-lg" onclick="window.location.href='cica.php?pFecha=<?php echo $fecha ?>&pIdAlmacen=<?php echo $fk_ID_almacen ?>'">
                    <i class="fa fa-sign-out"></i>&nbsp; REGRESAR
                </button>
        
            </form>

        </div>
    </div>
</div>






  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>