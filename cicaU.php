<?php
include_once "header.php";
   

    //si no es ADMIN o no se  no abre
if($whsCica==0){  
        echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
}elseif( !isset($_GET["id"])){  
    echo ('...');
}else{
    $id =  $_GET["id"];

//echo $id;

    $sentencia2 = $db->query("
    select * from CiCa where id=".$id."  "  );
    $TEMP1 = $sentencia2->fetchObject();

        $caja = $TEMP1->caja;
       // $Items = $TEMP1->items;
       // $cantidad = $TEMP1->cantidad;
        $fecha = $TEMP1->fecha;
        $Cerrado = $TEMP1->cerrado;
        $responsable = $TEMP1->responsable;
        $observacion = $TEMP1->observacion;

     //   echo $caja . $fecha  . $Cerrado;


    $s1 = $db->query("
    select
       *

    from cicasap  where caja='". $caja ."' and fecha='".$fecha."' " );
    $consolidados = $s1->fetchAll(PDO::FETCH_OBJ);   

       
?>



<script type="text/javascript">

    $(document).ready(function() {
        $('#n1').click(function() {
            var n11 = 7;
            $("td:nth-child(" + n11 + "),th:nth-child(" + n11 + ")").toggle();
            
        });
        $('#n2').click(function() {
            $('td:nth-child(9),th:nth-child(9)').toggle();
        });
        $('#n3').click(function() {
            $('td:nth-child(11),th:nth-child(11)').toggle();
        });
    });

  

    function calc(id) {
        var row=id.parentNode.parentNode;
        //alert("Cell index is: " + ( document.getElementById('valSAP')).cellIndex);
        
        var valSAP=row.cells[( document.getElementById('v1')).cellIndex].getElementsByTagName('input')[0].value;
        var recibido=row.cells[( document.getElementById('v2')).cellIndex].getElementsByTagName('input')[0].value;
        var online=row.cells[( document.getElementById('v3')).cellIndex].getElementsByTagName('input')[0].value;
        var pinpad=row.cells[( document.getElementById('v4')).cellIndex].getElementsByTagName('input')[0].value;
        var dataf=row.cells[( document.getElementById('v5')).cellIndex].getElementsByTagName('input')[0].value;
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
    };

</script>


<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

    <div class="col-md-12">
        
       

    <div class="card">
        
        <div class="card-header">
            <strong> <?php echo $caja."  [".$fecha."]" ?> </strong>         
        </div>
        <div class="card-body card-block">
            <form action="" id="frmConteo" method="post">    
                <!---------cabecera------------>
                <div style="width: 20%; float:left" class="input-group">
                    <input class="form-control" placeholder="Responsable" name='crespons' type="text" maxlength="49" value="<?php echo $responsable; ?>">
                </div>
                <div style="width: 80%; float:right">
                    <input class="form-control" placeholder="Observaciones" name='cobs' maxlength="400" type="text" value="<?php echo $observacion; ?>">
                </div>
                <!---------grid------------>
                <table id="resumentbl" class="table table-hover">
                    <thead class="thead-dark">
                        <tr>

                            <th>FORMA PAGO</th>
                            <th id='v1'>VALOR.SAP</th>
                            <th id='v2'>RECIBIDO</th>
                            <th id='v3'>ONLINE <i id='n1' aria-hidden="true" class="fa fa-comment"></i></th>
                            <th id='n1' style='display: none;'>NOTA</th>
                            <th id='v4'>PINPAD <i id='n2' aria-hidden="true" class="fa fa-comment"></i></th>
                            <th id='n2' style='display: none;'>NOTA</th>
                            <th id='v5'>DATAFAST/MEDIANET <i id='n3' aria-hidden="true" class="fa fa-comment"></i></th>
                            <th id='n3' style='display: none;'>NOTA</th>
                            <th id='v6'>DIFERENCIA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach($consolidados as $forpag){
                        ?>
                        <tr>
                            <td><input type="text" id="valSAP" name="forPag[]" value="<?php echo $forpag->CardName ?>" readonly></td>
                            <td class="valSAP">  
                                <input type="number" id="valSAP" name="valSAP[]" value="<?php echo $forpag->Valor; ?>" readonly>
                            </td>
                            <td class="valRec">
                                <input name="valRec[]" onkeyup="calc(this);"  step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valRec ?>" >
                            </td>
                            <td class="valOnline">
                                <input name="valOnline[]" onkeyup="calc(this);" step="any"  onchange="calc(this);" type="number"  value="<?php echo $forpag->valOnline ?>" >
                            </td>
                            <td style='display: none;'>
                                <input name="refOnline[]" type="text"  value="<?php echo $forpag->refOnline ?>" >
                            </td>
                            <td class="valPinpad">
                                <input name="valPinPad[]"  onkeyup="calc(this);" step="any" type="number" value="<?php echo $forpag->valPinpadOn ?>"  readonly>
                            </td>
                            <td style='display: none;'>
                                <input name="refPinPad[]" type="text"  value="<?php echo $forpag->refPinpadOn ?>" >
                            </td>
                            <td class="valMedianet">
                                <input name="valDatMed[]" onkeyup="calc(this);" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valPinpadOff ?>" >
                            </td>
                            <td style='display: none;'>
                                <input name="refDatMed[]" type="text"  value="<?php echo $forpag->refPinpadOff ?>" >
                            </td>
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
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-dot-circle-o"></i> Submit
            </button>
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </div>
            </form>

    </div>


<style>
    input {
    border: none;
    background: transparent;
}
    </style>


        <?php
    
?>


  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>