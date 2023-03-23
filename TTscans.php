<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
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
                        <button type="button" class="dropdown-item" onclick="window.location.href='TTscanRes.php?idcab=<?php echo $idcab ?>'">RESUMEN</button>
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
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">TEMPORAL</strong>
            </div>
            <div class="card-body">                    
                    <div class="form-group">
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
            </div>
            <div style="overflow-x: hidden; overflow-y: scroll;">
            <ul id="lsSaved">
       
            </ul>
            </div>
          
        </div>
    </div>


<script type="text/javascript"> 
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
            catch(x) { /* puede usarse cualquier otro nombre en lugar de 'x' */
            //document.getElementById("ejemplo").innerHTML = x.message;
        }

            
           // setInterval('contadoradd()',2000);
        }

        function contador(){


            var node = document.getElementById("lsTemp").firstChild;
           // document.getElementById("lsSaved").appendChild(node);
            
           // insertCodeBar(node.innerText);
           let codebar=node.innerText;
            document.getElementById("lsTemp").removeChild(node);
            insertCodeBar(codebar);

        }
        function contadoradd(barcode){
            var node = document.createElement('li');
            node.appendChild(document.createTextNode(barcode));
            //var node = document.getElementById("myList2").lastChild;
            document.getElementById("lsTemp").appendChild(node);

        }

     function clickPress(event) {
        if (event.keyCode == 13 && !((document.getElementById("searchInput").value).trim()==="")) {
           // console.log(<?php echo $userId ; ?>+ '  ' +((document.getElementById("searchInput")).value).replaceAll("'", "-").trim() + '  ' + <?php echo $idcab; ?>);
            contadoradd((document.getElementById("searchInput").value).replaceAll("'", "-").trim());
            document.getElementById("searchInput").value = "";
            document.getElementById("searchInput").focus();
        }
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
            
          /*  beforesend: function()
            {
              $('#mostrar_mensaje').html("Mensaje antes de Enviar");
            },*/

            success: function()
            {
                var node = document.createElement('li');
                node.appendChild(document.createTextNode(codebar));
                //var node = document.getElementById("myList2").lastChild;
                document.getElementById("lsSaved").appendChild(node);
            
            },
             error: function(){
                var node = document.createElement('li');
                node.appendChild(document.createTextNode(codebar));
                //var node = document.getElementById("myList2").lastChild;
                document.getElementById("lsError").appendChild(node);
                console.log('error:'+codebar);
            }
          });
        }
</script>
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   
include_once "footer.php";
 ?>