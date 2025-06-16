<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empresa = $_POST["empresa"];
    $pFecha = $_POST["fecha"];

set_time_limit(300); // 300 segundos = 5 minutos

    include_once "bd_StoreControl.php";
    include_once "f_consumo.php";

    // Obtener almacenes
    $data = json_encode(["empresa" => $empresa]);
    $ch = curl_init("http://localhost/sc22/php/get_almacenes.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);

    if ($response === false) {
        echo "❌ Error en cURL: " . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch);
    $almacenes = json_decode($response, true);

    if ($almacenes === null) {
        echo "❌ Error al decodificar JSON: " . json_last_error_msg();
        echo "<br>Respuesta cruda: <pre>$response</pre>";
        exit;
    }

    echo "<h3>✅ Almacenes obtenidos:</h3><ul>";
    foreach ($almacenes as $alm) {
        echo "<li>Almacén ID: $alm</li>";
    }
    echo "</ul>";

    $token = "TOKEN_AQUI";
    $h_cod_neg = "COD_NEG";

    foreach ($almacenes as $tiendaCica) {
        echo "<h4>🔁 Procesando tienda: $tiendaCica</h4>";

        for ($i = 1; $i < 8; $i++) {
            echo "🧹 Ejecutando sp_cicH_clearCaja para caja $i<br>";
            $stmt = $db->prepare("exec sp_cicH_clearCaja ?, ?, ?;");
            $stmt->execute([$tiendaCica, $pFecha, $i]);
        }

        for ($i = 1; $i < 8; $i++) {
            $h_cod_local = $tiendaCica;

            $data2 = consumo_arqueo($token, $h_cod_neg, $h_cod_local, $i, $pFecha);
            $data3 = consumo_arqueo_abonos($token, $h_cod_neg, $h_cod_local, $i, $pFecha);

            if ($data2 !== null) {
                foreach ($data2 as $key => $value) {
                    echo "💳 Insertando arqueo: $key - Monto: {$value["total_amount"]} - Cantidad: {$value["count"]}<br>";
                    $stmt = $db->prepare("exec sp_cic2_insertLine ?, ?, ?, ?, ?, ?;");
                    $stmt->execute([$tiendaCica, $pFecha, $i, $key, $value["total_amount"], $value["count"]]);
                }
            }

            if ($data3 !== null && $data3["total_amount"] > 0) {
                echo "💰 Insertando abono: Monto: {$data3["total_amount"]} - Cantidad: {$data3["count"]}<br>";
                $stmt = $db->prepare("exec sp_cic2_insertLine ?, ?, ?, ?, ?, ?;");
                $stmt->execute([$tiendaCica, $pFecha, $i, 'Crédito directo - Pago de abono', $data3["total_amount"], $data3["count"]]);
            }

            echo "🔄 Actualizando pinpad para caja $i<br>";
            $stmt = $db->prepare("exec sp_cicH_updateLine_pinpad ?, ?, ?;");
            $stmt->execute([$tiendaCica, $pFecha, $i]);
        }
    }

    echo "<br><strong>✅ Procesamiento completado.</strong>";
}
?>
