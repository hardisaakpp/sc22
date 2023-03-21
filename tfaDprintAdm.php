

<?php
  session_start();
  include_once "php/bd_StoreControl.php";

  // Validating Session
  if(strlen($_SESSION['username'])==0)
  {
    //header('location:index.php');
    exit();
  }
  
  //recupero ID_CONTEO 
  $idcab = $_GET["idcab"];
  //echo $idConteo;
  $username = $_SESSION['username'];
  $idU = $_SESSION['idU'];



 

  //cabecera 
  $sent = $db->prepare("SELECT top 1 s.* , tfa.responsable
  from StockCab s left join StockCab_tfa tfa on s.id=tfa.fk_id_StockCab
  where s.id= ? ");
$sent->execute([$idcab]);

$result = $sent->fetch(PDO::FETCH_OBJ);
  $creado =$result->date . $result->time;
  $id_cab= $result->id;
  $responsable= $result->responsable;
  $tomaCode = $result->tomaCode;
//detalle toma actual INICIAL-CONTEO
$s1 = $db->prepare("select det.id, ID_articulo, descripcion, nombreGrupo, stock, scan, conteo, reconteo, estado 
  from stockdet det join Articulo a on det.FK_ID_articulo=a.id
  where FK_id_StockCab= ? order by nombreGrupo,ID_articulo" );
  $s1->execute([$id_cab]);
$citems = $s1->fetchAll(PDO::FETCH_OBJ);    
    
    
   

?>
<html>
<head>
    <style>
        p.inline {display: none;}
        span { font-size: 12px;}
        </style>
        <style type="text/css" media="print">
            @page 
            {
                size: auto + 2px;   /* auto is the initial value */
                margin: 8mm;  /* this affects the margin in the printer settings */
            }

            @media print {
                table {
                    border: solid #000 !important;
                    border-width: 1px 0 0 1px !important;
                }
                th, td {
                    border: solid #000 !important;
                    border-width: 0 1px 1px 0 !important;
                    padding-left: 5px;
                    padding-bottom: 3px;
                    font-size: 12px;
                }
            }


      
    </style>
</head>
<body onload="window.print();">


  
  
 <h3 ALIGN=center> TOMA FISICA ALEATORIA <?php echo $idcab; ?></h3>

 <strong>Fecha de impresion:</strong> <script>
                            date = new Date().toLocaleString();
                            document.write(date);
                            </script></br>
   <strong> Usuario: </strong> <?php echo $username; ?> </br>
   <strong> Responsable toma fisica: </strong><?php echo $responsable; ?>  <br><br>
  

   


                        
                        <table>
                            <thead>
                                <tr>
                                    <th>ItemCode</th>
                                    <th>Descripcion</th>
                                    <th>Grupo</th>
                                    <th>Stock</th>
                                    <th>Conteo</th>
                                    <th>Reconteo</th>
                                    <th>Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php   foreach($citems as $citem){ 
                                $diferencia = $citem->reconteo-$citem->stock;                                
                                ?>


                                <tr>
                                    <td><?php echo $citem->ID_articulo ?></td>
                                    <td><?php echo $citem->descripcion ?></td>
                                    <td><?php echo $citem->nombreGrupo ?></td>
                                    <td><?php echo $citem->stock ?></td>
                                    <td><?php echo $citem->conteo ?></td>
                                    <td><?php echo $citem->reconteo ?></td>
                                    <td><?php echo $diferencia ?></td>
                                    <td><?php 
                                    if ($diferencia!=0) {
                                        echo '❗';
                                    }
                                    
                                    ?></td>
                                </tr>
                            
                            <?php }; ?>   
            
                            </tbody>

                        </table>




  
		<?php
/*

foreach ($reconteos as $reconteo) {
  $CODBAR = $reconteo->CodeBars;
  $CODE = $reconteo->ItemCode;
  $CONTEO = $reconteo->CONTEO;
  echo "<div class='box box2'>
  <span ><b>Item: ".$CODE."</b></span>
  <div class='box3'>"
  .bar128(stripcslashes($CODBAR)).
  "</div>
  <span ><b>Unidades: ".$CONTEO."</b></span>
  </div>
  ";

}

*/




		?>
	</div>
</body>
</html>