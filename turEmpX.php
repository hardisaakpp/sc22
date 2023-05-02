<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Pagina para control de inventarios">
    <meta name="author" content="Alex.Toasa@outlook.com">
    <title>Inventario|22</title>

    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <!-- Cargar el CSS de Boostrap-->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!--<script type="text/javascript" src="js/calendario.js"></script>-->
    <script src="js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="js/jquery-3.2.1.js" type="text/javascript"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

  </head>

<body>
  <?php
    // Validating Session
    session_start();
    if(strlen($_SESSION['username'])==0)
    {
      header('location:index.php');
    }
    //include_once "php/base_de_datos.php";
    date_default_timezone_set('America/Bogota');
    
    if (!isset($_GET["id"]))
    {
      exit();
    }else
    {
      $idTurem=$_GET['id'];
    }
      
      require 'php/bd_Biometricos.php';
      include_once "php/bd_StoreControl.php";

      //credenciales
      $username = $_SESSION['username'];
      $idU = $_SESSION['idU'];

      $sentencia3 = $db->query("select top 1 * from turem where id=".$idTurem." " );
      $regC = $sentencia3->fetchObject();
              $mes=$regC->mes;
              $year=$regC->anio;
              $nombre=$regC->nombre;
              $apellido=$regC->apellido;
              $cedula=$regC->cedula;
              $sueldo=$regC->sueldo_actual;


        $sentencia6 = $db->query("select a.fk_emp from users u join Almacen a on u.fk_ID_almacen_turemp=a.id where u.id=".$idU." " );
        $regC1 = $sentencia6->fetchObject();
        $emp=$regC1->fk_emp;
    //dias festivos
              $sentencia6 = $dbB->query(" SELECT CONCAT(YEAR([dtmFestivo]),RIGHT('00' + Ltrim(Rtrim(MONTH([dtmFestivo]))),2) ,RIGHT('00' + Ltrim(Rtrim(DAY([dtmFestivo]))),2))  AS 'DATAKEY'
              ,[strObservaciones]
            FROM [dbTimeSoftWebAutomatic_MABEL].[dbo].[tblFestivos]
            where YEAR([dtmFestivo])='".$year."' and MONTH([dtmFestivo])='".$mes."' " );
              $fers = $sentencia6->fetchAll(PDO::FETCH_OBJ);

//turnos para select dependiendo es MT o CE
if ($emp=='MT') {


      $sentencia4 = $dbB->query("  select d.intiddetalleturno  ,t.strNombre, -- intMinutosDescanso,
      case
      when dtmHoraDesde>dtmHoraHasta then
          DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso 
       else 
        DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso 
        end
       AS minTrab
        ,
          convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Horario
          from tblTurnos t
          inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
        where 
        t.intIdTurno in (3,4,11,12,14,17,15,16)  " );
      $turnos = $sentencia4->fetchAll(PDO::FETCH_OBJ);
    }else {
      $sentencia4 = $dbB->query("  select d.intiddetalleturno  ,t.strNombre, -- intMinutosDescanso,
      case
      when dtmHoraDesde>dtmHoraHasta then
          DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso 
       else 
        DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso 
        end
       AS minTrab
        ,
          convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Horario
          from tblTurnos t
          inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
        where 
        t.intIdTurno in (5,6,11,12,14,17,15,16)  " );
      $turnos = $sentencia4->fetchAll(PDO::FETCH_OBJ);
    }


      $sentencia5 = $db->query("select * from turem_day where fk_id_turem='".$idTurem."' " );
      $turemDays = $sentencia5->fetchAll(PDO::FETCH_OBJ);
    //  echo($idTurem);	
    //  echo(count($turemDays));
  ?>

  <script>
    //Arrays de datos:
    meses=["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    lasemana=["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"]
    diassemana=["LUNES","MARTES","MIÉRCOLES","JUEVES","VIERNES","SÁBADO","DOMINGO"];
    var xDiasMes = [];
    var xDiaZ = [];
    var xTurnoZ=[];
        <?php foreach($turnos as $turno){ ?>
          xTurnoZ.push([<?php echo $turno->intiddetalleturno ?>,<?php echo $turno->minTrab ?>]);
        <?php } ?>
    $(document).ready(function() {
      $('input[rel="txtTooltip"]').tooltip();
      $(".inputOculto").hide();
      document.getElementById('mes').value = <?php echo $mes; ?>-1;
      document.getElementById('year').value = <?php echo $year; ?>;
      mifecha();
      $("select").select2();
    });
    //Tras cargarse la página ...
    window.onload = function() {
      //fecha actual
        //Recoger dato del año en el formulario
        mianno=document.buscar.buscaanno.value; 
        listameses=document.buscar.buscames;
        opciones=listameses.options;
        num=listameses.selectedIndex
        mimes=opciones[num].value;

      hoy=new Date(); //objeto fecha actual
      diasemhoy=hoy.getDay(); //dia semana actual

      meshoy=mimes; //mes actual
      annohoy=mianno; //año actual
      // Elementos del DOM en primera fila
      f0=document.getElementById("fila0");
      // Definir elementos iniciales:
      mescal = meshoy; //mes principal
      annocal = annohoy //año principal
      //iniciar calendario:
      primeralinea();
      escribirdias();


    }
    //primera línea de tabla: días de la semana.
    function primeralinea() {
            for (i=0;i<7;i++) {
                celda0=f0.getElementsByTagName("th")[i];
                celda0.innerHTML=diassemana[i]
                }
            }
    //rellenar celdas con los días
    function secondsToString(seconds) {
      var hour = Math.floor(seconds / 3600);
      hour = (hour < 10)? '0' + hour : hour;
      var minute = Math.floor((seconds / 60) % 60);
      minute = (minute < 10)? '0' + minute : minute;
      var second = seconds % 60;
      second = (second < 10)? '0' + second : second;
      return hour + ':' + minute + ':' + second;
    }
    function escribirdias() {
            //Buscar dia de la semana del dia 1 del mes:
            primeromes=new Date(annocal,mescal,"1") //buscar primer día del mes
            prsem=primeromes.getDay() //buscar día de la semana del día 1
            prsem--; //adaptar al calendario español (empezar por lunes)
            if (prsem==-1) {prsem=6;}
            //buscar fecha para primera celda:
            diaprmes=primeromes.getDate() 
            prcelda=diaprmes-prsem; //restar días que sobran de la semana
            empezar=primeromes.setDate(prcelda) //empezar= tiempo UNIX 1ª celda
            diames=new Date() //convertir en fecha
            diames.setTime(empezar); //diames=fecha primera celda.
            //Recorrer las celdas para escribir el día:
            for (i=1;i<7;i++) { //localizar fila
                fila=document.getElementById("fila"+i);
                for (j=0;j<7;j++) {
                    midia=diames.getDate() 
                    mimes=diames.getMonth()
                    mianno=diames.getFullYear()
                    celda=fila.getElementsByTagName("td")[j];
                    celda.innerHTML=midia;
                    //dias restantes del mes en gris*/
                    if (mimes==mescal) { 
                        
                        let DataKey= (mianno + ((mimes+ 1).toString().padStart(2, '0') ) +midia.toString().padStart(2, '0')  );
                        if (mianno==<?php echo $year;?>) {
                          xDiasMes[midia-1]=DataKey;
                          xDiaZ.push([DataKey,i]);
                        }
                      
                        celda.innerHTML=  
                        "<div class='container' style='padding-right=0px; padding-left=0px;'>"+
                        "  <div class='row'>"+
                        "    <div class='col' id=dia"+ DataKey +" >"+
                        "        <h3>"+ midia +"</h3>"+
                        "    </div>"+
                        "    <div class='col'>"+
                        "        <input type='text' class='form-control' id=fer"+ DataKey +"  rel='txtTooltip' title='Horas Trabajadas' data-toggle='tooltip' data-placement='bottom' value=0 hidden>"+
                        "        <input type='text' class='form-control  form-control-sm inputOculto' onchange='refreshTT()' id=htr"+ DataKey +"  rel='txtTooltip' title='Horas Trabajadas' data-toggle='tooltip' data-placement='bottom' value=0>"+
                        "        <input type='text' id='cod"+ DataKey +"' onchange='reply_click(this.value, this.id)' class='form-control  form-control-sm' id='cod' name='cod' rel='txtTooltip' title='Código' data-toggle='tooltip' data-placement='bottom' value=0>    "+
                        "    </div>"+
                        "  </div>"+
                        "  <div class='row'>"+
                                " <select name='turno' style='width:200px;' class='operator' id='tur"+ DataKey +"'  Size='Number_of_options' class='form-select' onchange='handleSelectChange(event,this.id)'    "+         
                        "                 <?php foreach($turnos as $turno){ ?>"+
                        "                     <option value=<?php echo $turno->intiddetalleturno ?>> <?php echo $turno->strNombre . ' '. $turno->Horario ?>   </option>"+
                        "                 <?php } ?>"+
                        "                <option value=9999> NUEVO TURNO </option>"+
                        "             </select>    "+
                        "  </div>"+
                        "  <div class='row'>"+
                        "    <input type='text' class='inputOculto' id=des"+ DataKey +" name='row-1-age' value=''  placeholder='Descripcion' >"+
                        "  </div>"+
                        "</div>";
                        }else{
                        
                            celda.style.color="#a0babc";
                        }
                        
                    //pasar al siguiente día
                    midia=midia+1;
                    diames.setDate(midia);
                  // console.log("save");
                }
            }

            <?php foreach($turemDays as $turemDay){ ?>
              
              
              var x1codTurno = document.getElementById("tur"+<?php echo $turemDay->fk_DateKey ?>);
              var x1codTurnotxt = document.getElementById("cod"+<?php echo $turemDay->fk_DateKey ?>);
              
              x1codTurno.value=<?php echo $turemDay->cod_turno ?>;
              x1codTurnotxt.value=<?php echo $turemDay->cod_turno ?>;
              
            
             pruebaLX(<?php echo $turemDay->cod_turno ?>,"htr"+<?php echo $turemDay->fk_DateKey ?>);
              //SI ES NUEVO TURNO CARGO HORAS Y DESCRIPCION
              if (<?php echo $turemDay->cod_turno ?>==9999) {
                var x1codTurnodes = document.getElementById("des"+<?php echo $turemDay->fk_DateKey ?>);
                x1codTurnodes.value="<?php echo $turemDay->des_turno ?>";  
                var x1codTurnohtr = document.getElementById("htr"+<?php echo $turemDay->fk_DateKey ?>);
                x1codTurnohtr.value="<?php echo $turemDay->horlab_turno ?>"; 
              }



              var x1Desc = document.getElementById('des'+<?php echo $turemDay->fk_DateKey ?>);
              var x1Min = document.getElementById('htr'+<?php echo $turemDay->fk_DateKey ?>);
              if(<?php echo $turemDay->cod_turno ?>!=9999){
                x1Desc.style.display = 'none';
                x1Min.style.display = 'none';
              } else {
                x1Desc.style.display = 'block';
                x1Min.style.display = 'block';
              }

              if (x1codTurno.value==209 || x1codTurno.value==231 ||x1codTurno.value==268 || x1codTurno.value==235 || x1codTurno.value==236 || x1codTurno.value==210) {
                x1codTurnotxt.style.backgroundColor="yellow";
              } else {
                x1codTurnotxt.style.backgroundColor="white";
              }

            <?php } ?>

            <?php foreach($fers as $fer){ ?>
                var x1diames = document.getElementById("dia"+<?php echo $fer->DATAKEY ?>);
                x1diames.style.color="red";
                var x1diaferbit = document.getElementById("fer"+<?php echo $fer->DATAKEY ?>);
                x1diaferbit.value=1;
              <?php } ?>

            refreshTT();

            //lleno columna sueldos
      for (i=1;i<7;i++) {
                celda0=document.getElementById("sal"+i);
                celda0.innerText='<?php echo $sueldo ?>';
                }
      
                cargaObservaciones();
          }






    //volver al mes actual
    function actualizar() {
            mescal=hoy.getMonth(); //cambiar a mes actual
            annocal=hoy.getFullYear(); //cambiar a año actual 
        //    cabecera() //escribir la cabecera
            escribirdias() //escribir la tabla
            }
    //ir al mes buscado
    function mifecha() {
            //Recoger dato del año en el formulario
            mianno=document.buscar.buscaanno.value; 
            //recoger dato del mes en el formulario
            listameses=document.buscar.buscames;
            opciones=listameses.options;
            num=listameses.selectedIndex
            mimes=opciones[num].value;
            //Comprobar si el año está bien escrito
            if (isNaN(mianno) || mianno<1) { 
                //año mal escrito: mensaje de error
                alert("El año no es válido:\n debe ser un número mayor que 0")
                }
            else { //año bien escrito: ver mes en calendario:
                  mife=new Date(); //nueva fecha
                  mife.setMonth(mimes); //añadir mes y año a nueva fecha
                  mife.setFullYear(mianno);
                  mescal=mife.getMonth(); //cambiar a mes y año indicados
                  annocal=mife.getFullYear();
              //    cabecera() //escribir cabecera
                  escribirdias() //escribir tabla
                  }
    }

    function saveDay() {

      for (i = 0; i < xDiasMes.length; i++) {
        const DateKey = xDiasMes[i];   //20230206  el dia datakey
        var selextText = $("#tur" + DateKey).find(':selected').text().trim();  // el texto del select
        const xcodTurno = document.getElementById("tur"+xDiasMes[i]);
        const horLab = document.getElementById("htr"+xDiasMes[i]);
        //si es turno nuevo agrego descripcion 
          if (xcodTurno.value==9999) {
            const descr = document.getElementById("des"+xDiasMes[i]);
            selextText = descr.value.trim();
          }
          var o1 = $("#o1").val();
          var o2 = $("#o2").val();
          var o3 = $("#o3").val();
          var o4 = $("#o4").val();
          var o5 = $("#o5").val();
          var o6 = $("#o6").val();
        //console.log(DateKey+ '-->' +xcodTurno.value + '-->' + selextText + '-->' + horLab.value);
        saveDayAjax(DateKey,xcodTurno.value, selextText, horLab.value, o1, o2, o3, o4, o5, o6);  //DATAKEY , CodTurno, descripcio, horasLab
        //  console.log("save");
      } 

      
        alert("Guardado correctamente");
        //  alert("guardado exitosamente");
    }


    function minaHoras(min) {
      const horas= (min*1)/60;
      return horas;
    }

    function horasaMin(horas) {
      const min= (horas*60)/1;
      return min;
    }

    function pulsar(e) {  //bloquea el salto de linea en textarea
      if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        console.log('prevented');
        return false;
      }
    }

    function refreshTT() {
      
      //total horas
      var minTT=0; //total minutos trabajadas
      for (let i = 1; i < 7; i++) {
        //totales horizontales
            cellTdl(i);
            minTT = minTT + horasaMin(parseFloat(cellHtr(i), 0)) ;
            //console.log(cellHtr(i));
          }
        
      var horTT=minaHoras(minTT); //total horas trabajadas en decimales
      celda2=document.getElementById("htrTT");
      celda2.innerText=secondsToString((minTT)*60) ;


      var turnosLaborables=0;
      //contar dias trabajados lxlxlx
      for (ix = 0; ix < xDiaZ.length; ix++) {
          var c2c = document.getElementById("tur"+xDiaZ[ix][0]);
          if(c2c.value!=209 && c2c.value!=231 && c2c.value!=268 && c2c.value!=235 && c2c.value!=236 && c2c.value!=210){
            turnosLaborables=turnosLaborables+1;
          }
        
      } 

     // console.log('turnos laborables: ' + turnosLaborables);
      turnosLaborables = turnosLaborables*8;
     // console.log('turnos laborables: ' + turnosLaborables);
    //  console.log('horas trabajadas en decimales: ' + horTT);
      var horExtras = horTT - turnosLaborables; // horas extras trabajadas en decimales
    //  console.log('horas extras trabajadas en decimales: ' + horExtras);
      //var horExtras = horTT - 160; // horas extras trabajadas en decimales
      //console.log(horExtras);
      celda31=document.getElementById("htrTT3");
      celda31.innerText=turnosLaborables + ' horas' ;

      celda3=document.getElementById("htrTT2");
      if (horExtras>=0) {
        celda3.innerText=secondsToString((horasaMin(horExtras))*60) ;
      }else{
        celda3.innerText=horExtras + ' horas' ;
      }

      var sueldo= parseFloat(<?php echo $sueldo ?>, 0);
      //calculo pago de horas extras

      celda4=document.getElementById("h50TT2");
      celda4.innerText= '$' + ((((sueldo)/turnosLaborables)*1.5)* horExtras).toFixed(2)  ;

    h50
          //totales DIAS LIBREs
      var ttDias=0;
      var ttH1c=0;
      for (i=1;i<7;i++) {
          celda0=document.getElementById("tdl"+i);
          ttDias = parseFloat(celda0.innerHTML, 0) +ttDias ;
          ch1c=document.getElementById("h1c"+i);
          ttH1c = parseFloat(ch1c.innerHTML, 0) +ttH1c ;
          }
      celda1=document.getElementById("tdlTT");
      celda1.innerText=ttDias;

      celh1c=document.getElementById("h1cTT");
      celh1c.innerText=ttH1c;
      
            //calculo pago de horas extras 100

      cela4=document.getElementById("h1cTT2");
      cela4.innerText=  ((((sueldo)/turnosLaborables)*2)* ttH1c).toFixed(2)  ;

    }

    function cellTdl(i) {
      //encerar celda tt
      var cell = document.getElementById("tdl"+i);
      cell.innerHTML  = 0;
      // totalizar
      for (ix = 0; ix < xDiaZ.length; ix++) {
       //console.log(ix + ' --> ' + xDiaZ[ix][0] + ' --> ' + xDiaZ[ix][1]);
        if (xDiaZ[ix][1]==i) {
          var cc = document.getElementById("tur"+xDiaZ[ix][0]);
          if(cc.value==209 || cc.value==231 || cc.value==235 || cc.value==268 || cc.value==236  || cc.value==210){
            cell.innerHTML = parseFloat(cell.innerHTML, 0) + 1;
          }
          if (cell.innerHTML != 2 ) {
            cell.style.backgroundColor = "#FFE6E1" ;
            cell.style.color = "red" 
          }else{
            cell.style.backgroundColor = "white" ;
            cell.style.color = "black" 
          }
        }
      } 
    }
    function cellHtr(i) {
      ////PENDIENTE LAS HORAS AL 100% CUANDO SEAN FERIADOS
      
      // totalizar
      var tt= 0;
      var ttH100= 0;
      for (ix = 0; ix < xDiaZ.length; ix++) {
       //console.log(ix + ' --> ' + xDiaZ[ix][0] + ' --> ' + xDiaZ[ix][1]);
        if (xDiaZ[ix][1]==i) {
          var cc = document.getElementById("htr"+xDiaZ[ix][0]);
          //if(cc.value==209){
          tt = tt + parseFloat(cc.value, 0);
          



                var x1diaferbit = document.getElementById("fer"+xDiaZ[ix][0]);
                //ttH100= ttH100 + parseInt(x1diaferbit.value,0);
                if (parseInt(x1diaferbit.value,0)==1) {
                  ttH100= ttH100 + parseFloat(cc.value);
                }
               
          /*var xfer = document.getElementById("fer"+xDiaZ[ix][0]);
          if (xfer.value==1) {
            ttH100 = ttH100 + parseFloat(cc.value, 0);
          }
          console.log(xfer.value);*/
         // }
        }
      } 
     // console.log(xfer.value);
      var cell = document.getElementById("htr"+i);
      cell.innerHTML  = secondsToString(tt*60*60);

      //encerar celda tt
      var cll = document.getElementById("h1c"+i);
      cll.innerHTML  = ttH100;
    /*  var cell2 = document.getElementById("h1c"+i);
      cell2.innerHTML  = secondsToString(ttH1100*60*60);*/
      //cell.innerHTML  = tt;
      const result =tt;
      return tt;
    }


    
      function saveDayAjax(dateKey,codTurno, descripcio, horasLab, o1, o2, o3, o4, o5, o6) {  //DATAKEY , CodTurno, descripcio, horasLab
            //var parametros = {"Nombre":nombre,"Mensaje":mensaje};

            var parametros = 
              {
                "idTuremp" : "<?php echo $idTurem ; ?>" ,
                //"id_user" : "1",
                //"barcode" : "7333209222930",
                "dateKey":dateKey,
                "codTurno":codTurno,
                "descripcio":descripcio,
                "horasLab":horasLab,
                "o1":o1,"o2":o2,"o3":o3,"o4":o4,"o5":o5,"o6":o6
                //"dateKey" : ((document.getElementById("searchInput")).value).replaceAll("'", "-").replaceAll("/", "-").trim() , 
              };
            $.ajax({
                data:parametros,
                async: false,
                url:'turEmpSaveDay.php',
                type: 'post',
                //beforeSend: function () {
              //     $("#resultado").html("Procesando, espere por favor");
              // },
                success: function (response) {   
                    $("#resultado").html(response);
                    
                }
            });

          }

          function reply_clickhor(val, id) {
          
   
            refreshTT();
           }

      function reply_click(val, id) {
          
        // cambio de color el TXT
          var x1codTurnotxt = document.getElementById(id);
          if (val==209 ) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else if (val==231) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else if (val==235) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else if (val==268) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else if (val==236) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else if (val==210) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else {
            x1codTurnotxt.style.backgroundColor="white";
          }
        //si es codigo NUEVO TURNO muestro campos ocultos
          var x1Desc = document.getElementById(id.replace('cod','des'));
          var x1Min = document.getElementById(id.replace('cod','htr'));
          if(val!=9999){
            x1Desc.style.display = 'none';
            x1Min.style.display = 'none';
          } else {
            x1Desc.style.display = 'block';
            x1Min.style.display = 'block';
          }

          $("#" + id.replace('cod','tur') + "").val(val); // Select the option with a value of '1'
          $("#" + id.replace('cod','tur') + "").trigger('change'); // Notify any JS components that the value changed
        
          
          refreshTT();
         }

      function cargaObservaciones() {
          //document.getElementById('des20230101').style.display = 'block';
          //document.getElementById('htr20230101').style.display = 'none';
          $('#o1').val('<?php echo $regC->o1; ?>');
          $('#o2').val('<?php echo $regC->o2; ?>');
          $('#o3').val('<?php echo $regC->o3; ?>');
          $('#o4').val('<?php echo $regC->o4; ?>');
          $('#o5').val('<?php echo $regC->o5; ?>');
          $('#o6').val('<?php echo $regC->o6; ?>');


         }

      function pruebaLX(cod, htrtxt) {  //codigo de turnos y id dedonde esta
          var xHtr = document.getElementById(htrtxt);
          xHtr.value = 0;
          var min=0;
        //filteredArray = xTurnoZ.filter(item => item.name.indexOf('Fran') > -1);
        //let horastrab = xTurnoZ.filter(element => element > 10)
          xTurnoZ.forEach(element => {
            
            if (cod==element[0]) {
              xHtr.value = minaHoras(element[1]);
              //xHtr.value = secondsToString(element[1]*60);
              return;
            }
          });

  
       }

      function handleSelectChange(event, id) {

        var selectElement = event.target;
        var ivalue = selectElement.value;
        var x1codTurnotxt = document.getElementById(id.replace('tur','cod'));
        x1codTurnotxt.value=ivalue;

        var x1codTurno = document.getElementById(id);
        if (ivalue==209 || ivalue==231 || ivalue==268 || ivalue==235 || ivalue==236 || ivalue==210) {
            x1codTurnotxt.style.backgroundColor="yellow";
          } else {
            x1codTurnotxt.style.backgroundColor="white";
          }

          pruebaLX(ivalue, id.replace('tur','htr')); //codigo de turnos y id dedonde esta
          
          var x1Desc = document.getElementById(id.replace('tur','des'));
          var x1Min = document.getElementById(id.replace('tur','htr'));
          if(ivalue!=9999){
            x1Desc.style.display = 'none';
            x1Min.style.display = 'none';
          } else {
            x1Desc.style.display = 'block';
            x1Min.style.display = 'block';
          }

        refreshTT();

      }
  </script>

<!----------------------------------------------------------------------------------------------------------------------------------------->
<!-----------------------------------------------------CALENDARIO MES------------------------------------------------------------->
<!----------------------------------------------------------------------------------------------------------------------------------------->
<div id="calendario">
  <h2 id="titulos"> <?php echo $nombre .' ' .$apellido . ' - Mes : '.$mes.'/'.$year; ?></h2>
  <table id="diasc" class="table table-bordered" style="border-color: black">
    <col style="width: 250px;">
    <col style="width: 250px;">
    <col style="width: 250px;">
    <col style="width: 250px;">
    <col style="width: 250px;">
    <col style="width: 250px;">
    <col style="width: 250px;">
    <col style="width: 90px;">
    <col style="width: 100px;">
    <col style="width: 100px;">
    <col style="width: 100px;">
    <col style="width: 100px;">
    <thead>
      <tr id="fila0" class="table-dark"><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                  <th class='totals'>DIAS LIBRES</th><th>H. TRABAJADAS</th><th>SALARIO</th><th>N° HORAS 50%</th><th>N° HORAS 100%</th><th>OBSERVACION</th></tr>
    </thead>          
    <tbody>
      <tr id="fila1"><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td id='tdl1'></td><td id='htr1'></td><td id='sal1'></td><td></td><td id='h1c1'></td>
                    <td><textarea id="o1" name="o1" rows="2" onkeydown="pulsar(event)" placeholder='Observación'></textarea></td></tr>
      <tr id="fila2"><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td id='tdl2'></td><td id='htr2'></td><td id='sal2'></td><td></td><td id='h1c2'></td>
                    <td><textarea id="o2" name="o2" rows="2" onkeydown="pulsar(event)" placeholder='Observación'></textarea></td></tr>
      <tr id="fila3"><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td id='tdl3'></td><td id='htr3'></td><td id='sal3'></td><td></td><td id='h1c3'></td>
                    <td><textarea id="o3" name="o3" rows="2" onkeydown="pulsar(event)" placeholder='Observación'></textarea></td></tr>
      <tr id="fila4"><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td id='tdl4'></td><td id='htr4'></td><td id='sal4'></td><td></td><td id='h1c4'></td>
                    <td><textarea id="o4" name="o4" rows="2" onkeydown="pulsar(event)" placeholder='Observación'></textarea></td></tr>
      <tr id="fila5"><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td id='tdl5'></td><td id='htr5'></td><td id='sal5'></td><td></td><td id='h1c5'></td>
                    <td><textarea id="o5" name="o5" rows="2" onkeydown="pulsar(event)" placeholder='Observación'></textarea></td></tr>
      <tr id="fila6"><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td id='tdl6'></td><td id='htr6'></td><td id='sal6'></td><td></td><td id='h1c6'></td>
                    <td><textarea id="o6" name="o6" rows="2" onkeydown="pulsar(event)" placeholder='Observación'></textarea></td></tr>
    </tbody>
    <tfoot>
    <tr id="filaTT" class="table-dark"><th  colspan="6" rowspan="3"></th><th>TOTALES</th>
                    <th id='tdlTT'></th><th id='htrTT'></th><th id='salTT'></th><th></th><th id='h1cTT'></th><th></th>
    </tr>
    <tr id="filaTT3" class="table-dark"><th colspan="2"> Horas Requeridas</th>
                    <th id='htrTT3'></th><th id='salTT3'></th><th id='h50TT3'></th><th id='h1cTT3'></th><th id='obsTT3'></th>
    <tr id="filaTT2" class="table-dark"><th colspan="2"> Horas Extras trabajadas</th>
                    <th id='htrTT2'></th><th id='salTT2'>Subtot.:</th><th id='h50TT2'></th><th id='h1cTT2'></th><th id='obsTT2'></th>
    </tr>
    
    </tr>
  </tfoot>
  </table>
  <div class="btn-group" role="group" aria-label="...">
    <button type="button" class="btn btn-outline-success" onclick="saveDay()">Guardar</button>
    <button type="button" class="btn btn-outline-danger" onclick="window.close()">Salir</button>
  </div>
</div>
<div id="calendario" >
  <div id="buscafecha">
    <form action="#" name="buscar" style="display: none;">
      <p>Buscar ... MES
        <select name="buscames"  id='mes'>
          <option value="0">Enero</option>
          <option value="1">Febrero</option>
          <option value="2">Marzo</option>
          <option value="3">Abril</option>
          <option value="4">Mayo</option>
          <option value="5">Junio</option>
          <option value="6">Julio</option>
          <option value="7">Agosto</option>
          <option value="8">Septiembre</option>
          <option value="9">Octubre</option>
          <option value="10">Noviembre</option>
          <option value="11">Diciembre</option>
        </select>
      ... AÑO ...
        <input type="text" name="buscaanno" id='year' maxlength="4" size="4"/>
      ... 
        <input type="button" value="BUSCAR" onclick="mifecha()" />
      </p>
    </form>
  </div>
</div>

<div id="resultado" name="resultado" >  </div>
<!----------------------------------------------------------------------------------------------------------------------------------------->
<p>* Se pintan de amarillo los codigos de Dia Libre.</p>
<p>* El dia sera rojo cuando sea feriado.</p>
<p>* Los minutos de descanso se toma en consideracion para el total de horas trabajadas.</p>
<p>* Las horas trabajadas extras es el valor total de las h trabajadas - 160.</p>
<!----------------------------------------------------------------------------------------------------------------------------------------->





    <script src="js/bootstrap.bundle.min.js"></script>

      <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
  </body>
</html>