<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('ACCESO DENEGADO');
    
        }else {
            
        
        $s1 = $db->query("select * from Almacen" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);   
        
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where tipo='TT' and [date]>DATEADD(MONTH,-1,getdate())
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) 
        order by CONCAT(date,' ',left(time,5)) desc
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ);   
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
            <strong class="card-title">GENERADOR DE TOMAS FISICAS</strong>
        </div>
        <div class="card-body">
            <form id="frmLoad" method="post" enctype="multipart/form-data" class="form-horizontal">
                
                <div class="form-group">
                <label for="idalm" class=" form-control-label" >Almacen</label>
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

<div class="col-md-6">
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
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($users as $user){ ?>


                    <tr>
                        <td><?php echo $user->id ?></td>
                        <td><?php echo $user->cod_almacen ?></td>
                        <td><?php echo $user->fec ?></td>
                        <td><?php echo $user->items ?></td>
                        
                    </tr>                   
                <?php } ?>   
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
</script>
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   }; 
include_once "footer.php";
 ?>