<?php
    include_once "header.php";
    //si no es admin no abre
    /*if($userAdmin<>0){
        echo ('ACCESO DENEGADO');
        exit();
        }*/


    $emp='ALL';




    
  

    if ($userId==274 || $userId==275) { //MABELTRADING
        $s1 = $db->query(" SELECT * FROM VENDEDORES_OSLP where fk_emp='MT'" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
    }else {  //COSMECMAC
        $s1 = $db->query("SELECT * FROM VENDEDORES_OSLP where fk_emp='CE'" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);    
    }

?>
<div class="content">

<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">HORARIOS</strong>
  
        </div>
    
        <div class="card-body">
 
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>CODIGO</th>
                        <th>NOMBRE</th>
                        <th>EMP</th>

                    </tr>
                </thead>
                <tbody>
                <?php   foreach($whs as $wh){ ?>


                    <tr>
                        <td><?php echo $wh->SlpCode ?></td>
                        <td><?php echo $wh->SlpName ?></td>
                        <td><?php echo $wh->fk_emp ?></td>


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
      
<?php    include_once "footer.php"; ?>