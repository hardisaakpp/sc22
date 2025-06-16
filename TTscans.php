<?php
    include_once "header.php";

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
    $barcodes = null;//array_map(fn($item) => $item->codigoBarras, $lsArts);


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
  
<div class="content">
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
    const codigosSet = new Set(<?php echo json_encode($barcodes); ?>);

    function playSound() {
        var audioContext = new (window.AudioContext || window.webkitAudioContext)();
        var oscillator = audioContext.createOscillator();
        oscillator.type = 'sine'; 
        oscillator.frequency.setValueAtTime(440, audioContext.currentTime); 
        oscillator.connect(audioContext.destination);
        oscillator.start();
        setTimeout(function() {
        oscillator.stop();
        }, 1000);
    }

     document.getElementById("boton").addEventListener("click", () => {
        playSound();
     });

    function chargeErrors() {
        const listaErrores = document.getElementById("lsError");
        const errores = Array.from(listaErrores.children);

        errores.forEach((item) => {
            const codebar = item.getAttribute("data-barcode");
            const quantity = parseInt(item.getAttribute("data-quantity"), 10) || 1;

            insertCodeBar(codebar, quantity);
            listaErrores.removeChild(item);
        });
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
        const quantity = parseInt(document.getElementById("quantity").value, 10);
        if (!barcode || quantity <= 0) return;

        insertCodeBar(barcode, quantity);
        document.getElementById("quantity").value = 1;
        document.getElementById("searchInput").value = "";
        document.getElementById("searchInput").focus();
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

        function insertCodeBar(codebar, quantity = 1) {
            var parametros = {
                "id_user": "<?php echo $userId; ?>",
                "barcode": codebar,
                "ID_CONTEO": "<?php echo $idcab; ?>",
                "cantidad": quantity
            };

            $.ajax({
                data: parametros,
                url: 'php/barcode_insert.php',
                type: 'POST',
                timeout: 3000,
                success: function () {
                    const lista = document.getElementById("lsSaved");
                    const items = lista.getElementsByTagName("li");
                    let encontrado = false;

                    for (let i = 0; i < items.length; i++) {
                        let texto = items[i].innerText;
                        if (texto.startsWith(codebar + " (x")) {
                            // Extraer cantidad actual y sumarle la nueva
                            let cantidadActual = parseInt(texto.match(/\(x(\d+)\)/)[1]);
                            let nuevaCantidad = cantidadActual + parseInt(quantity);
                            items[i].innerText = `${codebar} (x${nuevaCantidad})`;
                            encontrado = true;
                            break;
                        }
                    }

                    if (!encontrado) {
                        let node = document.createElement('li');
                        node.appendChild(document.createTextNode(`${codebar} (x${quantity})`));
                        lista.appendChild(node);
                    }

                    let conta = document.getElementById("ItmsSav");
                    conta.value = parseInt(conta.value, 10) + parseInt(quantity, 10);
                },
                error: function () {
                    const lista = document.getElementById("lsError");
                    const node = document.createElement('li');
                    node.setAttribute("data-barcode", codebar);
                    node.setAttribute("data-quantity", quantity);
                    node.appendChild(document.createTextNode(`${codebar} (x${quantity})`));
                    lista.appendChild(node);
                    console.log('error:' + codebar);
                }

            });
        }


    </script>

</div>

<?php   
include_once "footer.php";
?> 