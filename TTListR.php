<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('ACCESO DENEGADO');
    
        }else {
            
        $fil = 'AL';

            
            if (isset($_GET["id"])) {
                $fil = $_GET["id"];
            }
          

        $s1 = $db->query("select * from Almacen" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);   
        


switch ($fil) {
    case 'AL':
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items
        ,c.tipo
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where (tipo='TT' or tipo='TP') and [date]>DATEADD(MONTH,-1,getdate())
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) , c.tipo
        order by CONCAT(date,' ',left(time,5)) desc
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ); 
        break;
    case 'TT':
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items
        ,c.tipo
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where (tipo='TT') and [date]>DATEADD(MONTH,-1,getdate())
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) , c.tipo
        order by CONCAT(date,' ',left(time,5)) desc
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ); 
        break;
    
    case 'TP':
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items
        ,c.tipo
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where (tipo='TP') and [date]>DATEADD(MONTH,-1,getdate())
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) , c.tipo
        order by CONCAT(date,' ',left(time,5)) desc
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ); 
        break;

    default:
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items
        ,c.tipo
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where (tipo='TT' or tipo='TP') and [date]>DATEADD(MONTH,-1,getdate())
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) , c.tipo
        order by CONCAT(date,' ',left(time,5)) desc
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ); 
        break;
}
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
                                <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Descargar Xlsx
                        </button>
           
                     
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                           
                                  <input type="submit"class="dropdown-item" value="Todos" onclick="window.location.href='TTListR.php'">
                                  <div class="dropdown-divider"></div>
                                  <input type="submit"class="dropdown-item" value="Totales" onclick="window.location.href='TTListR.php?id=TT'" >
                                  <input type="submit" class="dropdown-item" value="Parcial" onclick="window.location.href='TTListR.php?id=TP'">
                                  
                            </div>

                            <button type="button" class="btn btn-outline-warning" onclick="location.reload();">F5</button>
                            <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'">X</button>
                    </div>

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


<!-- Widgets  -->
    <div class="row">

    <?php   foreach($users as $user){ ?>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-3">
                        <a href="TTreporteSacnsI.php?idcab=<?php echo $user->id?>">
                            <i class="pe-7s-browser"></i>
                            </a>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count"><?php echo $user->items ?></span> items</div>
                                <div class="stat-text"><?php echo 'Conteo N°.'.$user->id?></div>
                                <div class="stat-heading"><?php echo $user->cod_almacen .' al '.$user->fec .' ['.$user->tipo.']' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?> 

        

    </div>


                 
               


<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   }; 
include_once "footer.php";
 ?>