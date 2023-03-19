<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('ACCESO DENEGADO');
    
        }else {
            
        
       
        $s1 = $db->query("	select a.id as 'WhsCode', articulosContar as 'Quantity', a.nombre, tcd.tomaCode
        from users u
            join Almacen a on u.fk_ID_almacen_invs=a.id
            left join (select FK_ID_almacen, tomaCode from StockCab where convert(varchar(10), [date], 102) = convert(varchar(10), getdate(), 102)) tcd
                on a.id=tcd.FK_ID_almacen
        where realizaConteo=1;" );
        $users = $s1->fetchAll(PDO::FETCH_OBJ);   
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
                //    timeout: 3000,
                success: function(data){
                    //console.log(data);
                    es=document.getElementById("tc"+WhsCode );
                    es.innerText = '✔️';
                //$("#find").click();
                   /* if (data==1) {
                        Swal.fire({
                        icon: 'success',
                        title: '👌😀',
                        text: 'Clave actualizada correctamente!'
                        })
                    } else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'La clave actual es incorrecta!'
                        })
                    }*/
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
            <strong class="card-title">BODEGAS ASIGNADAS PARA CONTEO</strong>
        </div>
        <div class="card-body">
            <table class="table" id='tblBodegas'>
                <thead>
                    <tr>
                        <th>Bodega</th>
                        <th>Cantidad</th>
                        <th>Actual</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($users as $user){ ?>


                    <tr>
                        <td><?php echo $user->nombre ?></td>
                        <td><?php echo $user->Quantity ?></td>
                        <td id='<?php echo "tc".$user->WhsCode  ?>'><?php echo $user->tomaCode  ?></td>



                    </tr>
                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>




<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   };
include_once "footer.php"; ?>