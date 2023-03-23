<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
    $s1 = $db->query("
    select u.username, count(barcode)  as scans
    from [StockScan] s join users u on s.id_user=u.id
    where s.fk_id_stockCab=".$idcab."
    group by  u.username order by 1    
    " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);    

    $sentencia2 = $db->query("
    select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items, sum(stock) as cantidad
    from StockCab c
    join Almacen a on c.FK_ID_almacen=a.id
    left join StockDet d on c.id=d.FK_id_StockCab 
    where c.id=".$idcab." and [date]>DATEADD(MONTH,-1,getdate())
    group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) 
    order by CONCAT(date,' ',left(time,5)) desc
    "  );
    $TEMP1 = $sentencia2->fetchObject();
        $WhsCode = $TEMP1->cod_almacen;
        $Items = $TEMP1->items;
        $cantidad = $TEMP1->cantidad;
        $FecStock = $TEMP1->fec;

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
                <div class="col-sm-8">
                    <div class="page-header float-right">

                    <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones
                    </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <button type="button" class="dropdown-item" onclick="window.location.href='TTscans.php?idcab=<?php echo $idcab ?>'">ESCANEAR</button>
                            <button type="button" class="dropdown-item" onclick="window.location.href='TTscanDel.php?idcab=<?php echo $idcab ?>'">ELIMINAR ITEMS</button>
                            <button type="button" class="dropdown-item" onclick="window.location.href='wllcm.php'">SALIR</button>
                        </div>
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


    <div class="col-lg-6">
        <div class="card">
             <div class="card-header">
                <strong class="card-title">RESUMEN DE ESCANEOS</strong>
            </div>
            <div class="card-body">
              
            <?php 
            echo "Almacen: ".$WhsCode."<br>";
            echo "Fecha de stock: ".$FecStock."<br>";
            echo "Items: ".$Items."<br>";
            echo "Cantidad: ".$cantidad."<br>";

            ?>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Registros Ingresados</th>
                    </tr>
                </thead>
                <tbody>
                <?php  
                $totals=0;
                
                foreach($scans as $user){ ?>
                    <tr>
                        <td><?php echo $user->username ?></td>
                        <td><?php echo $user->scans ?></td>
                    </tr>
                   
                <?php 
            $totals=$totals + $user->scans;
            } 
            
            echo "  <tr>
                        <td>TOTAL:</td>
                        <td>".$totals."</td>
                    </tr>";
            
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
      
<?php   
include_once "footer.php";
 ?>