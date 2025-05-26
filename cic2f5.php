<?php
include_once "header.php";

// Si no es admin, no abre
if ($userAdmin == 1 || $userAdmin == 6) {
?>



    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Sincronizar Cajas [SAP B1]</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    




<div class="content">

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

       
                
            <form id="syncForm">
                <div class="input-group">
                    <label for="fecha" class="form-control">Selecciona una fecha:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" required>
                    <label for="empresa" class="form-control">Selecciona la empresa:</label>
                    <select id="empresa" name="empresa" class="form-control" required>
                        <option value="MT">MABELTRADING</option>
                        <option value="CE">COSMEC</option>
                    </select>
                    <button type="submit" class="form-control">Ejecutar ▶️</button>
                </div>
            </form>





            <!-- Spinner de carga -->
            <div id="loadingSpinner" style="display: none; margin-top: 20px; text-align: center;">
                <div class="spinner"></div>
                <p style="font-weight: bold; color: #333;">Procesando, por favor espera...</p>
            </div>

            <!-- Barra de progreso -->
            <div id="progressBar" style="margin-top: 20px; width: 100%; background-color: #eee; border-radius: 5px; overflow: hidden;">
                <div id="progressBarFill" style="width: 0%; height: 30px; background-color: #4CAF50; text-align: center; color: white; line-height: 30px;">0%</div>
            </div>

            <ul id="statusList" style="margin-top: 20px; padding-left: 20px;"></ul>

            </div>
        </div>
    </div>



































</div>


<style>
    .spinner {
        border: 6px solid #f3f3f3;
        border-top: 6px solid #4CAF50;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #progressBarFill.loading {
        background: repeating-linear-gradient(
            45deg,
            #4CAF50,
            #4CAF50 10px,
            #45a049 10px,
            #45a049 20px
        );
        animation: progressAnim 1s linear infinite;
    }

    @keyframes progressAnim {
        0% { background-position: 0 0; }
        100% { background-position: 40px 0; }
    }
</style>

<script>
document.getElementById('syncForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const fecha = document.getElementById('fecha').value;
    const empresa = document.getElementById('empresa').value;
    const statusList = document.getElementById('statusList');
    const progressBarFill = document.getElementById('progressBarFill');
    const submitButton = this.querySelector('button');
    const spinner = document.getElementById('loadingSpinner');

    // Reset UI
    statusList.innerHTML = '';
    progressBarFill.style.width = '0%';
    progressBarFill.textContent = '';
    progressBarFill.classList.add('loading');
    spinner.style.display = 'block';
    submitButton.disabled = true;

    // Obtener almacenes
    const almacenesRes = await fetch('php/get_almacenes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ empresa })
    });

    const almacenes = await almacenesRes.json();
    const total = almacenes.length;

    for (let i = 0; i < total; i++) {
        const whsCica = almacenes[i];

        const res = await fetch('php/ejecutar_sp_por_almacen.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ whsCica, fecha })
        });

        const resultados = await res.json();

        resultados.forEach(item => {
            const li = document.createElement('li');
            li.textContent = item.mensaje;
            li.style.color = item.exito ? 'green' : 'red';
            statusList.appendChild(li);
        });

        const porcentaje = Math.round(((i + 1) / total) * 100);
        progressBarFill.style.width = porcentaje + '%';
        progressBarFill.textContent = porcentaje + '%';
    }

    // Finalizar
    progressBarFill.classList.remove('loading');
    spinner.style.display = 'none';
    submitButton.disabled = false;
});
</script>


<?php  
} else {
    echo "<div class='alert alert-danger'>No tienes permiso para ver esta página.</div>";
}

include_once "footer.php";
?>
