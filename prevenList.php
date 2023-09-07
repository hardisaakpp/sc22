<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>2){
        echo ('ACCESO DENEGADO');
        exit();
        }


    $emp='ALL';

if ($whsTurem>0) {
    
    $sentencia6 = $db->query("select fk_emp from  Almacen where id=".$whsTurem." " );
    $regC1 = $sentencia6->fetchObject();
    $emp=$regC1->fk_emp;

    if ($emp=='MT') { //MABELTRADING
        $s1 = $db->query(" SELECT * FROM VENDEDORES_OSLP where fk_emp='MT'" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
    }else {  //COSMECMAC
        $s1 = $db->query("SELECT * FROM VENDEDORES_OSLP where fk_emp='CE'" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);    
    }
}else {
    $s1 = $db->query("SELECT * FROM VENDEDORES_OSLP  " );
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
   <!--                       
<form id="monthformX" method="post" action="" name="headbuscar">
   <input type=number id="idcab" class="form-control" name="idcab" value=<?php echo $emp; ?> hidden >
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Descargar
        </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
            
                    <input type="submit"class="dropdown-item" value="Horarios.xlsx" onclick=this.form.action="turempLxls.php?ti=1&idcab=<?php echo $emp ?>">
               
                    
            </div>
    </div>
</form>
-->
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