<?php
include_once "header.php";
   

    //si es tienda y no tiene asignada una tienda para cierres
if($whsCica==0 && $userAdmin==2 ){  
        echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
}elseif( !isset($_GET["id"])){  
    echo ('...');
}else{
    $id =  $_GET["id"];

//echo $id;

    $sentencia2 = $db->query("  select c.* , a.cod_almacen
                                from cic c join Almacen a on c.fk_ID_almacen=a.id
                                where c.id=".$id."  "  );
    $TEMP1 = $sentencia2->fetchObject();

        $caja = $TEMP1->caja;
       // $Items = $TEMP1->items;
       // $cantidad = $TEMP1->cantidad;
       $fk_ID_almacen = $TEMP1->fk_ID_almacen;
        $fecha = $TEMP1->fecha;
        $cerrado = $TEMP1->cerrado;
        $responsable = $TEMP1->responsable;
        $observacion = $TEMP1->observacion;
        $whsCode = $TEMP1->cod_almacen;

//creo cicUSER
       
//if ($pFecha==date('Y-m-d')) {
   // echo "mismo dia!";  ///solo actualiza si es el mismo dia
    $sentencia = $db->query("
    EXEC sp_cicUs_create '". $fk_ID_almacen ."', '". $fecha ."';
    " );
    $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);
//}

//SI ES ADMIN SIEMPRE DESBLOQUEADO
if ($userAdmin==1) {
    $cerrado=0;
}
//SI ES CONTABILIDAD
if ($userAdmin==6) {
    $cerrado=0;
}


     //   echo $caja . $fecha  . $Cerrado;


    $s1 = $db->query("
    select  *
    from vw_cicCaja where caja='". $caja ."' and fecha='".$fecha."' and whsCode='".$whsCode."' " );
    $consolidados = $s1->fetchAll(PDO::FETCH_OBJ);   

       

    $ttvValSap =0; //variable para total
    $ttvRec =0; 
    $ttvOnline =0; 
    $ttvPinPad =0; 
    $ttvDatfast =0; 

?>



<script type="text/javascript">

document.addEventListener("DOMContentLoaded", function() {
document.getElementById("frmConteo").addEventListener('submit', validarFormulario); 
});

function validarFormulario(evento) {
  evento.preventDefault();
  
  /*var usuario = document.getElementById('usuario').value;
  if(usuario.length == 0) {
    alert('No has escrito nada en el usuario');
    return;
  }
  var clave = document.getElementById('clave').value;
  if (clave.length < 6) {
    alert('La clave no es válida');
    return;
  }
  this.submit();
  */

  var observacion = document.getElementById("Observaciones");
  observacion.value = observacion.value.trim();
  // console.log (observacion.value.trim().length);

if (observacion.value.trim().length==0) {
    //console.log ('vacio');

    const resume_table = document.getElementById("resumentbl");
    var filas = $("#ajuste").find("tr"); //devulve las filas del body de tu tabla segun el ejemplo que brindaste
	
	for(i=0; i<filas.length; i++){ //Recorre las filas 1 a 1
		var celdas = $(filas[i]).find("td"); //devolverá las celdas de una fila
		//codigo = $(celdas[0]).text();
		//descripcion= $(celdas[1]).text();
		//valorsap = $($(celdas[8]).children("input")[0]).val();
       // diferencia = $($(celdas[9]).children("input")[0]).val();
        impuesto = $($(celdas[10]).children("input")[0]).val();
            
        if (impuesto!=0) {
            alert('Tiene diferencias, favor ingrese una observacion para guardar. Gracias.');
            observacion.focus();
            return;
        }
	}

    this.submit();




} else {
    this.submit();
}



   /*
    const resume_table = document.getElementById("resumentbl");
    var filas = $("#ajuste").find("tr"); //devulve las filas del body de tu tabla segun el ejemplo que brindaste
	  var resultado = 0;
	for(i=0; i<filas.length; i++){ //Recorre las filas 1 a 1
		var celdas = $(filas[i]).find("td"); //devolverá las celdas de una fila
		//codigo = $(celdas[0]).text();
		//descripcion= $(celdas[1]).text();
		//valorsap = $($(celdas[8]).children("input")[0]).val();
       // diferencia = $($(celdas[9]).children("input")[0]).val();
        impuesto = $($(celdas[10]).children("input")[0]).val();
            
        if (impuesto!=0) {
            resultado=resultado+1;
            //console.log(impuesto);
        }
	}
	
	alert(resultado);

*/






  //alert('La clave no es válida');
    return;


}











    function indexesLX(){
        try {
                $('#n1').click(function() {
                    var n11 = 6;
                    $("td:nth-child(" + n11 + "),th:nth-child(" + n11 + ")").toggle();
                    
                });
                $('#n2').click(function() {
                    $('td:nth-child(8),th:nth-child(8)').toggle();
                });
                $('#n3').click(function() {
                    $('td:nth-child(10),th:nth-child(10)').toggle();
                });
        }
            catch(x) { /* puede usarse cualquier otro nombre en lugar de 'x' */
            //document.getElementById("ejemplo").innerHTML = x.message;
        }
    // setInterval('contadoradd()',2000);
    }


   
       
   

  

    function calc(id) {
        var row=id.parentNode.parentNode;
        //alert("Cell index is: " + ( document.getElementById('valSAP')).cellIndex);
        
        var valSAP=row.cells[( document.getElementById('v1')).cellIndex].getElementsByTagName('input')[0].value;
        var recibido=row.cells[( document.getElementById('v2')).cellIndex].getElementsByTagName('input')[0].value;
        var online=row.cells[( document.getElementById('v3')).cellIndex].getElementsByTagName('input')[0].value;
        var pinpad=row.cells[( document.getElementById('v4')).cellIndex].getElementsByTagName('input')[0].value;
        var dataf=row.cells[( document.getElementById('v5')).cellIndex].getElementsByTagName('input')[0].value;
        
        //encerando
       /* if (recibido == null || recibido == '') { row.cells[( document.getElementById('v2')).cellIndex].getElementsByTagName('input')[0].value=0.00; recibido = 0.00;}
        if (online == null || online == '') { row.cells[( document.getElementById('v3')).cellIndex].getElementsByTagName('input')[0].value=0.00;online =0.00; }
        if (dataf == null || dataf == '') { row.cells[( document.getElementById('v5')).cellIndex].getElementsByTagName('input')[0].value=0.00; dataf=0.00;}

        */
        
        if(online==null || online=='') {
        res=parseFloat(valSAP)*parseFloat(recibido);
        } else {
        var res=(parseFloat(0+pinpad)+parseFloat(0+dataf)+parseFloat(0+recibido)+parseFloat(0+online)-parseFloat(0+valSAP)).toFixed(2);
        }
        row.cells[( document.getElementById('v6')).cellIndex].getElementsByTagName('input')[0].value=res;
        if (res<0) {
            row.cells[( document.getElementById('v6')).cellIndex].getElementsByTagName('input')[0].style.color='red';
        } else {
            row.cells[( document.getElementById('v6')).cellIndex].getElementsByTagName('input')[0].style.color='green';
        }

       // document.getElementById("ttRec").value="HOLA";
       // <!--alex-->
           
       /*
       $ttValSap = $ttValSap + $forpag->Valor;
            $ttvRec = $ttvRec+ $forpag->valRec; 
            $ttvOnline = $ttvOnline $forpag->valOnline; 
            $ttPinPad = $ttPinPad + $forpag->valPinpadOn;
            $ttvDatfast = $ttvDatfast +$forpag->valPinpadOff ; 

*/


       var tabla = document.getElementById("resumentbl");
        var filas = tabla.getElementsByTagName("tr");


        var xRECIBIDO = 0;
        var xONLINE = 0;
        var xDATAF = 0
        
        for (var i = 0; i < filas.length-1; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            for (var j = 2; j < celdas.length; j++) {
                var input = celdas[j].querySelector("input");
                if (input) {
                    var valor = input.value;
                    console.log("Valor del input en fila " + (i + 1) + ", columna " + (j + 1) + ": " + valor);

                if ((j + 1)==4) {
                    xRECIBIDO = xRECIBIDO  + parseFloat(valor);
                } else if((j + 1)==5)  {
                    xONLINE = xONLINE  + parseFloat(valor);
                }else if((j + 1)==9)  {
                    xDATAF = xDATAF  + parseFloat(valor);
                }

                }
            }
        }

        document.getElementById("ttvRec").value=xRECIBIDO;
        document.getElementById("ttvOnline").value=xONLINE;
        document.getElementById("ttvDatfast").value=xDATAF;
 
    };

</script>


<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

    <div class="col-md-12">
        
       

    <div class="card">
        
        <div class="card-header">
            <strong> <?php 
                if ($cerrado==1) {
                    echo $caja."  [".$fecha."] 🔒";
                } else {
                    echo $caja."  [".$fecha."] 🔓";
                }
                
            
             ?> </strong>  
             
        </div>
        <div class="card-body card-block">
            <form action="php/cicSave.php" id="frmConteo" method="post" onSubmit="return validate()">    
                <input class="form-control" name='id' type="hidden" value="<?php echo $id; ?>" >        
                <input class="form-control" name='ccaja' type="hidden" value="<?php echo $caja; ?>" >        
                <input class="form-control" name='fec' type="hidden" value="<?php echo $fecha; ?>">        
                <!---------cabecera------------>
                
                <?php
                if ($cerrado==0) {
                ?> 
                   <div style="width: 20%; float:left" class="input-group">
                    <input class="form-control" placeholder="Responsable" name='crespons' type="text" maxlength="49" value="<?php echo $responsable; ?>" required>
                    </div>
                    <div style="width: 80%; float:right">
                        <input class="form-control" placeholder="Observaciones" id='Observaciones'  name='cobs' maxlength="400" type="text" value="<?php echo $observacion; ?>">
                    </div>
                <?php
                }else{
                ?>
                    <div style="width: 20%; float:left" class="input-group">
                    <input class="form-control" placeholder="Responsable" name='crespons' type="text" maxlength="49" value="<?php echo $responsable; ?>" readonly>
                    </div>
                    <div style="width: 80%; float:right">
                        <input class="form-control" placeholder="Observaciones" name='cobs' maxlength="400" type="text" value="<?php echo $observacion; ?>" readonly>
                    </div>
                <?php   
                }
                ?>
                
                <!---------grid------------>
                <table id="resumentbl" class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th id='idcicasap' style='display: none;'>idcicasap</th>
                            <th>FORMA PAGO</th>
                            <th id='v1'>VALOR.SAP</th>
                            <th id='v2'>RECIBIDO</th>
                            <th id='v3'>ONLINE <i id='n1' aria-hidden="true" class="fa fa-comment"></i></th>
                            <th id='n1' style='display: none;'>NOTA</th>
                            <th id='v4'>PINPAD</th>
                            <th id='n2' style='display: none;'>NOTA</th>
                            <th id='v5'>DATAFAST/ MEDIANET <i id='n3' aria-hidden="true" class="fa fa-comment"></i></th>
                            <th id='n3' style='display: none;'>NOTA</th>
                            <th id='v6'>DIFERENCIA</th>
                        </tr>
                    </thead>
                    <tbody id="ajuste">
                        <?php 
                            foreach($consolidados as $forpag){
                                    $ttvValSap = $ttvValSap + $forpag->Valor;
                                    $ttvRec = $ttvRec+ $forpag->valRec; 
                                    $ttvOnline = $ttvOnline + $forpag->valOnline; 
                                    $ttvPinPad = $ttvPinPad + $forpag->valPinpadOn;
                                    $ttvDatfast = $ttvDatfast +$forpag->valPinpadOff ; 

                        ?>
                        <tr>
                            <td style='display: none;'>
                                <input name="idcicasap[]" type="number"  value="<?php echo $forpag->id ?>" >
                            </td>
                            <td><input type="text" id="forPag" name="forPag[]" value="<?php echo $forpag->CardName ?>" readonly></td>
                            <td class="valSAP">  
                                <input type="number" id="valSAP" name="valSAP[]" value="<?php echo $forpag->Valor; ?>" readonly>
                            </td>


                            <?php
                            if ($cerrado==0) {
                            ?> 
                            <div style="width: 20%; float:left" class="input-group">
                                <td class="valRec">
                                    <input name="valRec[]" onkeyup="calc(this);" min="0" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valRec ?>" required >
                                </td>
                                <td class="valOnline">
                                    <input name="valOnline[]" onkeyup="calc(this);" min="0" step="any"  onchange="calc(this);" type="number"  value="<?php echo $forpag->valOnline ?>" required >
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
                                    <input name="valDatMed[]" onkeyup="calc(this);" min="0" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valPinpadOff ?>" required >
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
                                    echo '<input class="status" type="number" style="color:red;" name="Dif[]" value="'.$difz.'" readonly>';
                                } elseif ($difz>0) {
                                    echo '<input class="status" type="number" style="color:green;" name="Dif[]" value="'.$difz.'" readonly>';
                                }else{
                                    echo '<input class="status" type="number" name="Dif[]" value="'.$difz.'" readonly>';
                                }
                                ?>
                            </td>
                        
                        </tr>
                        <?php } ?>


                        <!--  $ttvValSap = $ttvValSap + $forpag->Valor;
                                    $ttvRec = $ttvRec+ $forpag->valRec; 
                                    $ttvOnline = $ttvOnline + $forpag->valOnline; 
                                    $ttvPinPad = $ttvPinPad + $forpag->valPinpadOn;
                                    $ttvDatfast = $ttvDatfast +$forpag->valPinpadOff ; 
                            -->
                        <tr class="table-secondary"><td>TOTAL:</td>
                            
                            <td>
                                <input type="text"  value="<?php echo $ttvValSap ?>" readonly>
                            </td>
                            <td>
                                <input type="text" value="<?php echo $ttvRec ?>" name="ttvRec" id="ttvRec" readonly>
                            </td>
                            <td>
                                <input type="text" value="<?php echo $ttvOnline ?>" name="ttvOnline" id="ttvOnline" readonly>
                            </td>
                            <td>
                                <input type="text"  value="<?php echo $ttvPinPad ?>" readonly>
                            </td>
                            <td>
                                <input type="text" value="<?php echo $ttvDatfast ?>" name="ttvDatfast" id="ttvDatfast" readonly>
                            </td>
                            <td></td>
                            <td></td>

                        </tr>

                      <!--      <td>' +  svalRec.toFixed(2) +
        '</td><td>' +  svalOnline.toFixed(2) +
        '</td><td>' +  svalPinpad.toFixed(2) +
        '</td><td>' +  svalMedianet.toFixed(2) +'</td></tr>';-->

                        
                    </tbody>
                </table>
             

        </div>
        <div class="card-footer">

        <?php
        if ($cerrado==0) {
        ?> 
            <button type="submit" class="btn btn-primary btn-lg">
            <i class="fa fa-save"></i> GUARDAR
            </button>
            <button type="reset" class="btn btn-danger btn-lg">
                <i class="fa fa-ban"></i> RESET
            </button>
            <button type="button" class="btn btn-secondary btn-lg" onClick=window.open("<?php echo "adjuntos.php?id=" . $id ?>","demo","toolbar=0,status=0,")>
                <i class="fa fa-paperclip"></i>&nbsp; ADJUNTOS
            </button>
        <?php
        }
        ?>
            



            <button type="button" class="btn btn-secondary btn-lg" onclick="window.location.href='cic.php?pFecha=<?php echo $fecha ?>&pIdAlmacen=<?php echo $fk_ID_almacen ?>'">
                <i class="fa fa-sign-out"></i>&nbsp; REGRESAR
            </button>
        </div>
            </form>

    </div>


    <?php if ($cerrado==1) { 
         $path = "films/" . $id;
        ?> 
    
        <div class="card">
            <div class="card-header"><strong>ADJUNTOS</strong></div>
            <div class="card-body card-block">
 
                <!--tabla-->
                <div class="panel panel-primary">
                   
                    <div class="panel-body">
                
                <table class="table">
                <thead>
                    <tr>
                    <th width="7%">#</th>
      <th width="70%">Nombre del Archivo</th>
      <th width="23%">Descargar</th>
                    </tr>
                </thead>
                <tbody>
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
      <th scope="row"><?php echo $num;?></th>
      <td><?php echo $archivos[$i]; ?></td>
      <td><a
      href="<?php echo $path . "/" . $archivos[$i]; ?>" download="<?php echo $archivos[$i]; ?>"
      > 💾  </a>  </td>
      </tr>
                <?php }?> 

                </tbody>
                </table>
                </div>
                </div>
                <!-- Fin tabla--> 
            </div>
        </div>

    <?php }    ?>






<style>
    input {
    border: none;
    background: transparent;
    }
</style>

<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>