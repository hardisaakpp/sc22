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
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-success">ESCANEAR</button>
                                <button type="button" class="btn btn-outline-warning" onclick="window.location.href='TTscanDel.php?idcab=<?php echo $idcab ?>'">ITEMS PARA ELIMINAR</button>
                                <button type="button" class="btn btn-outline-warning" onclick="window.location.href='TTscanRes.php?idcab=<?php echo $idcab ?>'">RESUMEN</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'">X</button>
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
<!---------------------------------------------->
<script>
  

    function chargeTFA(){


       <?php foreach($users as $use){ ?>  
            createTFA('<?php echo $use->WhsCode ?>', '<?php echo $use->Quantity ?>');
        <?php } ?>

    }

    function createTFA(WhsCode, Quantity) {
    
        var parametros = 
            {
                "WhsCode" : WhsCode ,
                "Quantity" : Quantity
            };

            $.ajax({
                data: parametros,
                url: 'php/loadTomaFisicaAleatoria.php',
                type: 'POST',
                //    timeout: 3000,
                success: function(data){
                    //console.log(data);
                    es=document.getElementById("tc"+WhsCode );
                    es.innerText = '✔️';
                //$("#find").click();
                   /* if (data==1) {
                        Swal.fire({
                        icon: 'success',
                        title: '👌😀',
                        text: 'Clave actualizada correctamente!'
                        })
                    } else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'La clave actual es incorrecta!'
                        })
                    }*/
                },
                error: function(){
                    console.log('error de conexion - revisa tu red');
                }
            });
    }
</script>

    <div class="col-lg-6">
        <div class="card">
            <!--  <div class="card-header">
                <strong class="card-title">TOMA FISICA TOTAL <?php  echo $idcab ?></strong>
            </div>-->
            <div class="card-body">
              <!--  <form id="frmLoad" method="post" enctype="multipart/form-data" class="form-horizontal">-->
                    
                    <div class="form-group">
                        <label for="searchInput" class=" form-control-label" >Codigo de barras</label>
                        <input type="text" required class="form-control"  jsname="YPqjbf" autocomplete="off" tabindex="0" aria-label="Nombre" value="" 
                        maxlength="30" dir="ltr" autofocus="" id="searchInput" onkeypress="clickPress(event)">
                    </div>
            </div>
            
                <!-- </form>-->
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <textarea id="msgbox" style="border: none; margin: 5px; color-font:gray;" class="form-control"  rows="10" cols="23" readonly ></textarea>
        </div>
    </div>


<script type="text/javascript"> 
     function clickPress(event) {
        if (event.keyCode == 13 && !((document.getElementById("searchInput").value).trim()==="")) {
            console.log(<?php echo $userId ; ?>+ '  ' +((document.getElementById("searchInput")).value).replaceAll("'", "-").trim() + '  ' + <?php echo $idcab; ?>);
            saludame();
            document.getElementById("msgbox").value = (document.getElementById("searchInput").value).replaceAll("'", "-").trim() + "\n" + document.getElementById("msgbox").value + "\n";
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

      function saludame()
        { 
          var parametros = 
          {
            "id_user" : "<?php echo $userId ; ?>" ,
            //"id_user" : "1",
            //"barcode" : "7333209222930",
            "barcode" : ((document.getElementById("searchInput")).value).replaceAll("'", "-").trim() ,
            "ID_CONTEO" : "<?php echo $idcab; ?>" 
          };

          $.ajax({
            data: parametros,
            url: 'php/barcode_insert.php',
            type: 'POST',
            
            beforesend: function()
            {
              $('#mostrar_mensaje').html("Mensaje antes de Enviar");
            },

            success: function(data)
            {
                console.log(data);
              $('#mostrar_mensaje').html((document.getElementById("searchInput")).value);
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