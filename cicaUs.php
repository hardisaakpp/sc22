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

     //   echo $caja . $fecha  . $Cerrado;


    $s1 = $db->query("
    select
       *

    from cicasap  where caja='". $caja ."' and fecha='".$fecha."' " );
    $consolidados = $s1->fetchAll(PDO::FETCH_OBJ);   

       
?>


<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

    <div class="col-md-12">
        
       

        <div class="card">
            <form action="" id="frmConteo" method="post">
                <div class="card-header">
                    <div class="input-group">
                    <label class="form-control" >
                        <blockquote class="blockquote"><strong>
                        <?php echo $fecha." - ".$caja ?> 
                        </blockquote></strong>
                    </label>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">Button</button>
                        <button class="btn btn-outline-secondary" type="button">Button</button>
                    </div>
                    </div>
                
                        
                </div>
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
                            <input type="number" id="valSAP" name="Dif[]" value="<?php echo  $forpag->valRec+$forpag->valOnline+$forpag->valPinpadOn+$forpag->valPinpadOff-$forpag->Valor ?>" readonly>
                        </td>
                    
                </tr>
            <?php } ?>
                    
                </tbody>
            </table>
            </form> 

        </div>



    </div>


<style>
    input {
    border: none;
    background: transparent;
}
    </style>

    <script language="javascript" type="text/javascript">
        var tds = document.getElementById('resumentbl').getElementsByTagName('td');
        var sum = 0.0;
        var svalRec = 0.0;
        var svalOnline = 0.0;
        var svalPinpad = 0.0;
        var svalMedianet = 0.0;
        for(var i = 0; i < tds.length; i ++) {
            console.log('ok');
            console.log(tds[i].className);
            console.log(tds[i].getElementsByTagName('input')[0].value);

            if(tds[i].className == 'valSAP') {
                sum += isNaN(tds[i].getElementsByTagName('input')[0].value) ? 0 : parseFloat(tds[i].getElementsByTagName('input')[0].value);
                //sum += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
               // console.log('ok');
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

        function calc(id) {
      
         //console.log(id);
            var row=id.parentNode.parentNode;
          //console.log(row);
        

            //celdas de ROW
            var valSAP =row.cells[1].getElementsByTagName('input')[0].value;
            var valPinpadOn =row.cells[4].getElementsByTagName('input')[0].value;

            var valRec =row.cells[2].getElementsByTagName('input')[0].value;
            var valOnline =row.cells[3].getElementsByTagName('input')[0].value;
            var valPinpadOff =row.cells[5].getElementsByTagName('input')[0].value;

            //encerando
            if (valRec == null || valRec == '') { row.cells[2].getElementsByTagName('input')[0].value=0.00; valRec = 0.00;}
            if (valOnline == null || valOnline == '') { row.cells[3].getElementsByTagName('input')[0].value=0.00;valOnline =0.00; }
            if (valPinpadOff == null || valPinpadOff == '') { row.cells[5].getElementsByTagName('input')[0].value=0.00; valPinpadOff=0.00;}


            
            //actualizo total
            var difx= parseFloat(valPinpadOff)+parseFloat(valOnline)+parseFloat(valRec)+parseFloat(valPinpadOn)-parseFloat(valSAP);
console.log(difx);

row.cells[( document.getElementById('v6')).cellIndex].getElementsByTagName('input')[0].value=difx;


           // var diff =row.cells[6].getElementsByTagName('input')[0].value;

         //   diff=parseFloat(valRec)+parseFloat(valOnline);

        /*
            console.log(quant);
            console.log(price);
            console.log(disc);
        */
            /*
            if(disc==null || disc=='') {
            res=parseFloat(quant)*parseFloat(price);
            } else {
            var res=(parseFloat(quant)*parseFloat(price))-(parseFloat(quant)*parseFloat(price)*(parseFloat(disc)/100));
            }
            row.cells[4].getElementsByTagName('input')[0].value=res;
            */





        }
       



    </script>




        <?php
    
?>


  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>