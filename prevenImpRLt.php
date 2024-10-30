<?php include_once "header.php" ;



//$pFecha= Date('2023-06-14') ;
//$pFecha= Date('Y-m-d') ; 
$pdia = date("j");

//HOY

if ($pdia>30) {


    
?>

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">Importar Presupuestos Vendedores</strong>
        </div>
        <div class="card-body">
            ✋ Solo puede cargar los  10 días del mes. ✋</br>
        </div>
    </div>
</div>

<?php 
}else{
    ?>




<br>




<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">Importar Presupuestos Vendedores TIENDA ROLAND</strong>
        </div>
        <div class="card-body">
            El archivo debe subirse en formato "Libro de excel 97-2003 [.xls]".
            <form method="post" action="prevenImportRLt.php" enctype="multipart/form-data">
                <div class="form-group">
                    <input name="tiendaTuremp" value='<?php echo $whsTurem; ?>' hidden>
                    <!-- <label for="exampleInputFile"><h3>Importar turnos</h3></label> -->
                    <input type="file" accept=".xls" name="file" class="form-control" id="exampleInputFile" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Cargar</button>
            </form>
        </div>
    </div>
</div>



<?php 
}



include_once "footer.php" ?>