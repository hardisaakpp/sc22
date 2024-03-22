<?php
    include_once "header.php";
    //si no es admin no abre
   // if ($userAdmin!=1 && $userAdmin!=3 && $userAdmin!=6){
    //    echo ('<h4> NO TIENE ACCESO</h4>');
        
    //}else
    {
        # code...

        $mes=date("n"); ;
        $anio=date("Y");


    if (isset($_POST["mes"]) and isset($_POST["anio"]) )
    {
        $mes=$_POST['mes'];
        $anio=$_POST['anio'];
        
    }

// cabecera de toma actual

if (strncmp($userName, "RL-", 3) === 0 || strncmp($userName, "LP-", 3) === 0) {
    //MABELTRADING
    $sentencia = $db->query("
    declare @mes int
    set @mes=".$mes."
    declare @anio int
    set @anio=".$anio."
    declare @whs nvarchar(20)
    set @whs='".$userName."'



    select TIPO, Q1.SlpCode, V.SlpName,Fecha,
		SUM(Q1.total) AS total,SUM(Q1.impuestos) as impuestos, 
		SUM(Q1.facturas) AS facturas, SUM(Q1.meta) as meta, SUM(Q1.Cantidad) as Cantidad
    from (
				--FACTURAS
				SELECT 'FACTURA' AS TIPO, SlpCode, Fecha,
				SUM(total) AS total, SUM(impuestos) AS impuestos,
				SUM(Docs) AS facturas, 0 as meta, SUM(Cantidad) as Cantidad
				FROM DOCS3_MT
				where year(fecha)=@anio and MONTH(fecha)=@mes  and ALMACEN=@whs and TIPO = 'FACTURA'
				GROUP BY ALMACEN,Fecha, SlpCode 
				UNION
				SELECT 'NOTA CREDITO' AS TIPO, SlpCode,  Fecha,
				SUM(total)*-1 AS total, SUM(impuestos)*-1 AS impuestos,
				count(total) AS facturas, 0 as meta, SUM(Cantidad) as Cantidad
				FROM DOCS3_MT
				where year(fecha)=@anio and MONTH(fecha)=@mes  and ALMACEN=@whs and TIPO = 'NOTA CREDITO'
				GROUP BY ALMACEN, SlpCode ,Fecha
				  ) Q1
    LEFT JOIN Vendedores_OSLP v on Q1.SlpCode = v.SlpCode and v.fk_emp='MT'
    group by Q1.SlpCode, V.SlpName, Fecha, TIPO
    ");

} else {
      //COSMECMAC
      $sentencia = $db->query("
      declare @mes int
      set @mes=".$mes."
      declare @anio int
      set @anio=".$anio."
      declare @whs nvarchar(20)
      set @whs='".$userName."'
  
  
      select TIPO, Q1.SlpCode, V.SlpName,Fecha,
      SUM(Q1.total) AS total,SUM(Q1.impuestos) as impuestos, 
      SUM(Q1.facturas) AS facturas, SUM(Q1.meta) as meta, SUM(Q1.Cantidad) as Cantidad
  from (
              --FACTURAS
              SELECT 'FACTURA' AS TIPO, SlpCode, Fecha,
              SUM(total) AS total, SUM(impuestos) AS impuestos,
              SUM(Docs) AS facturas, 0 as meta, SUM(Cantidad) as Cantidad
              FROM DOCS3_CE
              where year(fecha)=@anio and MONTH(fecha)=@mes  and ALMACEN=@whs and TIPO = 'FACTURA'
              GROUP BY ALMACEN,Fecha, SlpCode 
              UNION
              SELECT 'NOTA CREDITO' AS TIPO, SlpCode,  Fecha,
              SUM(total)*-1 AS total, SUM(impuestos)*-1 AS impuestos,
              count(total) AS facturas, 0 as meta, SUM(Cantidad) as Cantidad
              FROM DOCS3_CE
              where year(fecha)=@anio and MONTH(fecha)=@mes  and ALMACEN=@whs and TIPO = 'NOTA CREDITO'
              GROUP BY ALMACEN, SlpCode ,Fecha
                ) Q1
  LEFT JOIN Vendedores_OSLP v on Q1.SlpCode = v.SlpCode and v.fk_emp='MT'
  group by Q1.SlpCode, V.SlpName, Fecha, TIPO
      ");
}



    
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ); 


  
    
   
        ?>

<!-- Breadcrumbs
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>CUMPLIMIENTO POR VENDEDOR</h1>
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
 /.breadcrumbs-->

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->
    
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            
        <form id="monthformX"  method="post" action="">
            <div class="input-group">
                Mes
                <select name="mes" id="mes" class="form-control">
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
                Año
                <input type="number" name="anio" min="2023" max="2025" id="anio" class="form-control" value="<?php echo $anio ?>" required>
                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="prevenLRLmDAY.php">	
            </div>
        </form>
        <script>
            document.getElementById('mes').value = <?php echo $mes; ?>;
        </script>


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
            <strong class="card-title">VENTAS POR DIA Y ASESOR </strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>FECHA</th>
                                    <th>Nombre Vendedor</th>
                                    <th>Tipo</th>
                                    <th>Venta Neta</th>
                                    
                                    <th>Facturas</th>
                                    <th>Ticket Promedio</th>
                                    <th>Unidades</th>
                                    <th>Precio Promedio</th>
                                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                        
                        <td><?php echo $citem->Fecha ?></td>
                        <td><?php echo $citem->SlpName ?></td>
                        <td><?php echo $citem->TIPO ?></td>
                        <td> <?php echo $citem->total - $citem->impuestos ?> </td>
                        
                       
                       
                        <td> <?php echo $citem->facturas ?> </td>
                        <td> <?php 
                            if ($citem->facturas>0) {

                                echo '$ '.number_format((float)(($citem->total - $citem->impuestos)/$citem->facturas), 2, '.', '');
                                
                            } else {
                                echo '$ 0.00';
                            }
                        ?> </td>
                        <td> <?php echo $citem->Cantidad ?> </td>
                        <td> <?php 
                            if ($citem->Cantidad>0) {

                                echo '$ '.number_format((float)(($citem->total - $citem->impuestos)/$citem->Cantidad), 2, '.', '');
                                
                            } else {
                                echo '$ 0.00';
                            }
                        ?> </td>
                    </tr>
                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>



       



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