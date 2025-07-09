<?php
    include_once "header.php";
    if($userAdmin<>1){  
        echo ('ACCESO DENEGADO');
        exit();
    }
?>

<div class="content">
    <div class="card mt-4">
        <div class="card-header"><strong>Herramientas de Mantenimiento</strong></div>
        <div class="card-body">
            <button id="btnActualizarArticulos" class="btn btn-primary">Actualizar artículos</button>
            <div id="loader" style="display:none;margin-top:15px;">
                <span class="spinner-border text-primary" style="width:2rem;height:2rem;"></span> Procesando...
            </div>
            <div id="resultado" style="margin-top:15px;"></div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnActualizarArticulos').onclick = function() {
        document.getElementById('loader').style.display = 'block';
        document.getElementById('resultado').innerHTML = '';
        fetch('php/ejecutar_sp.php?sp=sp_articuloF5')
            .then(r => r.json())
            .then(data => {
                document.getElementById('loader').style.display = 'none';
                if(data.success) {
                    document.getElementById('resultado').innerHTML = '<div class="alert alert-success">' +
                        'Resultado 1: ' + data.result1 + '<br>Resultado 2: ' + data.result2 + '</div>';
                } else {
                    document.getElementById('resultado').innerHTML = '<div class="alert alert-danger">' + (data.error || 'Error al ejecutar el procedimiento.') + '</div>';
                }
            })
            .catch(err => {
                document.getElementById('loader').style.display = 'none';
                document.getElementById('resultado').innerHTML = '<div class="alert alert-danger">Error de red o servidor.</div>';
            });
    };
</script>

<?php    include_once "footer.php"; ?>