<?php
    include_once "header.php";
    //si no es admin no abre
    if($whsInvs==0){
        echo ('<h4> NO TIENE ASIGNADO UN ALMACEN PARA REALIZAR INVENTARIO</h4>');
        
    }else{
        # code...
    
// cabecerqa de toma actual
    $sentencia = $db->prepare("SELECT top 1 *  from StockCab
            where convert(varchar(10), [date], 102) = convert(varchar(10), getdate(), 102) and  tipo='TF'
            and FK_ID_almacen= ? ");
    $sentencia->execute([$whsInvs]);
    
    $rows = $sentencia->fetchAll();
    
    if (count($rows)==0) {
        echo ('<div class="alert alert-danger" role="alert"> ¡No se ha generado toma física para el día de hoy 😱! </div>');
    } else {


   



    //cabecera 
        $sent = $db->prepare("SELECT top 1 s.* , tfa.responsable
            from StockCab s left join StockCab_tfa tfa on s.id=tfa.fk_id_StockCab
            where convert(varchar(10), [date], 102) = convert(varchar(10), getdate(), 102) and  tipo='TF'
                    and FK_ID_almacen= ? ");
        $sent->execute([$whsInvs]);

        $result = $sent->fetch(PDO::FETCH_OBJ);
            $creado =$result->date . $result->time;
            $id_cab= $result->id;
            $responsable= $result->responsable;
            $tomaCode = $result->tomaCode;
    //detalle toma actual INICIAL-CONTEO
        $s1 = $db->prepare("select det.id, ID_articulo, descripcion, nombreGrupo, stock, scan, conteo, reconteo, estado 
            from stockdet det join Articulo a on det.FK_ID_articulo=a.id
            where estado= 'INI' and FK_id_StockCab= ? order by nombreGrupo,ID_articulo" );
            $s1->execute([$id_cab]);
        $citems = $s1->fetchAll(PDO::FETCH_OBJ);            
    //detalle toma actual RECONTEO
        $s2 = $db->prepare("select det.id, ID_articulo, descripcion, nombreGrupo, stock, scan, conteo, reconteo, estado 
            from stockdet det join Articulo a on det.FK_ID_articulo=a.id
            where estado= 'REC' and FK_id_StockCab= ? order by nombreGrupo,ID_articulo" );
            $s2->execute([$id_cab]);
        $reconteos = $s2->fetchAll(PDO::FETCH_OBJ);  
    //detalle toma actual FIN
        $s3 = $db->prepare("select det.id, ID_articulo, descripcion, nombreGrupo, stock, scan, conteo, reconteo, estado 
            from stockdet det join Articulo a on det.FK_ID_articulo=a.id
            where estado= 'FIN' and FK_id_StockCab= ? order by nombreGrupo,ID_articulo" );
            $s3->execute([$id_cab]);
        $cerrados = $s3->fetchAll(PDO::FETCH_OBJ);           
    

 //log ingreso
 $sentencia = $db->prepare("
 INSERT INTO [dbo].[StockLog] ([fk_id_stockCab] ,[id_user] ,[accion])
     VALUES
         (?
         ,?
         ,'Consulta TFA diaria');");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute([$id_cab, $userId]);





        ?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMA FISICA: <?php echo $id_cab ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-success" onclick=window.open("<?php echo 'tfaDprint.php?idcab=' . $id_cab ?>","demo","toolbar=0,status=0,");> 🖨️ </button> 
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


<?php
    if (count($citems)>0) {
        ?>

    <!--//conteo-->
    <div class="col-md-12">
        <div class="card">
        <form action="" id="frmConteo" method="post">
            <div class="card-header">
            <strong class="card-title">CONTEO DE ARTICULOS ❗</strong>
                            
                        
                     
                        
                        <div style="float: right;">
                            
                            <div id="contentx" > </div>
                            <input type="text" placeholder="Ingrese responsable" name="responsable" id="responsableC" value="<?php echo $responsable ?>" required>
                            <input type="text" name="estado" value="CONTEO" hidden>
                            <input type="text" name="idcab" value="<?php echo $id_cab ?>" hidden>
                            <input type="submit" class="btn btn-outline-success" value='Enviar CONTEO ►' onclick=this.form.action="php/tfaSave.php"> 
                            <button type="button" class="btn btn-outline-success" onclick="saveTFA('INI');"> 💾 </button>
                            
                        </div>  
            </div>
                    

                        
                        <table id="bootstrap-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style='display: none;'>ID</th>
                                    <th>ItemCode</th>
                                    <th>Descripcion</th>
                                    <th>Grupo</th>

                                    <th>Conteo</th>

                                </tr>
                            </thead>
                            <tbody>
                            <?php   foreach($citems as $citem){ ?>


                                <tr>
                                    <td style='display: none;'><input type="text" id="id" name="id[]" value="<?php echo $citem->id; ?>" readonly></td>
                                    <td><?php echo $citem->ID_articulo ?></td>
                                    <td><?php echo $citem->descripcion ?></td>
                                    <td><?php echo $citem->nombreGrupo ?></td>
                                    <td>
                                        
                                        
                                        <input  name="conteo[]" value="<?php echo $citem->conteo ?>" id="<?php echo 'co'.$citem->id ?>" class="campo-numerico" type="number" min="0" pattern="^[0-9]+" onpaste="return false;" onDrop="return false;" autocomplete=off onchange="this.style.backgroundColor = '#E7FD09'" required>    
                                 
                                    </td>
                                </tr>
                            
                            <?php }; ?>   
            
                            </tbody>

                        </table>
                    </form> 

        </div>
    </div>


        <?php
    }
?>


<?php
    if (count($reconteos)>0) {
        ?>
    <!--//reconteo-->
    <div class="col-md-12">
        <div class="card">
        <form action="" id="frmConteo" method="post" >
            <div class="card-header">
            <strong class="card-title">RECONTEO DE ARTICULOS ‼️</strong>
                            
                        <div style="float: right;">
                            
                            <div id="contentx" > </div>
                            <input type="text" placeholder="Ingrese responsable" name="responsable" id="responsableR" value="<?php echo $responsable ?>"  required>
                            <input type="text" name="estado" value="RECONTEO" hidden>
                            <input type="text" name="idcab" value="<?php echo $id_cab ?>" hidden>
                            <input type="submit" class="btn btn-outline-success" value='Enviar RECONTEO ►' onclick=this.form.action="php/tfaSave.php"> 
                            <button type="button" class="btn btn-outline-success" onclick="saveTFA('REC');"> 💾 </button>

                        </div>  
            </div>
                    

                        
                        <table id="bootstrap-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style='display: none;'>ID</th>
                                    <th>ItemCode</th>
                                    <th>Descripcion</th>
                                    <th>Grupo</th>

                                    <th>Re-Conteo</th>

                                </tr>
                            </thead>
                            <tbody>
                            <?php   foreach($reconteos as $citem){ ?>


                                <tr>
                                    <td style='display: none;'><input type="text" id="id" name="id[]" value="<?php echo $citem->id; ?>" readonly></td>
                                    <td><?php echo $citem->ID_articulo ?></td>
                                    <td><?php echo $citem->descripcion ?></td>
                                    <td><?php echo $citem->nombreGrupo ?></td>
                                    <td>
                                        
                                        
                                        <input  name="conteo[]" value="<?php echo $citem->reconteo ?>" id="<?php echo 'co'.$citem->id ?>" class="campo-numerico" type="number" min="0" pattern="^[0-9]+" onpaste="return false;" onDrop="return false;" autocomplete=off onchange="this.style.backgroundColor = '#E7FD09'">    
                                 
                                    </td>
                                </tr>
                            
                            <?php }; ?>   
            
                            </tbody>

                        </table>
                    </form> 

        </div>
    </div>

    <?php
        }
    ?>
    <?php
        if (count($cerrados)>0) {
    ?>
    <!--//cerrados-->
    <div class="col-md-12">
        <div class="card">
        <form action="cicaSave.php" method="post" >
            <div class="card-header">
            <strong class="card-title">ARTICULOS ✔️</strong>
                            
            
            </div>
                    
                        <table id="bootstrap-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style='display: none;'>ID</th>
                                    <th>ItemCode</th>
                                    <th>Descripcion</th>
                                    <th>Grupo</th>

                                    <th>Conteo</th>

                                </tr>
                            </thead>
                            <tbody>
                            <?php   foreach($cerrados as $citem){ ?>


                                <tr>
                                    <td style='display: none;'><input type="text" id="id" name="id[]" value="<?php echo $citem->id; ?>" readonly></td>
                                    <td><?php echo $citem->ID_articulo ?></td>
                                    <td><?php echo $citem->descripcion ?></td>
                                    <td><?php echo $citem->nombreGrupo ?></td>
                                    <td >
                                        <?php echo $citem->reconteo ?>
                                    </td>
                                </tr>
                            
                            <?php }; ?>   
            
                            </tbody>

                        </table>
                    </form> 

        </div>
    </div>


    <?php
        }
    ?>

<script> 


document.getElementById("frmConteo").onsubmit = function() {
   
    if (confirm("¿Seguro de enviar?")) {
        saveTFA('INI');
        $(".loader-page").css({visibility:"visible",opacity:"0.8"});
        return true;
        } else {
        return false;
        }
};

document.addEventListener('keydown', function(evento) {
  const elemento = evento.target;
  if (elemento.className === 'campo-numerico') {
    const teclaPresionada = evento.key;
    const teclaPresionadaEsUnNumero =
      Number.isInteger(parseInt(teclaPresionada));

    const sePresionoUnaTeclaNoAdmitida = 
      teclaPresionada != 'ArrowDown' &&
      teclaPresionada != 'ArrowUp' &&
      teclaPresionada != 'ArrowLeft' &&
      teclaPresionada != 'ArrowRight' &&
      teclaPresionada != 'Backspace' &&
      teclaPresionada != 'Delete' &&
      teclaPresionada != 'Enter' &&
      teclaPresionada != 'Tab' &&
      !teclaPresionadaEsUnNumero;
    const comienzaPorCero = 
      elemento.value.length === 0 &&
      teclaPresionada == 0;
   //   if (e.keyCode == 8) return true;
    if (sePresionoUnaTeclaNoAdmitida || comienzaPorCero) {
      evento.preventDefault(); 
    }
  }
});

    


function saveTFA(ESTADO){

    if (ESTADO=='INI') { 
        re=document.getElementById('responsableC');
        savedResp(re.value,'<?php echo $id_cab ?>');
        <?php
            foreach($citems as $use1){ 
        ?>  
                es=document.getElementById('<?php echo "co".$use1->id ?>');
                savedTFA('<?php echo $use1->id ?>', es.value);
        <?php 
            }; 
        ?>
    } else {
        re=document.getElementById('responsableR');
        savedResp(re.value,'<?php echo $id_cab ?>');
        <?php
            foreach($reconteos as $use1){ 
        ?>  
                es=document.getElementById('<?php echo "co".$use1->id ?>');
                console.log('<?php echo $use1->id ?>'+ es.value+ 'REC');
                savedTFA('<?php echo $use1->id ?>', es.value);
        <?php 
            }; 
        ?>
    };
}

function savedResp(responsable,idcab) {

var parametros = 
    {
        "responsable" : responsable ,
        "idcab" : idcab
    };

    $.ajax({
        data: parametros,
        url: 'php/tfaSaveResp.php',
        type: 'POST',
        timeout: 3000,
        success: function(){
            console.log('ok');
          // es=document.getElementById("co"+WhsCode);
          // es.style.backgroundColor = "#DDF1D2";
        },
        error: function(){
           //es=document.getElementById("co"+WhsCode);
           //es.style.backgroundColor = "#DDF1D2"; 
           console.log('error de conexion - revisa tu red');
        }
    });
}

function savedTFA(WhsCode, Quantity) {

 var parametros = 
     {
         "WhsCode" : WhsCode ,
         "Quantity" : Quantity
     };

     $.ajax({
         data: parametros,
         url: 'php/tfaModify.php',
         type: 'POST',
         timeout: 3000,
         success: function(){
            // console.log(data);
            es=document.getElementById("co"+WhsCode);
            es.style.backgroundColor = "#DDF1D2";
         },
         error: function(){
            es=document.getElementById("co"+WhsCode);
            es.style.backgroundColor = "#DDF1D2"; 
            //console.log('error de conexion - revisa tu red');
         }
     });
}

</script> 

<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  

}; }; 
  
include_once "footer.php"; ?>