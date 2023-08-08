<?php
include_once "header.php";
?>



<!-- -----------------------------------------------------------------------------------------------------------
        --------------------------------------------------------------------------------------------------------------
        --------------------------------------------------------------------------------------------------------------
        ------------------------------------------------------------------------------------------------------------------>

        <div>

            <form class="form-inline" method="GET" >
            
                <input type="text" class="form-control mb-2 mr-sm-2" 
                id="sx"  name="sx"  placeholder="NumDoc" value="" required>
                <input type="submit" name="find" id="find" value="Buscar 🔎" class="form-control"
                    onclick=this.form.action="soltr.php">

            </form>
        </div>
        <h3 style="color:gray";>Solicitud de Translado:</h3>
            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ------------------------------------------------------------------------------------------------------------------>
            <div class="content">

<?php
//validar que es ADMINISTRADOR
if($userAdmin==5 || $userAdmin==1)
{
    //verifico si recibi numero de translado
    if (!isset($_GET["sx"])){
            $solicitud=(0);
        }else{
            $solicitud=$_GET['sx'];
            //SP para buscar la solicitud si existe o no 
            $sentencia3 = $db->query("
                exec [sp_getSolTr] ". $solicitud ."  " );
            $soltra = $sentencia3->fetchObject();
                $res = $soltra->RES;
            // si se encontro la solicitud de pedido
            if ($res==0) {
                echo "<h3 style='color: RED';> No se encontro el numero de solicitud.</h3>
                 <p style='color: GRAY';> (Solo puede visualizar datos del año actual)</p>";
                   
            }else {
                $sentencia = $db->query("
                    select * from StockCab_ST st 
                    join StockCab sc on st.fk_id_stockCab=sc.id 
                    where st.solicitud= ". $solicitud ."  " );
                $cab = $sentencia->fetchObject();
                    //campos STOCK_CAB
                    $fecROW = $cab->date.' '.substr($cab->time,0,5);
                    $id_cab = $cab->id;
                    //campos ST
                    $fechaSol = $cab->fecha_sol;
                    $origen = $cab->origen;
                    $transferencia = $cab->transferencia;
                    $destino = $cab->destino;
                
                $txtTransfer = '';
                if($transferencia==0){
                    $txtTransfer="bg-warning text-dark'> Nro.Transferencia aún NO generado";
                }else{
                    $txtTransfer="bg-light'> Transferencia Nro.".$transferencia;
                }

                echo "
                <div class='container'>
                <div class='row row-cols-2 row-cols-lg-6 g-2 g-lg-3'>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Solicitud No.".$solicitud."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'> Fecha solicitud ".$fechaSol."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Origen: ".$origen."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Destino: ".$destino."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border  ".$txtTransfer."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Actualización: ".$fecROW."</div>
                    </div>
                </div>
                </div>";

             
           
            ?>

            <div class="row">

            
            <!-- -----------------------------------------------------------------------------------------------------------
            -------------------MENU--PESTAÑAS---------------------------------------
            ------------------------------------------------------------------------------------------------------------------>
            <ul class="nav nav-tabs nav-justified nav-dark">
            <li class="nav-item">
                <a class="nav-link active" href="#">Resumen</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo "soltrR.php?sx=" . $solicitud?>">Escanear</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo "soltrDel.php?sx=" . $solicitud?>">Eliminar</a>
            </li>
            </ul>
            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ----------------RESUMEN-------------------------------------------------------------------->

            
            <div class="col-12">

<div ALIGN="right" >  
            Número de cartones    
          <input type="number" id="cc" name="cc" style="border: 0; outline: none; width: 45px;" value="1">      
<!--<input type=button class="btn btn-success" onClick=window.open("<?php echo "transfPrint22.php?id=" . $solicitud . "&cc="?>"+document.getElementById('cc').value,"demo","toolbar=0,status=0,"); value="Etiquetas"> -->
<a class="btn btn-primary btn-sm" style="color:white" onClick=window.open("<?php echo "php/transfPrint22.php?id=" . $solicitud . "&cc="?>"+document.getElementById('cc').value,"demo","toolbar=0,status=0,"); >Etiquetas</a>
<a class="btn btn-secondary btn-sm" href="<?php echo "refreshSoltr.php?id=" .$solicitud ?>">Actualizar</a>
</div>    



            <form method='post' action='download.php'>
                <div ALIGN="right" >  
                
            </div>    
                    <div class="table-responsive">
                        <table class="table table-bordered display" id="example" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    
                            
                                <th  style='display:none;'>ID</th>
                                <th>CODIGO</th>
                                <th>DESCRIPCION</th>
                                <th>GRUPO</th>
                                <th>CODIGO BARRAS</th>
                                <th>STOCK</th>
                                <th>CONTEO</th>
                                <th>DIFERENCIA</th>

                                
                                </tr>
                            </thead>
                            <tbody>

         <?php

            $sentenci = $db->query("

            select 
            q1.id, q1.ID_articulo, q1.descripcion, q1.nombreGrupo
                ,isnull(q1.stock,0) as 'stock'
            ,isnull(q1.codigoBarras,q2.barcode) as 'codigoBarras'
            ,   ISNULL(q2.scans, 0) as 'scans' 
            ,  (ISNULL(q2.scans, 0) - isnull(q1.stock,0)) as 'diff'
            from
                (select a.id,a.ID_articulo, a.descripcion, 
                            a.nombreGrupo, a.codigoBarras, det.stock
                    from StockDet det
                        join Articulo a on det.FK_ID_articulo=a.id
                    where FK_id_StockCab=". $id_cab .") q1
                full outer join 
                        (select *
                        from vw_stockscan_count s 
                        where id_cab=". $id_cab .") q2 on q1.codigoBarras=q2.barcode " );
            $solitems = $sentenci->fetchAll(PDO::FETCH_OBJ);
            foreach ($solitems as $solitem) {
                ?>
                <tr>
                    <td  style='display:none;'><?php echo $solitem->id; ?></td>
                    <td><?php echo $solitem->ID_articulo; ?></td>
                    <td><?php echo $solitem->descripcion; ?></td>
                    <td><?php echo $solitem->nombreGrupo; ?></td>
                    <td><?php echo $solitem->codigoBarras; ?></td>
                    <td><?php echo $solitem->stock; ?></td>
                    <td><?php echo $solitem->scans; ?></td>
                    <td><?php echo $solitem->diff; ?></td>
                    
                    

                
                </tr>
            
            <?php } 
            
            ?>
  

            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ------------------------------------------------------------------------------------>


            <?php
                        
            }
        }       
}else{
    ?>
    <h2 style="color:gray";>Acceso denegado</h2>
    <?php
 }
 
 ?>
 <!---------------------------------------------->
 <!--------------Fin Content -------------------->
 <!---------------------------------------------->
 </div>
 
 
 
       
 <?php  
 
  
   
 include_once "footerSF.php"; ?>