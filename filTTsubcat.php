<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
    $s1 = $db->query(" 
		 select 
			ar.subcategoria, count(d.id) as items 
        from StockCab c
			join Almacen a on c.FK_ID_almacen=a.id
			left join StockDet d on c.id=d.FK_id_StockCab 
			left join Articulo ar on d.FK_ID_articulo=ar.id
        where (tipo='TT' or tipo='TP') and c.id=".$idcab."
        group by ar.subcategoria
		order by 1

    " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);    

?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMA FISICA TOTAL <?php  echo $idcab ?></h1>
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
                <div class="card-header">
                    <strong class="card-title"> POR SUBCATEGORIA </strong>
                </div>
                <div class="card-body">
                
            

                <table  class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Subcategoria</th>
                            <th>Items</th>
                            <th></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php  

                    
                    foreach($scans as $user){ ?>
                        <tr>
                            <td><?php echo $user->subcategoria ?></td>
                            <td><?php echo $user->items ?></td>
                            <td>
                        
                                <button type="button" class="btn btn-warning delete" 
                                onclick="delete_user($(this),'<?php echo $user->subcategoria ?>',<?php echo $idcab ?>)"
                                > ✖️ Eliminar </button> 

                                
                            </td>
                        </tr>
                    
                    <?php 

                } 
                
            
                
                ?>   
                    </tbody>
                </table>

                        
                    
                </div>
                
                    <!-- </form>-->
            </div>
        </div>

    <!---------------------------------------------->
    <!--------------Fin Content -------------------->
    <!---------------------------------------------->
</div>
<script>
    function delete_user(row,id,idcab)
        { 


            if (confirm("¿Seguro de eliminar los articulos que pertenecen a este grupo?")) {
           // $(".loader-page").css({visibility:"visible",opacity:"0.8"});
           console.log(id);
           console.log(idcab);
                delTD(id,row,idcab);
            } else {
                console.log('FALSO!');
            }


        }

    function delTD(id,row,idcab) {
        
        var parametros = 
            {
                "id" : id,
                "idcab" : idcab
            };

            $.ajax({
                data: parametros,
                url: 'php/deleteTFTsubcat.php',
                type: 'GET',
                async: false,
                success: function(data){
                    row.closest('tr').remove();
                    Swal.fire({
                    position: 'top-end',
                    icon: 'Eliminado',
                    title: 'Se elimino 1 registro',
                    showConfirmButton: false,
                    timer: 2000
                    })

                },
                error: function(){
                    console.log('error de conexion - revisa tu red');
                }
            });
    }
</script>
<?php   
include_once "footer.php";
 ?>