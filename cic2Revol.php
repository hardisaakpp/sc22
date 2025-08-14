<?php
include_once "header.php";
include_once "php/f_consumo.php";

// Configuración para que el output se envíe en vivo
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
while (ob_get_level()) { ob_end_flush(); }
ob_implicit_flush(true);

$pFecha = isset($_POST['pFecha']) ? $_POST['pFecha'] : date('Y-m-d');

if (!isset($_POST['iniciar'])) {
?>
    <form method="POST">
        <label>Fecha:</label>
        <input type="date" name="pFecha" value="<?php echo $pFecha; ?>" required>
        <button type="submit" name="iniciar">Iniciar proceso</button>
    </form>
<?php
    include_once "footer.php";
    exit;
}

// Obtener tiendas
$stmtTiendas = $db->query("
    SELECT DISTINCT fk_ID_almacen_cierre
    FROM users
    WHERE realizaconteo = 1 AND fk_ID_almacen_cierre > 0
");
$tiendas = $stmtTiendas->fetchAll(PDO::FETCH_COLUMN);

echo "<h3>Procesando arqueo para fecha {$pFecha}</h3>";
echo "<pre style='font-size:16px'>";
flush();

foreach ($tiendas as $tiendaCica) {
    echo "Iniciando tienda {$tiendaCica}...\n";
    flush();

    // Obtener datos tienda
    $sentencia2 = $db->query("SELECT * FROM almacen WHERE id = ".$tiendaCica);
    $TEMP1 = $sentencia2->fetchObject();
    $h_cod_neg = $TEMP1->hit_cod_neg;
    $h_cod_local = $TEMP1->hit_cod_local;

    // Token
    $token = get_token($tiendaCica, $db, $userName);

    // Encerar formas de pago
    for ($i=1; $i < 8; $i++) { 
        $db->prepare("EXEC sp_cicH_clearCaja ?, ?, ?")->execute([$tiendaCica, $pFecha, $i]);
    }

    // Arqueo
    for ($i=1; $i < 8; $i++) { 
        $data2 = consumo_arqueo($token, $h_cod_neg, $h_cod_local, $i, $pFecha);
        $data3 = consumo_arqueo_abonos($token, $h_cod_neg, $h_cod_local, $i, $pFecha);

        if ($data2) {
            foreach ($data2 as $key => $value) {
                $db->prepare("EXEC sp_cic2_insertLine ?, ?, ?, ?, ?, ?")
                   ->execute([$tiendaCica, $pFecha, $i, $key, $value["total_amount"], $value["count"]]);
            }
        }

        if ($data3 && $data3["total_amount"] > 0) {
            $db->prepare("EXEC sp_cic2_insertLine ?, ?, ?, ?, ?, ?")
               ->execute([$tiendaCica, $pFecha, $i, 'Crédito directo - Pago de abono', $data3["total_amount"], $data3["count"]]);
        }

        $db->prepare("EXEC sp_cicH_updateLine_pinpad ?, ?, ?")
           ->execute([$tiendaCica, $pFecha, $i]);
    }

    // Procesos finales si la fecha es ayer o hoy
    if ($pFecha >= date('Y-m-d', strtotime('yesterday'))) {
        $db->query("
            EXEC sp_cic_sincSAPSingle '{$tiendaCica}', '{$pFecha}';
            EXEC sp_cic_createCajas '{$tiendaCica}', '{$pFecha}';
            EXEC sp_cicUs_create '{$tiendaCica}', '{$pFecha}';
        ");
    }

    echo "✔ Tienda {$tiendaCica} procesada\n";
    flush();
}

echo "\n✅ Proceso finalizado.\n</pre>";
include_once "footer.php";
?>
