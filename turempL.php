<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin==0){
        echo ('ACCESO DENEGADO');
        exit();
        }

        $s1 = $dbB->query(" select d.intiddetalleturno
        ,
          convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Horario
  ,t.strNombre, intMinutosDescanso as minDescanso,
      case
      when dtmHoraDesde>dtmHoraHasta then
          DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso 
       else 
        DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso 
        end
       AS minTrabajo

          from tblTurnos t
          inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
        where 
        t.intIdTurno in (3,4,11,12,14,17,15,16) " );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
       
?>
<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->


<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">HORARIOS MABELTRADING</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>CODIGO</th>
                        <th>HORARIO</th>
                        <th>DESCRIPCION</th>
                        <th>MIN. DESCANSO</th>
                        <th>MIN. TRABAJO</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($whs as $wh){ ?>


                    <tr>
                        <td><?php echo $wh->intiddetalleturno ?></td>
                        <td><?php echo $wh->Horario ?></td>
                        <td><?php echo $wh->strNombre ?></td>
                        <td><?php echo $wh->minDescanso ?></td>
                        <td><?php echo $wh->minTrabajo ?></td>

                    </tr>
                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>



  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php    include_once "footer.php"; ?>