
 
<?php
include_once "header.php";
?>
<!-- -----------------------------------------------------------------------------------------------------------
        --------------------------------------------------------------------------------------------------------------
        --------------------------------------------------------------------------------------------------------------
        ------------------------------------------------------------------------------------------------------------------>

        <div>

            <form class="form-inline" method="GET" >
            
                <input type="text" class="form-control mb-2 mr-sm-2" 
                id="sx" name="sx"  placeholder="NumDoc" value="">
                <input type="submit" name="find" value="Buscar 🔎" class="form-control"
                    onclick=this.form.action="soltr.php">

            </form>
        </div>
        <h3 style="color:gray";>Solicitud de Translado:</h3>
            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ------------------------------------------------------------------------------------------------------------------>


<?php
//validar que es ADMINISTRADOR
if($userAdmin==5 || $userAdmin==1)
{
    $idU = $_SESSION['idU'];
    //verifico si recibi numero de translado
    if (!isset($_GET["sx"])){
            $solicitud=(0);
        }else{
            $solicitud=$_GET['sx'];
            //SP para buscar la solicitud si existe o no 
            $sentencia3 = $db->query("
                exec [sp_getSolTr] ". $solicitud ."  " );
            $soltra = $sentencia3->fetchObject();
                $res = $soltra->RES;
            // si se encontro la solicitud de pedido
            if ($res==0) {
                echo "<h3 style='color: RED';> No se encontro el numero de solicitud.</h3>
                 <p style='color: GRAY';> (Solo puede visualizar datos del año actual)</p>";
                   
            }else {
                $sentencia = $db->query("
                    select * from StockCab_ST st 
                    join StockCab sc on st.fk_id_stockCab=sc.id 
                    where st.solicitud= ". $solicitud ."  " );
                $cab = $sentencia->fetchObject();
                    //campos STOCK_CAB
                    $fecROW = $cab->date.' '.substr($cab->time,0,5);
                    $id_cab = $cab->id;
                    //campos ST
                    $fechaSol = $cab->fecha_sol;
                    $origen = $cab->origen;
                    $transferencia = $cab->transferencia;
                    $destino = $cab->destino;
                
                $txtTransfer = '';
                if($transferencia==0){
                    $txtTransfer="bg-warning text-dark'> Nro.Transferencia aún NO generado";
                }else{
                    $txtTransfer="bg-light'> Transferencia Nro.".$transferencia;
                }

                echo "
                <div class='container'>
                <div class='row row-cols-2 row-cols-lg-6 g-2 g-lg-3'>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Solicitud No.".$solicitud."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'> Fecha solicitud ".$fechaSol."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Origen: ".$origen."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Destino: ".$destino."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border  ".$txtTransfer."</div>
                    </div>
                    <div class='col'>
                    <div class='p-2 border bg-light'>Actualización: ".$fecROW."</div>
                    </div>
                </div>
                </div>";

             
           
            ?>

            <div class="row">

            <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
            <link rel="stylesheet" type="text/css" href="js/syntax/shCore.css">
            <link rel="stylesheet" type="text/css" href="js/demo.css">
            <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
            <script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
            <script type="text/javascript" language="javascript" src="js/syntax/shCore.js"></script>
            <script type="text/javascript" language="javascript" src="js/demo.js"></script>
            <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                    $('#example').DataTable();
                    $('#sx').val('<?php echo  $solicitud;?>');
                    } );
            </script>

            
            <!-- -----------------------------------------------------------------------------------------------------------
            -------------------MENU--PESTAÑAS---------------------------------------
            ------------------------------------------------------------------------------------------------------------------>
            <ul class="nav nav-tabs nav-justified nav-dark">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo "soltr.php?sx=" . $solicitud?>">Resumen</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#" >Escanear</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo "soltrDel.php?sx=" . $solicitud?>">Eliminar</a>
            </li>
            </ul>
            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ----------------SCAN-------------------------------------------------------------------->

            
            <div  class="form-group" style="margin: 5px;">
            <input type="text" required class="form-control"  jsname="YPqjbf" autocomplete="off" tabindex="0" aria-label="Nombre" value="" 
            dir="ltr" autofocus="" id="searchInput" onkeypress="clickPress(event)">
            <!-- <input type="button" value="SALUDAME" class="btn btn-secondary btn-sm" id="boton" onclick="saludame();"> -->
            </br>
            <textarea id="msgbox" style="border: none; margin: 5px; color-font:gray;" class="form-control"  rows="10" cols="23" readonly ></textarea>
            </div>


            <script type="text/javascript">
                function clickPress(event) {
                    if (event.keyCode == 13 && !((document.getElementById("searchInput").value).trim()==="")) {
                        console.log("<?php echo $id_cab; ?>");
                        console.log(((document.getElementById("searchInput")).value).replaceAll("'", "-").trim());
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
                        "id_user" : "<?php echo $idU ; ?>" ,
                        "barcode" : ((document.getElementById("searchInput")).value).replaceAll("'", "-").trim() ,
                        "id_cab" : "<?php echo $id_cab; ?>" 
                    };

                    $.ajax({
                        data: parametros,
                        url: 'php/insert_stockScan.php',
                        type: 'POST',
                        
                        beforesend: function()
                        {
                        $('#mostrar_mensaje').html("Mensaje antes de Enviar");
                        console.log('OK antes');
                        },

                        success: function()
                        {
                            console.log('succes');
                        $('#mostrar_mensaje').html((document.getElementById("searchInput")).value);
                        }
                    });
                    }
                </script>

            <!-- -----------------------------------------------------------------------------------------------------------
            --------------------------------------------------------------------------------------------------------------
            ------------------------------------------------------------------------------------>


            <?php
                        
            }
        }       
}else{
    ?>
    <h2 style="color:gray";>Acceso denegado</h2>
    <?php
 }
 
 ?>
 <!---------------------------------------------->
 <!--------------Fin Content -------------------->
 <!---------------------------------------------->
 </div>
 
 
 
       
 <?php  
 

include_once "footerSF.php" ?>