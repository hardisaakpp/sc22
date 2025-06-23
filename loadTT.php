<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('ACCESO DENEGADO');
        }else {
            $fil = 'AL';
            if (isset($_GET["fil"])) {
                $fil = $_GET["fil"];
            }
        $s1 = $db->query("select * from Almacen where inactivo=0" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);   

 $sentencia = $db->query("

    EXEC sp_GetTTStockData '". $fil ."' ;    
    " );
    $users = $sentencia->fetchAll(PDO::FETCH_OBJ);

     
?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMAS FISICAS TOTALES</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">F5</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'">X</button>
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
<script>
  

    function chargeTFA(){


       <?php foreach($users as $use){ ?>  
            createTFA('<?php echo $use->WhsCode ?>', '<?php echo $use->Quantity ?>');
        <?php } ?>

    }

    function createTFA(WhsCode, Quantity) {
    
        var parametros = 
            {
                "WhsCode" : WhsCode ,
                "Quantity" : Quantity
            };

            $.ajax({
                data: parametros,
                url: 'php/loadTomaFisicaAleatoria.php',
                type: 'POST',
                success: function(data){
                    es=document.getElementById("tc"+WhsCode );
                    es.innerText = '✔️';
                },
                error: function(){
                    console.log('error de conexion - revisa tu red');
                }
            });
    }
</script>

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">GENERADOR DE TOMAS FISICAS</strong>
        </div>
        <div class="card-body">
            <form id="frmLoad" method="post" enctype="multipart/form-data" class="form-horizontal">
                
                <div class="form-group">
                <label for="idalm" class="form-control-label" >Almacen</label>
                        <select name='idalm'  data-placeholder='Selecciona el almacen' class='js-example-basic-single form-control' id='idalm'  Size='Number_of_options'>
                        
                            <?php   foreach($whs as $wh){ ?>
                                <option value="<?php echo $wh->id ?>"><?php echo $wh->cod_almacen . ' '.$wh->nombre  ?></option>
                            <?php } ?>
                        </select>
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm" value='Enviar CONTEO ►' onclick=this.form.action="php/tftCreate.php"> 
                <i class="fa fa-dot-circle-o"></i> GENERAR
            </button>
        </div>
            </form>
    </div>
</div>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">GENERADAS LOS ULTIMOS 30 DIAS</strong>
        </div>
        <div class="card-body">
            <table class="table" id='tblBodegas'>
                <thead>
                    <tr>
                        <th>CODIGO</th>
                        <th>ALMACEN</th>
                        <th>FECHA</th>
                        <th>ITEMS</th>
                        <th>TIPO</th>
                        <th>FUNCIONES</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($users as $user){ ?>


                    <tr>
                        <td><?php echo $user->id ?></td>
                        <td><?php echo $user->cod_almacen ?></td>
                        <td><?php echo $user->fec ?></td>
                        <td><?php echo $user->items ?></td>
                        <td>
                            <?php
                                if ($user->locked==0) {
                                       
                                        if ($user->tipo=="TT") {
                                            ?>


                                        <button type="button" class="btn btn-outline-primary"  id='<?php echo $user->fec.$user->id ?>'
                                                onclick ="tip_user($(this),<?php echo $user->id ?>,'<?php echo $user->fec ?>','<?php echo $user->tipo ?>')">
                                            
                                            <?php
                                            
                                            echo "TOTAL</button> </td>";
                                        } else {
                                            ?>
                                            <button type="button" class="btn btn-outline-primary"  id='<?php echo $user->fec.$user->id ?>'
                                                onclick ="tip_user($(this),<?php echo $user->id ?>,'<?php echo $user->fec ?>','<?php echo $user->tipo ?>')">
                                            
                                                <?php
                                            echo "PARCIAL</button> </td>";
                                        }
                                        ?>
                                        <td>

                                            <button type="button" class="btn btn-outline-success" 
                                            onclick="window.open('filTT.php?idcab=<?php echo $user->id ?>','_self')"
                                            > 🪄Grupos </button> 
                                            <button type="button" class="btn btn-outline-success" 
                                            onclick="window.open('filTTsubcat.php?idcab=<?php echo $user->id ?>','_self')"
                                            > 🪄SubCategorias </button> 
                                            <button type="button" class="btn btn-warning delete" 
                                            onclick="delete_user($(this),<?php echo $user->id ?>)"
                                            > ✖️Eliminar </button> 

                                            <button type="button" class="btn btn-outline-success" onclick="confirmar('<?php echo $user->id ?>')">
                                            🔃Actualizar </button>
                                        </td>
                                <?php
                                    } else {
                                         if ($user->tipo=="TT") {
                                            echo "TOTAL </td>";

                                        } else {
                                            echo "PARCIAL </td>";
                                        }
                                    }
                                ?>
                                    
                            

                        
                    </tr>                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<script> 

   function confirmar(idcab) {
            if (confirm("¿Seguro de actualizar?")) {
                $(".loader-page").css({ visibility: "visible", opacity: "0.8" });
                window.location.href = 'php/refreshTFT.php?idcab=' + idcab;
            }
        };
        
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
            var uno = document.getElementById(fecha+id);
            if (uno.innerText=='TOTAL') {
                uno.innerText = "PARCIAL";
            } else {
                uno.innerText = "TOTAL";
                
            }
        };

    function delete_user(row,id)
        { 
            if (confirm("¿Seguro de eliminar?")) {
             delTD(id,row);
            } else {
                console.log('FALSO!');
            }
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



      
<?php   }; 
include_once "footer.php";
 ?>