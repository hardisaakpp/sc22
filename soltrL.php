<?php
    include_once "header.php";
    //si no es admin no abre
    if ($userAdmin!=1 && $userAdmin!=3  && $userAdmin!=5){
        echo ('<h4> NO TIENE ACCESO</h4>');
        
    }else{
        # code...
//COMPROBAR SI HAY VARIABLES o asigno por default
    $conteo = 0;
    $reconteo = 0;
    $cerrado = 0;
    $diferencias = 0;
    if (isset($_POST["conteo"])) {
        $conteo = $_POST["conteo"];
        if ($conteo=='on') {
            $conteo=1;
        }
    }
    if (isset($_POST["reconteo"])) {
        $reconteo = $_POST["reconteo"];
        if ($reconteo=='on') {
            $reconteo=1;
        }
    }
    if (isset($_POST["cerrado"])) {
        $cerrado = $_POST["cerrado"];
        if ($cerrado=='on') {
            $cerrado=1;
        }
    }
    if (isset($_POST["diferencias"])) {
        $diferencias = $_POST["diferencias"];
        if ($diferencias=='on') {
            $diferencias=1;
        }
    }

    $wheres = '';
    if ($conteo==1) {
        $wheres = $wheres." and INI>0 ";
    }
    if ($reconteo==1) {
        $wheres = $wheres." and REC>0 ";
    }
    if ($cerrado==1) {
        $wheres = $wheres." and FIN>0 ";
    }
    if ($diferencias==1) {
        $wheres = $wheres." and NOVEDADES>0 ";
    }

    if (!isset($_POST["desde"]) and !isset($_POST["hasta"]) )
    {
        $desde=Date('Y-m-d') ;
        $hasta=Date('Y-m-d') ;

    }else
    {
        $desde=$_POST['desde'];
        $hasta=$_POST['hasta'];
        
    }


   // echo $conteo;

// cabecerqa de toma actual


if ($conteo==0) {
    $sentencia = $db->prepare("
    select * 
    ,(select count(*) from StockScan scan where scan.fk_id_stockCab=c.fk_id_stockCab) as scans
    from StockCab_ST c
    WHERE c.fecha_sol between '".$desde."' and '".$hasta."' and c.estado='FIN'");

} else {
    $sentencia = $db->prepare("
    select *
    ,(select count(*) from StockScan scan where scan.fk_id_stockCab=c.fk_id_stockCab) as scans 
    from StockCab_ST c
    WHERE c.fecha_sol between '".$desde."' and '".$hasta."' and c.estado!='INI'");
}



  
    $sentencia->execute([]);
    
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
   
        ?>

<!-- Breadcrumbs
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMAS FISICAS ALEATORIAS</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">🔃</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php';">X</button>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
<!-- /.breadcrumbs-->

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->
    
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            
        <form id="monthformX"  method="post" action="">
            <div class="input-group">
                Rango fecha
                <input type="date" name="desde" id="desde" class="form-control" value="<?php echo $desde ?>" required>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo $hasta ?>" required>

            <?php 
                if ($conteo==1) {
                    echo "<label>.  Mostrar Impresos<input type='checkbox' name='conteo' checked ></label>";
                } else {
                    echo "<label>.  Mostrar Impresos<input type='checkbox' name='conteo'></label>";
                }
                /*
                if ($reconteo==1) {
                    echo "<label>.  Reconteo<input type='checkbox' name='reconteo' checked ></label>";
                } else {
                    echo "<label>.  Reconteo<input type='checkbox' name='reconteo'></label>";
                }
                if ($cerrado==1) {
                    echo "<label>.  Cerrado<input type='checkbox' name='cerrado' checked ></label>";
                } else {
                    echo "<label>.  Cerrado<input type='checkbox' name='cerrado'></label>";
                }
                if ($diferencias==1) {
                    echo "<label>.  Diferencia<input type='checkbox' name='diferencias' checked ></label>";
                } else {
                    echo "<label>.  Diferencia<input type='checkbox' name='diferencias'></label>.   .";
                }*/
            ?>


                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="soltrL.php">	
            </div>
        </form>



        </div>
    </div>
</div>
    
<!---------------------------------------------->

<?php
 if (count($rows)==0) {
    echo ('<h4> ¡No existen registros! </h4>');
} else {
  

    ?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">IMPRESION DE ETIQUETAS</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>ID</th>
                                    <th>SOLICITUD</th>
                                    <th>FECHA</th>
                                    <th>ORIGEN</th>
                                    <th>DESTINO</th>
                                    <th>TRANSFERENCIA</th>
                                    <th>CARTONES</th>
                                    <th>SCANS</th>
                                    <th>ESTADO</th>
                                    <th></th>
                                </tr>
                </thead>
                <tbody>
                <?php   
                
                $totalscans=0;
                
                foreach($rows as $citem){ 
                    $totalscans=$totalscans+$citem->scans;?>


                    <tr>
                                    <td><?php echo $citem->fk_id_stockCab ?></td>
                                    <td><?php echo $citem->solicitud ?></td>
                                    <td><?php echo $citem->fecha_sol ?></td>
                                    <td><?php echo $citem->origen ?></td>
                                    <td><?php echo $citem->destino ?></td>
                                    <td><?php echo $citem->transferencia ?></td>
                                    <td><?php echo $citem->cartones ?></td>
                                    <td><?php echo $citem->scans ?></td>
                                    <td><?php 
                                    if ($citem->estado=='FIN') {
                                        echo 'CERRADO';
                                    } else {
                                        echo 'IMPRESO';
                                    }
                                    
                                    
                                    
                                     ?></td>
                                    <td>
                                        <?php

                                        if ($citem->transferencia>0) {
                                            echo "<input type='checkbox'/>  ";
                                        }else {
                                            echo "<input type='checkbox' onclick='return false' />  ";
                                        }
                                        

                                        ?>
                                      
                                <!--    <button type="button" class="btn btn-outline-success" onclick=window.open("<?php echo 'tfaDprintAdm.php?idcab=' . $citem->id_cab ?>","demo","toolbar=0,status=0,");> 👁️‍🗨️ </button>  -->
                                </td>
                            </tr>
                   
                <?php } ?>   
                </tbody>
            </table>

            Scans:  <?php echo $citem->scans ?>
            <input type="button" value="🖨️ Etiquetas" onclick="GetSelected()" />
            <input type="button" value=" Refrescar #Transferencias" onclick="GetSelectedTrans()" />
        </div>
    </div>
</div>


<!--
<a class="btn btn-primary btn-sm" style="color:white" onClick=window.open("<?php echo "php/transfPrint22.php?id=" . $solicitud . "&cc="?>"+document.getElementById('cc').value,"demo","toolbar=0,status=0,"); >Etiquetas</a>
                                    -->

<script type="text/javascript">
    function GetSelected() {
        //Reference the Table.
        var grid = document.getElementById("bootstrap-data-table");
 
        //Reference the CheckBoxes in Table.
        var checkBoxes = grid.getElementsByTagName("INPUT");
        var message = "";
 
        //Loop through the CheckBoxes.
        for (var i = 0; i < checkBoxes.length; i++) {
            if (checkBoxes[i].checked) {
                var row = checkBoxes[i].parentNode.parentNode;
                message += row.cells[1].innerHTML;   //solicitud
              //  message += "-" + row.cells[6].innerHTML;   //cartones
                
                
               
               // message += "   " + row.cells[5].innerHTML;
               
                message += ",";
           
             //   window.open("<?php echo "php/transfPrint22.php?id="?>" + row.cells[1].innerHTML + "<?php echo  "&cc="?>"+row.cells[6].innerHTML,"demo","directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no");  
            }
            

        }
  


if (message.length>0) {
    //alert(message);
    message = message.substring(0, message.length - 1);
    window.open("<?php echo 'php/transfPrint23.php?cc='?>" + message ,"demo","directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no");

} 
          
               
       
        //window.open('/pageaddress.html','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=350');

        //Display selected Row data in Alert Box.
       // alert(message);
    }

    function GetSelectedTrans() {
        //Reference the Table.
        var grid = document.getElementById("bootstrap-data-table");
 
        //Reference the CheckBoxes in Table.
        var checkBoxes = grid.getElementsByTagName("INPUT");
        var message = "";
 
        //Loop through the CheckBoxes.
        for (var i = 0; i < checkBoxes.length; i++) {
            if (!checkBoxes[i].checked) {
                var row = checkBoxes[i].parentNode.parentNode;
              
                if (row.cells[5].innerHTML==0) {
                    message += row.cells[1].innerHTML;   //solicitud
              //  message += "-" + row.cells[6].innerHTML;   //cartones
                
                
              //<a class='btn btn-secondary btn-sm' href='refreshSoltrT.php?id=".$solicitud."'>🔁</a>
               // message += "   " + row.cells[5].innerHTML;  //transferencia
               
                message += ",";
           
}
                


             //   window.open("<?php echo "php/transfPrint22.php?id="?>" + row.cells[1].innerHTML + "<?php echo  "&cc="?>"+row.cells[6].innerHTML,"demo","directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no");  
            }
            

        }
  


if (message.length>0) {
    //alert(message);
    message = message.substring(0, message.length - 1);
    //alert(message);
    location.href ="<?php echo 'refreshSoltrTransf.php?id='?>" + message;
    //window.open("<?php echo 'php/transfPrint23.php?cc='?>" + message ,"demo","directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no");

} 
          
               
       
        //window.open('/pageaddress.html','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=350');

        //Display selected Row data in Alert Box.
       // alert(message);
    }
</script>



    <?php
        }
    }
    ?>


<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  

 
  
include_once "footer.php"; ?>