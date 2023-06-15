<?php
    include_once "header.php";


/*-----------------------------------------------------------
------------------- PERFILES ------------    $userAdmin = $_SESSION["perfil"];--------------------
-------------------0->DESACTIVADO----------------------------
-------------------1->ADMIN----------------------------------
-------------------2->TIENDA---------------------------------
-------------------3->INVENTARIOS----------------------------
-------------------4->ASISTENTE PARA CONTEOS TFT-------------
-------------------5->BODEGA---------------------------------
------------------------------------------------------------->*/

    /*Si viene por parametro se coge sino nada */
    if (isset($_POST["pIdAlmacen"])) {
        $whsCica = $_POST["pIdAlmacen"];
    }
   
    $pFecha= Date('Y-m-d') ;
    if (isset($_POST["pFecha"])) {
        $pFecha = $_POST["pFecha"];
    }



    //si no es ADMIN o no se  no abre
if($whsCica==0){  
    echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
    //echo $whsCica;
   // echo $pFecha;
    //exit();
}else{


if ($pFecha==date('Y-m-d')) {
   // echo "mismo dia!";  ///solo actualiza si es el mismo dia
    $sentencia = $db->query("
        
    EXEC sp_cica_sincSAPSingle '". $whsCica ."', '". date('Y-m-d') ."';

    EXEC sp_cica_createCajas '". $whsCica ."', '". date('Y-m-d') ."';
    
    " );
    $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);
}






  //  echo $whsCica;
   // echo $pFecha;

    $s1 = $db->query("
    select * from CiCa 
    where fk_ID_almacen=".$whsCica."	and fecha= '".$pFecha."'
    " );
    $cajas = $s1->fetchAll(PDO::FETCH_OBJ);   

    $sentencia = $db->query("      
    select 
            c.CardName as 'forPag'
            , sum(Valor) as 'valSAP'
            , sum(valRec) as 'valRec'
            , sum(valOnline) as 'valOnline'
            , sum(valPinpadOn) as 'valPinpad'
            , sum(valPinpadOff) as 'valMedianet'
            , ( sum(valRec) +sum(valPinpadOff)+ sum(valPinpadOn)+sum(valOnline)-sum(Valor)) as 'Diferencia'

        from CiCaSAP c join Almacen a on a.cod_almacen=c.whsCode
        where a.id='". $whsCica ."' and c.fecha='". $pFecha ."'
        group by c.CardName" );
    $consolidados = $sentencia->fetchAll(PDO::FETCH_OBJ);
       
?>

<!-- Breadcrumbs-->
   <!-- <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>CAMBIO DE CLAVE</h1>
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
    </div>-->
<!-- /.breadcrumbs-->




<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->




<!-- Widgets  -->
<div class="row">

<?php   foreach($cajas as $user){ ?>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-3">
                    <a href="cicaU.php?id=<?php echo $user->id?>">
                        <i class="pe-7s-browser"></i>
                        </a>
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                       
                            <div class="stat-text"><?php echo $user->caja?></div>
                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?> 

    

</div>
<!------------------------------------------------------------------------------------------------>
<!------------------------------------------------------------------------------------------------>



<!------------------------------------------------------------------------------------------------>

    <!--//conteo-->
    <div class="col-md-12">
        
       

        <div class="card">
            <form action="" id="frmConteo" method="post">
                <div class="card-header">
                    <div class="input-group">
                    <label class="form-control" >
                        <blockquote class="blockquote"><strong>
                        <?php echo $pFecha ?> CIERRE DE CAJA CONSOLIDADO
                        </blockquote></strong>
                    </label>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">Button</button>
                        <button class="btn btn-outline-secondary" type="button">Button</button>
                    </div>
                    </div>
                
                
                    
                        
                </div>
                <table id="resumentbl" class="table table-hover">
                    <thead class="thead-dark">
                        <tr>

                            <th>FORMA PAGO</th>
                            <th id='v1'>VALOR SAP</th>
                            <th id='v2'>RECIBIDO</th>
                            <th id='v3'>ONLINE</th>
                        
                            <th id='v4'>PINPAD</th>
                        
                            <th id='v5'>DATAFAST/ MEDIANET</th>
        
                            <th id='v6'>DIFERENCIA</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 

            foreach($consolidados as $forpag){
                
                    ?>
                <tr>
                    <td><?php echo $forpag->forPag ?></td>

                    <td class="valSAP"><?php echo $forpag->valSAP; ?></td>
                    <td class="valRec"><?php echo $forpag->valRec; ?></td>
                    <td class="valOnline"><?php echo $forpag->valOnline; ?></td>
                    <td class="valPinpad"><?php echo $forpag->valPinpad; ?></td>
                    <td class="valMedianet"><?php echo $forpag->valMedianet; ?></td>
                    <td class="Diferencia"><?php echo $forpag->Diferencia; ?></td>
                    

                </tr>
            <?php } ?>
                    
                </tbody>
            </table>
            </form> 

        </div>



    </div>




    <script language="javascript" type="text/javascript">
        var tds = document.getElementById('resumentbl').getElementsByTagName('td');
        var sum = 0.0;
        var svalRec = 0.0;
        var svalOnline = 0.0;
        var svalPinpad = 0.0;
        var svalMedianet = 0.0;
        for(var i = 0; i < tds.length; i ++) {
            if(tds[i].className == 'valSAP') {
                sum += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valRec') {
                svalRec += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valOnline') {
                svalOnline += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valPinpad') {
                svalPinpad += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == 'valMedianet') {
                svalMedianet += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }

        }
        document.getElementById('resumentbl').innerHTML += '<tr class="table-secondary"><td>TOTAL:</td><td>' + sum.toFixed(2) + '</td><td>' +  svalRec.toFixed(2) +
        '</td><td>' +  svalOnline.toFixed(2) +
        '</td><td>' +  svalPinpad.toFixed(2) +
        '</td><td>' +  svalMedianet.toFixed(2) +'</td></tr>';
    </script>




        <?php
    
?>


  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php  
}  include_once "footer.php"; ?>