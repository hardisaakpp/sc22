<?php
include_once "header.php";
   

    //si no es ADMIN o no se  no abre
if( !isset($_GET["idcaja"])){  
    echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
}else{
    $whsCica = $_GET["pW"];
    $pFecha = $_GET["pF"];
    $idcaja =  $_GET["idcaja"];


    $s1 = $db->query("
    select * from cicasys where FK_idCiCa=".$idcaja."  " );
    $consolidados = $s1->fetchAll(PDO::FETCH_OBJ);   

   $sentencia = $db->query("      
   select * from cica  where id='". $whsCica ."' " );
    $caja = $sentencia->fetchAll(PDO::FETCH_OBJ);
       
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




<!-- Widgets  

<div class="row">

<?php   foreach($cajas as $user){ ?>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-3">
                    <a href="cicaU.php?idcaja=<?php echo $user->id?>">
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

    

</div>-->
<!------------------------------------------------------------------------------------------------>
<!------------------------------------------------------------------------------------------------>



<!------------------------------------------------------------------------------------------------>

    <!--//conteo-->
    <div class="col-md-12">
        
       

        <div class="card">
            <form action="" id="frmConteo" method="post">
                <div class="card-header">
                    <div class="input-group">
                    <label class="form-control" >
                        <blockquote class="blockquote"><strong>
                        <?php echo $pFecha ?> CIERRE DE CAJA CONSOLIDADO
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
                    <td><input type="text" id="valSAP" name="forPag[]" value="<?php echo $forpag->forPag ?>" readonly></td>
                        <td class="valSAP">  
                            <input type="number" id="valSAP" name="valSAP[]" value="<?php echo $forpag->valSAP; ?>" readonly>
                        </td>
                        <td class="valRec">
                            <input name="valRec[]" onkeyup="calc(this);"  step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valRec ?>" >
                        </td>
                        <td class="valOnline">
                            <input name="valOnline[]" onkeyup="calc(this);" step="any"  onchange="calc(this);" type="number"  value="<?php echo $forpag->valOnline ?>"" >
                        </td>
                        <td style='display: none;'>
                            <input name="refOnline[]" type="text"  value="<?php echo $forpag->refOnline ?>" >
                        </td>
                        <td class="valPinpad">
                            <input name="valPinPad[]"  onkeyup="calc(this);" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valPinPad ?>" >
                        </td>
                        <td style='display: none;'>
                            <input name="refPinPad[]" type="text"  value="<?php echo $forpag->refPinPad ?>" >
                        </td>
                        <td class="valMedianet">
                            <input name="valDatMed[]" onkeyup="calc(this);" step="any" onchange="calc(this);" type="number"  value="<?php echo $forpag->valDatMed ?>" >
                        </td>
                        <td style='display: none;'>
                            <input name="refDatMed[]" type="text"  value="<?php echo $forpag->refDatMed ?>" >
                        </td>
                        <td class="Diferencia"> 
                            <input type="text" id="valSAP" name="Dif" value="<?php echo  $forpag->valRec+$forpag->valOnline+$forpag->valPinPad+$forpag->valDatMed-$forpag->valSAP ?>" readonly>
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
      
            console.log(id);
            var row=id.parentNode.parentNode;

            console.log(row);
            var quant=row.cells[1].getElementsByTagName('input')[0].value;
            var price=row.cells[2].getElementsByTagName('input')[0].value;
            var disc=row.cells[3].getElementsByTagName('input')[0].value;
            console.log(quant);
            console.log(price);
            console.log(disc);
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