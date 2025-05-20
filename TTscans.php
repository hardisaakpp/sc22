<?php
    include_once "header.php";

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];

    $s1 = $db->query("EXEC sp_getCodBarEnabled '$idcab';");
    $lsArts = $s1->fetchAll(PDO::FETCH_OBJ);

    // Extraer solo los códigos de barra
    $barcodes = array_map(fn($item) => $item->codigoBarras, $lsArts);


?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMA FISICA TOTAL <?php  echo $idcab ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">

                    <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button type="button" class="dropdown-item" onclick="window.location.href='TTscanDel.php?idcab=<?php echo $idcab ?>'">ELIMINAR ITEMS</button>
                        <button class="dropdown-item" onclick="window.location.href='TTscanRes.php?idcab=<?php echo $idcab ?>'">RESUMEN</button>
                        <button type="button" class="dropdown-item" onclick="window.location.href='wllcm.php'">X</button>
                    </div>
                    </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
<!-- /.breadcrumbs-->
  
<div class="content">
<!----------------- Content -------------------->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">TEMPORAL</strong>
            </div>
            <div class="card-body">                    
                    <div class="form-group">
                        <input type="number" required class="form-control"  jsname="quantity" autocomplete="off" value="1" 
                        id="quantity">
                        <input type="text" placeholder="Codigo de barras" required class="form-control"  jsname="YPqjbf" autocomplete="off" tabindex="0" aria-label="Nombre" value="" 
                        maxlength="30" dir="ltr" autofocus="" id="searchInput" onkeypress="clickPress(event)">
                    </div>
                    <ul id="lsTemp">
                    </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">ERRORES</strong>
                <input type="button" value="Reenviar" onclick="chargeErrors();">
            </div>
            <div class="card-body">                    
                <ul id="lsError">
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">ENVIADOS</strong>
                <input type='number' value=-1 id='ItmsSav' style='border:0; text-align:center;' readonly>
            </div>
            <div style="overflow-x: hidden; overflow-y: scroll;">
            <ul id="lsSaved">
       
            </ul>
            </div>
          
        </div>
    </div>


<script type="text/javascript"> 
     // Crear un Set para búsquedas rápidas
    const codigosSet = new Set(<?php echo json_encode($barcodes); ?>);


    function playSound() {
        // Crear el contexto de audio
        var audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Crear un oscilador
        var oscillator = audioContext.createOscillator();
        oscillator.type = 'sine'; // Tipo de onda (puede ser 'sine', 'square', 'sawtooth', 'triangle')
        oscillator.frequency.setValueAtTime(440, audioContext.currentTime); // Frecuencia en Hz (440 Hz es la nota A)

        // Conectar el oscilador al destino (altavoces)
        oscillator.connect(audioContext.destination);

        // Iniciar el oscilador
        oscillator.start();

        // Detener el oscilador después de 1 segundo
        setTimeout(function() {
        oscillator.stop();
        }, 1000);
    }

     // Ejemplo de uso: Llamar a la función para emitir un sonido
     document.getElementById("boton").addEventListener("click", () => {
        playSound();
     });

    function chargeErrors() {
        
        const origen = document.getElementById("lsError");
        const destino = document.getElementById("lsTemp");

            Array.from(origen.children).forEach(
                (item) => mover(item, destino)
            );
        }

        function mover(item, destino) {
        destino.appendChild(item);
    }

    function indexesLX(){

        try {
            setInterval('contador()',500);
        }
            catch(x) { 
        }
    }

    function contador(){
        var node = document.getElementById("lsTemp").firstChild;
        let codebar=node.innerText;
        document.getElementById("lsTemp").removeChild(node);
        insertCodeBar(codebar);

    }

    function contadoradd(barcode, description = '', isManual = false){
        var node = document.createElement('li');
        if (isManual) {
            node.appendChild(document.createTextNode(barcode));
        } else {
            node.appendChild(document.createTextNode(barcode));
        }
        document.getElementById("lsTemp").appendChild(node);
    }

    
    function clickPress(event) {
        if (event.key === "Enter") {
            const barcode = document.getElementById("searchInput").value.trim().replaceAll("'", "-");
            if (!barcode) return;

            if (codigosSet.has(barcode)) {
                addBarcodeToList(barcode);
            } else {
                playSound();
                if (confirm("El código de barras no existe. ¿Desea agregarlo?")) {
                    addBarcodeToList(barcode, '', true);
                }
            }
        }
    }







        function addBarcodeToList(barcode, description = '', isManual = false) {
            let ivez = (document.getElementById("quantity")).value;
            for (let index = 0; index < ivez; index++) {
                contadoradd(barcode, description, isManual);   
            }
            document.getElementById("quantity").value = 1;
            document.getElementById("searchInput").value = "";
            document.getElementById("searchInput").focus();
        }

        var boton = document.getElementById("boton");

            boton.addEventListener("click", () => {
                
                var input = document.getElementById("searchInput").replaceAll("'", "-").replaceAll("'", "-").trim();
                var valor = input.value;

                alert("El valor del campo es:"+ valor);
                
            });

        function insertCodeBar(codebar)
            { 
            var parametros = 
            {
                "id_user" : "<?php echo $userId ; ?>" ,
                "barcode" : codebar ,
                "ID_CONTEO" : "<?php echo $idcab; ?>" 
            };

            $.ajax({
                data: parametros,
                url: 'php/barcode_insert.php',
                type: 'POST',
                timeout: 3000,
                
        

                success: function()
                {
                    var node = document.createElement('li');
                    node.appendChild(document.createTextNode(codebar));
                    document.getElementById("lsSaved").appendChild(node);
                    conta = document.getElementById("ItmsSav");
                    conta.value = parseInt(conta.value,10) + 1;
                },
                error: function(){
                    var node = document.createElement('li');
                    node.appendChild(document.createTextNode(codebar));
                    document.getElementById("lsError").appendChild(node);
                    console.log('error:'+codebar);
                }
            });
            }
    </script>

</div>

<?php   
include_once "footer.php";
?> 