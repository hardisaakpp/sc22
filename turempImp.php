<?php include_once "header.php" ?>



<br>


<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">Importar Turnos</strong>
        </div>
        <div class="card-body">
            
            <form method="post" action="turemImport.php" enctype="multipart/form-data">
                <div class="form-group">
                    <input name="tiendaTuremp" value='<?php echo $whsTurem; ?>' hidden>
                    <!-- <label for="exampleInputFile"><h3>Importar turnos</h3></label> -->
                    <input type="file" name="file" class="form-control" id="exampleInputFile" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Cargar</button>
            </form>
        </div>
    </div>
</div>






<?php include_once "footer.php" ?>