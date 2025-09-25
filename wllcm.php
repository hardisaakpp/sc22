<?php
include_once "header.php";
?>
<div class="content">
  <!---------------------------------------------->
  <!----------------- Content -------------------->
  <!---------------------------------------------->
  <h1 ALIGN="center" class="display-6">Bienvenido</h1>

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700;800&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      min-height: 100vh;

    }

    body .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: 40px 0;
    }

    body .container .card {
      position: relative;
      min-width: 320px;
      height: 440px;
      box-shadow: inset 5px 5px 5px rgba(0, 0, 0, 0.2),
        inset -5px -5px 15px rgba(255, 255, 255, 0.1),
        5px 5px 15px rgba(0, 0, 0, 0.3), -5px -5px 15px rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      margin: 30px;
      transition: 0.5s;
    }

    body .container .card:nth-child(1) .box .content a {
      background: #2196f3;
    }

    body .container .card:nth-child(2) .box .content a {
      background: #e91e63;
    }

    body .container .card:nth-child(3) .box .content a {
      background: #23c186;
    }

    body .container .card .box {
      position: absolute;
      top: 20px;
      left: 20px;
      right: 20px;
      bottom: 20px;
      background: #2a2b2f;
      border-radius: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      transition: 0.5s;
    }

    body .container .card .box:hover {
      transform: translateY(-50px);
    }

    body .container .card .box:before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 50%;
      height: 100%;
      background: rgba(255, 255, 255, 0.03);
    }

    body .container .card .box .content {
      padding: 20px;
      text-align: center;
    }

    body .container .card .box .content h2 {
      position: absolute;
      top: -10px;
      right: 30px;
      font-size: 8rem;
      color: rgba(255, 255, 255, 0.1);
    }

    body .container .card .box .content h3 {
      font-size: 1.8rem;
      color: #fff;
      z-index: 1;
      transition: 0.5s;
      margin-bottom: 15px;
    }

    body .container .card .box .content p {
      font-size: 1rem;
      font-weight: 300;
      color: rgba(255, 255, 255, 0.9);
      z-index: 1;
      transition: 0.5s;
    }

    body .container .card .box .content a {
      position: relative;
      display: inline-block;
      padding: 8px 20px;
      background: black;
      border-radius: 5px;
      text-decoration: none;
      color: white;
      margin-top: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      transition: 0.5s;
    }

    body .container .card .box .content a:hover {
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6);
      background: #fff;
      color: #000;
    }
  </style>

  <?php
  if ($userAdmin == 2) // si es TIENDA
  {

    $sentencia = $db->query("exec sp_getStockTotalXAlm " . $_SESSION['idU'] . " ");
    $quser = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if (count($quser) > 0) {
      $sentencia2 = $db->query("exec sp_getStockTotalXAlm " . $_SESSION['idU'] . " ");
      $TEMP1 = $sentencia2->fetchObject();
      $xpuntaje1 = $TEMP1->Inventario;
      $xtransit = $TEMP1->WhsCode;
    } else {
      $xpuntaje1 = 0;
      $xtransit = '.';
    }

    $sentencia4 = $db->query("exec sp_getStockTransitorio " . $_SESSION['idU'] . " ");
    $qtrans = $sentencia4->fetchAll(PDO::FETCH_OBJ);



  ?>


    <div class="row">


      <div class="container">
        <?php if ($xpuntaje1 > 0) {  ?>
          <div class="card">
            <div class="box">
              <div class="content">
                <h2>🚛</h2>
                <div class="col-md-6" style="text-align: center; display: flex; justify-content: center;">
                  <img src="images/dash_icon.jpg" class="img-fluid rounded-start" alt="...">
                </div>
                <h3>

                  Transitoria <?php echo $xtransit; ?></h3>
                <p>
                  <?php foreach ($qtrans as $mascota) {
                    if ($mascota->ANTIGUEDAD == 'RETRASADO') { ?>



                <h5 class="card-title" style=" color: red;">
                  <?php echo $mascota->OpenQty; ?> u. pendientes mayor a 48horas
                </h5>


              <?php  } else { ?>

                <h5 class="card-title" style=" color: green;">
                  <?php echo $xpuntaje1; ?> unidades recientes
                </h5>


              <?php } ?>

            <?php } ?>
            <small class="text-muted">Actualizado desde SAP B1</small>
            </p>

              </div>
            </div>
          </div>
        <?php } else {   ?>
          <div class="card">
            <div class="box">
              <div class="content">
                <h2>📦</h2>
                <div class="col-md-6" style="text-align: center; display: flex; justify-content: center;">
                  <img src="images/dash_icon.jpg" class="img-fluid rounded-start" alt="...">
                </div>
                <h3>

                  Transitoria <?php echo $xtransit; ?></h3>
                <p>
                <h5 class="card-title">
                  <?php echo $xpuntaje1; ?> unidades pendientes
                </h5>
                <p class="card-text">Transitoria limpia. Buen trabajo. 👍</p>
                <small class="text-muted">Actualizado desde SAP B1</small>
                </p>

              </div>
            </div>
          </div>
        <?php
        }

        ?>
        <?php if($userName == 'RL-PSC') { ?>
          <div class="card">
            <div class="box">
              <div class="content">
                <h2>📝</h2>
                <h3>Artículo</h3>
                <p>Revisa tus productos.</p>
                <a href="articulo.php">Iniciar</a>
              </div>
            </div>
          </div>
          <?php } ?>
      </div>

    <?php
  }  ?>

    <!---------------------------------------------->
    <!--------------Fin Content -------------------->
    <!---------------------------------------------->
    </div>
    <?php include_once "footer.php"; ?>