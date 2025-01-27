<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('ACCESO DENEGADO');
    }else{
        include 'php/depcru.php';

        // Manejo de operaciones CRUD
            if (isset($_POST['crear'])) {
                crearDeposito($db, $_POST['fecha'], $_POST['cantidad'], $_POST['descripcion']);
            }

            if (isset($_GET['eliminar'])) {
                eliminarDeposito($db, $_GET['eliminar']);
            }

            if (isset($_POST['actualizar'])) {
                actualizarDeposito($db, $_POST['id'], $_POST['fecha'], $_POST['cantidad'], $_POST['descripcion']);
            }

            $depositos = obtenerDepositos($db, $_POST['fecha_inicio'] ?? null, $_POST['fecha_fin'] ?? null);
            $rows = $depositos->fetchAll(PDO::FETCH_OBJ); 
            $total_valor = 0;
            
?>


<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">DEPOSITOS REGISTRADOS</strong>
        </div>


        <form id="monthformX"  method="post" action="">
            <div class="input-group">
                <input type="text" class="form-control" value="Fecha de cajas :" readonly disabled >
                <input type="date" name="desde" id="desde" class="form-control" value="<?php echo $desde ?>" required>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo $hasta ?>" required>
                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="cicL.php">	
                <input type="button" value="Nuevo 📄" class="form-control" onclick="window.location.href='depsN.php';">	
            </div>
        </form>


        
        <div class="table-stats order-table ov-h">
            <table class="table" id='tblBodegas'>
                <thead>
                    <tr>
                        <th>FECHA DE CAJA</th>
                        <th>BANCO</th>
                        <th>FECHA DEPOSITO</th>
                        <th>NRO DEPOSITO</th>
                        <th>VALOR</th>
                        <th>OBSERVACION</th>
                        <th>FUNCIONES</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $row){ 
                            $total_valor += $row->valor;
                    ?>


                    <tr>
                        <td><?php echo $row->fecha_cica ?></td>
                        <td><?php echo $row->bnk ?></td>
                        <td><?php echo $row->fec_dep ?></td>
                        <td><?php echo $row->nro_dep ?></td>
                        <td><?php echo $row->valor ?></td>
                        <td><?php echo $row->observacion ?></td>
                        <td>
                            <button type="button" class="btn btn-outline-success" 
                            onclick="window.open('filTT.php?idcab=<?php echo $row->id ?>','_self')"
                            > 🖋️ </button> 
                            <button type="button" class="btn btn-warning delete" 
                            onclick="delete_user($(this),<?php echo $row->id ?>)"
                            > ✖️ </button> 

                            
                        </td>
                        
                    </tr>                   
                <?php } ?>   
                    <tr>
                        <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                        <td><strong><?php echo $total_valor; ?></strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<script> 
    document.getElementById("frmLoad").onsubmit = function() {
    
        if (confirm("¿Seguro de enviar?")) {
            $(".loader-page").css({visibility:"visible",opacity:"0.8"});
            return true;
            } else {
            return false;
            }
    };

    function tip_user(row,id,fecha,cerrado)
        { 
        
            tipTD(id,fecha);
            //console.log(id + ' -> ', fecha);
            
            var uno = document.getElementById(fecha+id);
        // valor?uno.innerText = "off":uno.innerText = "on";
        // valor=!valor ;
        //console.log(uno.innerText);
            if (uno.innerText=='TOTAL') {
                uno.innerText = "PARCIAL";
            } else {
                uno.innerText = "TOTAL";
                
            }
                //alert(row.name );
                // alert(id);
            //    row.closest('tr').remove();
        };

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
        };

        function tipTD(id,fecha) 
        {
            
            var parametros = 
                {
                    "id" : id,
                    "fecha" : fecha
                };

                $.ajax({
                    data: parametros,
                    url: 'php/tipTT.php',
                    type: 'GET',
                    async: false,
                    success: function(data){
                        //row.closest('tr').remove();
                        Swal.fire({
                        position: 'top-end',
                        icon: 'info',
                        title: 'Se actualizo correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })

                    },
                    error: function(){
                        console.log('error de conexion - revisa tu red');
                    }
                });
        }

    function delTD(id,row) {
    
    var parametros = 
        {
            "id" : id
        };

        $.ajax({
            data: parametros,
            url: 'php/deleteTFT.php',
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
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>



      
<?php  
    }; 
include_once "footer.php";
 ?>