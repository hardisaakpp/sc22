<?php
    include_once "header.php";
    //si no es admin no abre
    if ($userAdmin!=1 && $userAdmin!=3){
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

// cabecerqa de toma actual
    $sentencia = $db->prepare("SELECT s.id as id_cab, FK_ID_almacen as id_alm, concat(date,' ',time) as fec, 
        da.responsable, INI, REC, FIN, a.cod_almacen, a.nombre, isnull(nov.NOVEDADES,0) as NOVEDADES
        FROM StockCab s 
            JOIN Almacen a on s.FK_ID_almacen=a.id
            join vw_stockDet_pivotStatus p on s.id=p.FK_id_StockCab
            left join StockCab_TFA da on s.id=da.fk_id_StockCab
            LEFT JOIN (select FK_id_StockCab AS idcab, COUNT(*) as NOVEDADES from StockDet WHERE estado='FIN' AND reconteo<>stock group by FK_id_StockCab) nov on s.id=nov.idcab
        WHERE tipo='TF' ". $wheres ." AND [date] between '".$desde."' and '".$hasta."' ");
    $sentencia->execute([]);
    
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
   
        ?>

<!-- Breadcrumbs-->
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
                    echo "<label>.  Conteo<input type='checkbox' name='conteo' checked ></label>";
                } else {
                    echo "<label>.  Conteo<input type='checkbox' name='conteo'></label>";
                }
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
                }
            ?>


                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="tfaL.php">	
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
            <strong class="card-title">HISTORIAL ULTIMO MES</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>ID</th>
                                    <th>BODEGA</th>
                                    <th>FECHA</th>
                                    <th>RESPONSABLE</th>
                                    <th>CONTEO</th><th>RECONTEO</th><th>CERRADO</th>
                                    <th>CUMPLIMIENTO</th>
                                    <th>DIFERENCIAS</th>
                                    <th></th>
                                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                                    <td><?php echo $citem->id_cab ?></td>
                                    <td><?php echo $citem->cod_almacen ?></td>
                                    <td><?php echo $citem->fec ?></td>
                                    <td><?php echo $citem->responsable ?></td>
                                    <td><?php echo $citem->INI ?></td><td><?php echo $citem->REC ?></td><td><?php echo $citem->FIN ?></td>
                                    <td><?php echo (($citem->FIN*100)/($citem->FIN+$citem->INI+$citem->REC)).'%'  ?></td>
                                    <td><?php echo $citem->NOVEDADES ?></td>
                                    <td>
                                        <button type="button" class="btn btn-outline-success" onclick=window.open("<?php echo 'tfaDprintAdm.php?idcab=' . $citem->id_cab ?>","demo","toolbar=0,status=0,");> 👁️‍🗨️ </button>  
                                    
                                        <button type="button" class="btn btn-warning delete" 
                                        onclick="delete_user($(this),<?php echo $citem->id_cab ?>)"
                                        > ✖️ Eliminar </button> 
                                    </td>
                                </tr>
                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>



       
<script> 

    function delete_user(row,id)
        { 
            if (confirm("¿Seguro de eliminar?")) {
           // $(".loader-page").css({visibility:"visible",opacity:"0.8"});
           // console.log('VERDADERO');
             delTD(id,row);
            } else {
                console.log('FALSO!');
            }

          //  alert(id);
        
            //row.closest('tr').remove();
        }

    function delTD(id,row) {
    
    var parametros = 
        {
            "id" : id
        };

        $.ajax({
            data: parametros,
            url: 'php/deleteTFA.php',
            type: 'GET',
            async: false,
            success: function(data){
                row.closest('tr').remove();
                Swal.fire({
                position: 'top-end',
                icon: 'Eliminado',
                title: 'Se elimino 1 registro',
                showConfirmButton: false,
                timer: 1500
                })

            },
            error: function(){
                console.log('error de conexion - revisa tu red');
            }
        });
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