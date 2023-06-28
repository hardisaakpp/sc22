<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin>2){
        echo ('ACCESO DENEGADO');
        exit();
        }


    $emp='ALL';

if ($whsTurem>0) {
    
    $sentencia6 = $db->query("select fk_emp from  Almacen where id=".$whsTurem." " );
    $regC1 = $sentencia6->fetchObject();
    $emp=$regC1->fk_emp;

    if ($emp=='MT') { //MABELTRADING
        $s1 = $dbB->query(" select d.intiddetalleturno,
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
    }else {  //COSMECMAC
        $s1 = $dbB->query(" select d.intiddetalleturno,
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
            t.intIdTurno in (5,6,11,12,14,17,15,16) " );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);    
    }
}else {
    $s1 = $dbB->query(" select d.intiddetalleturno,
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
    t.intIdTurno in (5,6,11,12,14,17,15,16,3,4,11,12,14,17,15,16) " );
    $whs = $s1->fetchAll(PDO::FETCH_OBJ);  
}

?>
<div class="content">

<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">HORARIOS</strong>
  
        </div>
    
        <div class="card-body">
                          
<form id="monthformX" method="post" action="" name="headbuscar">
   <input type=number id="idcab" class="form-control" name="idcab" value=<?php echo $emp; ?> hidden >
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Descargar
        </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
            
                    <input type="submit"class="dropdown-item" value="Horarios.xlsx" onclick=this.form.action="turempLxls.php?ti=1&idcab=<?php echo $emp ?>">
                 <!--   <div class="dropdown-divider"></div>
                    <input type="submit"class="dropdown-item" value="Mabel Trading" onclick=this.form.action="turempLxls.php?ti=2&idcab=<?php echo $emp ?>">
                    <input type="submit"class="dropdown-item" value="Cosmecmac" onclick=this.form.action="turempLxls.php?ti=3&idcab=<?php echo $emp ?>">
                -->  
                    
            </div>
    </div>
</form>
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