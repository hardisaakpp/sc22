
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
                id="sx" name="sx"  placeholder="NumDoc" value="">
                <input type="submit" name="find" value="Buscar 🔎" class="form-control"
                    onclick=this.form.action="soltr.php">

            </form>
        </div>
        <h3 style="color:gray";>Solicitud de Translado:</h3>
            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ------------------------------------------------------------------------------------------------------------------>


<?php
//validar que es ADMINISTRADOR
if($userAdmin==5 || $userAdmin==1)
{
    $idU = $_SESSION['idU'];
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

            <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
            <link rel="stylesheet" type="text/css" href="js/syntax/shCore.css">
            <link rel="stylesheet" type="text/css" href="js/demo.css">
            <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
            <script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
            <script type="text/javascript" language="javascript" src="js/syntax/shCore.js"></script>
            <script type="text/javascript" language="javascript" src="js/demo.js"></script>
            <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                    $('#example').DataTable();
                    $('#sx').val('<?php echo  $solicitud;?>');
                    } );
            </script>

            
            <!-- -----------------------------------------------------------------------------------------------------------
            -------------------MENU--PESTAÑAS---------------------------------------
            ------------------------------------------------------------------------------------------------------------------>
            <ul class="nav nav-tabs nav-justified nav-dark">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo "soltr.php?sx=" . $solicitud?>">Resumen</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo "soltrR.php?sx=" . $solicitud?>">Escanear</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">Eliminar</a>
            </li>
            </ul>
            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ----------------SCAN-------------------------------------------------------------------->
<?php

            //Consulta tomas TT
    $sentenci = $db->query("select top 100 s.id,s.barcode,s.fecScan,a.ID_articulo
    from stockScan s 
     left join Articulo a on s.barcode=a.codigoBarras
    where fk_id_stockCab=".$id_cab." " );  
    $mascotas = $sentenci->fetchAll(PDO::FETCH_OBJ);
    ?>
<div class="table-responsive">
			<table class="table table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>BARCODE</th>
                        <th>HORA</th>
						<th>CODIGO SAP</th>
                        <th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($mascotas as $mascota){ ?>
						<tr>
							<td><?php echo $mascota->barcode ?></td>
							<td><?php echo $mascota->fecScan ?></td>
							<td><?php echo $mascota->ID_articulo ?></td>
							<td>
                                <a class="btn btn-secondary btn-sm" href="<?php echo "eliminaScan.php?id=" . $mascota->id . "&idcab=".$solicitud ?>">ELIMINA</a>
                            </td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
            


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
 

include_once "footerSF.php" ?>