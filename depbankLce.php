<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin==0){
        echo ('ACCESO DENEGADO');
        exit();
        }

        $s1 = $db->query("select * from CuentaFinancieraCE" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
       
?>
<div class="content">

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
              <strong class="card-title">Cuentas Financieras - COSMEC</strong> para Depositos Tiendas
        </div> 
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>AcctCode</th>
                        <th>AcctName</th>
                        <th>FormatCode</th>
                        <th>Para depositos</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($whs as $wh){ ?>
                    <tr>
                        <td><?php echo $wh->AcctCode ?></td>
                        <td><?php echo $wh->AcctName ?></td>
                        <td><?php echo $wh->FormatCode ?></td>
                        <td>
                            <?php if ($wh->DepositTiendas == 1): ?>
                                <button class="btn btn-success btn-sm" onclick="cambiarEstado('<?php echo $wh->AcctCode ?>', 0)">Sí</button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" onclick="cambiarEstado('<?php echo $wh->AcctCode ?>', 1)">No</button>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

<script>
    function cambiarEstado(acctCode, nuevoEstado) {
        if (confirm("¿Estás seguro de cambiar el estado de esta cuenta?")) {
            fetch('php/actualizar_estado_cuentafinancieraCE.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `acctCode=${acctCode}&estado=${nuevoEstado}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload(); // Recarga la página para ver el cambio
            })
            .catch(error => {
                alert("Error al actualizar: " + error);
            });
        }
    }
</script>

      
<?php    include_once "footer.php"; ?>