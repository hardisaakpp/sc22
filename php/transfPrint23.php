

<?php
  session_start();
  include_once "bd_StoreControl.php";

  // Validating Session
  /*if(strlen($_SESSION['username'])==0)
  {
    //header('location:index.php');
    exit();
  }*/
  //if($userAdmin==5 || $userAdmin==1){
     // header('location:listar.php');
   //   exit();
//  }
  //recupero ID_CONTEO 
  $solicitud = $_GET["cc"];

  //echo $idConteo;
  $username = $_SESSION['username'];
  $idU = $_SESSION['idU'];


/*
  $sentencia = $db->query("
                    select * from StockCab_ST st 
                    join StockCab sc on st.fk_id_stockCab=sc.id 
                    where st.solicitud in (". $solicitud .")  " );
                $cab = $sentencia->fetchObject();
                    //campos STOCK_CAB
                    $fecROW = $cab->date.' '.substr($cab->time,0,5);
                    $id_cab = $cab->id;
                    //campos ST
                    $fechaSol = $cab->fecha_sol;
                    $origen = $cab->origen;
                    $transferencia = $cab->transferencia;
                    $destino = $cab->destino;
                    $cc = $cab->cartones;
*/


                $sentencia = $db->prepare("
                    select * from StockCab_ST st 
                    join StockCab sc on st.fk_id_stockCab=sc.id 
                    where st.solicitud in (". $solicitud .")  " );
                $sentencia->execute([]);
                
                $rows = $sentencia->fetchAll(PDO::FETCH_OBJ);



?>
<html>
<head>
    <style>
    p.inline {display: inline-block;}
    span { font-size: 13px;}
    </style>
    <style type="text/css" media="print">
        @page 
        {
            size: auto + 2px;   /* auto is the initial value */
            margin: 4mm;  /* this affects the margin in the printer settings */
        }
        @media print {
            .pagebreak { page-break-before: always; } /* page-break-after works, as well */
        }
    .wrapper {
      display: grid;
      /*grid-template-columns: repeat(2, 1fr);*/
      /*grid-auto-columns: auto;*/
      grid-template-columns: 400 px;
      grid-template-rows: 397px;
      row-gap: 3px;
      column-gap: 5px;
      align-content: center;
    }

    .wrapper > div {

      background-color: rgba(255, 255, 255, 0.8);
      
      padding: 0px 0;

    }
    .box1 {

      grid-column: 1;
      grid-row: 1;
      border: 1px solid rgba(0, 0, 0, 0.8);
      padding: 0px;
      margin: 0px;
      font-size: 15px;
      text-align: left;


    }
    .box2 {
    
      padding: 0px;
      margin: 0px;
      
      text-align: center;
    }
    .box3 {
      text-align: center;
      display: flex;
      justify-content: center;
    }



      
    </style>
</head>
<body onload="window.print();">

<div class="wrapper">
  
 
  

<?php
    include 'barcode128.php';




   foreach($rows as $citem){



        for ($i=1; $i < $citem->cartones +1; $i++) { 
            echo "
            <div class='pagebreak'> 
            <div class='box1'>
            <p><h1 ALIGN=center>Destino: ". $citem->destino."</h1> 
            <h3 ALIGN=center> Fecha:<script>
                date = new Date().toLocaleString();
                document.write(date);
                </script></br>
            Usuario:".$username."</br>
            </h3>

            <div class='box box2'>
            <span ><b>TRANSFERENCIA</b></span>
            <div class='box3'>"
            .bar128(stripcslashes($citem->transferencia)).
            "</div>
            </br>
            <h3 ALIGN=center> N° de Cartón: ".$i . " de " . $citem->cartones."</h3>
            </div>    
            </div> 
            ";
        } 


        $sentenc1 = $db->prepare("
        update StockCab_ST set estado='IMP' where solicitud=".$citem->solicitud."
        " );
$sentenc1->execute();




    }

?>
 	</div>
</body>
</html>