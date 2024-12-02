<?php
    include_once "header.php";
    //si no es admin no abre
    if ($userAdmin!=1 && $userAdmin!=3 && $userAdmin!=6){
        echo ('<h4> NO TIENE ACCESO</h4>');
        
    }else{
        # code...

        $desde=Date('Y-m-d') ;
        $hasta=Date('Y-m-d') ;


    if (isset($_POST["desde"]) and isset($_POST["hasta"]) )
    {
        $desde=$_POST['desde'];
        $hasta=$_POST['hasta'];
        
    }

// cabecera de toma actual


$sentencia = $db->query("





select q1.id , q1.fecha, q1.almacen, q4.DifCarga, q4.SAPB1, q4.StoreControl
, ( (valRec) +(valPinpadOff)+ (q1.valPinpadOn)+(valOnline)-(valSAP)) as 'Diferencia'
,
q1.cerrado, q3.numDif, q1.revisado
from
(
select 
    c.fecha,c.whsCode,a.id,concat(a.cod_almacen,'-',a.nombre) as almacen,
    (Select top 1 cerrado from CiC cic where a.id=cic.fk_ID_almacen and c.fecha=cic.fecha) as 'cerrado',
    (Select top 1 revisado from CiC cic where a.id=cic.fk_ID_almacen and c.fecha=cic.fecha) as 'revisado',
    sum(Valor) as 'valSAP'
    , sum(valPinpadOn) as 'valPinpadOn'
    /*   , sum(valRec) as 'valRec'
    , sum(valOnline) as 'valOnline'
    , sum(valPinpadOn) as 'valPinpad'
    , sum(valPinpadOff) as 'valMedianet'
    , ( sum(valRec) +sum(valPinpadOff)+ sum(valPinpadOn)+sum(valOnline)-sum(Valor)) as 'Diferencia'
    */
from cicSAP c join Almacen a on a.cod_almacen=c.whsCode
where c.fecha > DATEADD(MONTH,-2,GETDATE()) and c.origen not like 'H'  
group by a.id,
c.fecha,c.whsCode, a.cod_almacen,a.nombre
)q1
join
(
SELECT [fecha]
,[whsCode]

,sum([valRec]) as [valRec]
,sum([valOnline]) as [valOnline]
,sum([valPinpadOn]) as [valPinpadOn]
,sum([valPinpadOff]) as [valPinpadOff]
FROM [dbo].[cicUs]

where fecha between '".$desde."' and '".$hasta."'  
and (whsCode like 'RL-%' OR whsCode like 'OUT-%')
 and  (whsCode not like 'LP-%' and whsCode not like 'YHD-%')
group by [fecha]
,[whsCode]

) q2
on q1.fecha=q2.fecha and q1.whsCode=q2.whsCode 
join
	(
			select  cs.fecha, cs.whsCode,
		--( ISNULL((cu.valRec), 0 ) +ISNULL((cu.valPinpadOff), 0 )+ ISNULL((cs.valPinpadOn), 0 ) + ISNULL((cu.valOnline), 0 )-ISNULL((cs.Valor), 0 )) as 'Diferencia' ,	
		 sum(
			CASE
				WHEN ((cu.valRec) +(cu.valPinpadOff)+ (cs.valPinpadOn)+(cu.valOnline)-(cs.Valor))<>0 
				OR ((cu.valRec) +(cu.valPinpadOff)+ (cs.valPinpadOn)+(cu.valOnline)-(cs.Valor)) IS NULL THEN 1
				ELSE 0
			END
			) as numDif
		FROM [dbo].[cicUs] cu
			full outer join cicSAP cs on cu.fecha=cs.fecha and cs.whsCode=cu.whsCode and cs.caja=cu.caja and cs.CardName=cu.CardName
		where  cs.origen<>'H' 
			and cs.fecha between '".$desde."' and '".$hasta."'  
			and (cs.whsCode like 'RL-%' OR cs.whsCode like 'OUT-%')
			and  (cs.whsCode not like 'LP-%' and cs.whsCode not like 'YHD-%')
		group by  cs.fecha, cs.whsCode

	)q3
on q1.fecha=q3.fecha and q1.whsCode=q3.whsCode 
left join
    (
        select fecha,whsCode,
            sum(CASE
                    WHEN (origen = 'H' or origen = 'S')  THEN Valor
                    ELSE 0
                END 
                -
                CASE
                    WHEN (origen = 'M' or origen = 'S')  THEN Valor
                    ELSE 0
                END )
                AS 'DifCarga',

            sum(CASE
                WHEN (origen = 'H' or origen = 'S')  THEN Valor
                ELSE 0
            END )
                AS 'SAPB1',
            sum(CASE
                WHEN (origen = 'M' or origen = 'S')  THEN Valor
                ELSE 0
            END )
                AS 'StoreControl'
        from cicSAP 
        group by fecha,whsCode
    )q4
on q1.fecha=q4.fecha and q1.whsCode=q4.whsCode 

     ");

    
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ); 


  
    
   
        ?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>CIERRES DE CAJA</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                              <!--  <button type="button" class="btn btn-outline-warning" onclick="location.reload();">🔃</button>-->
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
                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="cicL.php">	
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
            <strong class="card-title">HISTORIAL </strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>ID</th>
                                    <th>BODEGA</th>
                                    <th>FECHA</th>
                                    <th>DIFERENCIA CARGADA EN SAP</th>
                                    <th>DIFERENCIA CIERRE</th>
                                    <th></th>
                                    <th>LINEAS DIF.</th>
                                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                                    <td><?php echo $citem->id ?></td>
                                    <td><?php echo $citem->almacen ?></td>
                                    <td> <?php echo $citem->fecha ?> </td>
                                    <td> <?php echo $citem->DifCarga ?> </td>
                                    <td><?php echo $citem->Diferencia ?></td>
                                    
                                    <td>

                                                <button type="button" class="btn btn-outline-success" 
                                                onclick="window.open('cic.php?pFecha=<?php echo $citem->fecha ?>&pIdAlmacen=<?php echo $citem->id ?>','_blank')"
                                                > 👁️‍🗨️ </button> 

                                                <?php
                                        if ($citem->fecha>date("Y-m-d", strtotime("-44 days"))) {
                                            ?>
                                                <button type="button" class="btn btn-outline-success" 
                                                onclick="window.open('cicRELOAD.php?pFecha=<?php echo $citem->fecha ?>&pIdAlmacen=<?php echo $citem->id ?>','_blank')"
                                                > 🔄️ </button> 
                                        <?php
                                                
                                                }
                                            ?>

                                
                                        <?php
                                            if ($citem->cerrado==1) {
                                                ?>
                                                    <button type="button" class="btn btn-outline-success"  id='<?php echo $citem->fecha.$citem->id ?>'
                                                    onclick ="delete_user($(this),<?php echo $citem->id ?>,'<?php echo $citem->fecha ?>',<?php echo $citem->cerrado ?>)">
                                                    
                                                <?php
                                                echo "🔒 Abrir</button> ";
                                            } else {
                                                ?>
                                                    <button type="button" class="btn btn-outline-success" id='<?php echo $citem->fecha.$citem->id ?>'
                                                    onclick ="delete_user($(this),<?php echo $citem->id ?>,'<?php echo $citem->fecha ?>',<?php echo $citem->cerrado ?>)">
                                                <?php
                                                echo "🔓 Cerrar</button> ";
                                            }
                                        ?>

                                        <?php
                                            if ($citem->Diferencia<>0 || $citem->DifCarga<>0) {
                                                if ($citem->revisado==1) {
                                                    ?>
                                                        <button type="button" class="btn btn-outline-success"  id='<?php echo 'r'.$citem->fecha.$citem->id ?>'
                                                        onclick ="revisado($(this),<?php echo $citem->id ?>,'<?php echo $citem->fecha ?>',<?php echo $citem->revisado ?>)">
                                                        
                                                    <?php
                                                    echo "✅</button> ";
                                                } else {
                                                    ?>
                                                        <button type="button" class="btn btn-outline-success" id='<?php echo 'r'.$citem->fecha.$citem->id ?>'
                                                        onclick ="revisado($(this),<?php echo $citem->id ?>,'<?php echo $citem->fecha ?>',<?php echo $citem->revisado ?>)">
                                                    <?php
                                                    echo "❌</button> ";
                                                }
                                            }
                                        ?>
                                           


                                    </td>
                                
                                    <td><?php echo $citem->numDif ?></td>
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


<script>
    function delete_user(row,id,fecha,cerrado)
        { 
          
            delTD(id,fecha);
            //console.log(id + ' -> ', fecha);
            
            var uno = document.getElementById(fecha+id);
           // valor?uno.innerText = "off":uno.innerText = "on";
           // valor=!valor ;
           //console.log(uno.innerText);
            if (uno.innerText=='🔒 Abrir') {
                uno.innerText = "🔓 Cerrar";
            } else {
                uno.innerText = "🔒 Abrir";
                
            }
                //alert(row.name );
                // alert(id);
            //    row.closest('tr').remove();
        }
        function revisado(row,id,fecha,cerrado)
        { 
          
            delCH(id,fecha);
            //console.log(id + ' -> ', fecha);
            
            var uno = document.getElementById('r'+fecha+id);
           // valor?uno.innerText = "off":uno.innerText = "on";
           // valor=!valor ;
           //console.log(uno.innerText);
            if (uno.innerText=='✅') {
                uno.innerText = "❌";
            } else {
                uno.innerText = "✅";
                
            }
                //alert(row.name );
                // alert(id);
            //    row.closest('tr').remove();
        }


    function delTD(id,fecha) 
        {
            
            var parametros = 
                {
                    "id" : id,
                    "fecha" : fecha
                };

                $.ajax({
                    data: parametros,
                    url: 'php/cicUnlock.php',
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

        function delCH(id,fecha) 
        {
            
            var parametros = 
                {
                    "id" : id,
                    "fecha" : fecha
                };

                $.ajax({
                    data: parametros,
                    url: 'php/cicCheck.php',
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
</script>
      
<?php  

 
  
include_once "footer.php"; ?>