<?php
    include_once "header.php";
    //si no es admin no abre
    if ($userAdmin==1 || $userAdmin==6){
?>
<style>
        #progressBar {
            width: 100%;
            background-color: #eee;
        }
        #progressBarFill {
            width: 0%;
            height: 30px;
            background-color: #4CAF50;
            text-align: center;
            color: white;
        }
</style>

    <!-- Breadcrumbs-->
        <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Sincronización de Cajas</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li>
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

    <h2>Sincronizar Cajas</h2>
    <form id="syncForm">
        <label for="fecha">Selecciona una fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
        <button type="submit">Ejecutar</button>
    </form>

    <div id="progressBar" style="margin-top: 20px;">
        <div id="progressBarFill">0%</div>
    </div>

    <ul id="statusList"></ul>

    <script>
        document.getElementById('syncForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const fecha = document.getElementById('fecha').value;
            const statusList = document.getElementById('statusList');
            const progressBarFill = document.getElementById('progressBarFill');
            statusList.innerHTML = '';
            progressBarFill.style.width = '0%';
            progressBarFill.textContent = '0%';

            fetch('ejecutar_sp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fecha })
            })
            .then(res => res.json())
            .then(data => {
                data.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.textContent = item.mensaje;
                    li.style.color = item.exito ? 'green' : 'red';
                    statusList.appendChild(li);
                    progressBarFill.style.width = `${((index + 1) / data.length) * 100}%`;
                    progressBarFill.textContent = `${((index + 1) / data.length) * 100}%`;
                });
            });
        });
    </script>

<!---------------------------------------------->

<?php  

    } else {
        echo "<div class='alert alert-danger'>No tienes permiso para ver esta página.</div>";
    }
  
include_once "footer.php"; ?>