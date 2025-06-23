<?php
function consumo_arqueo($token, $h_cod_neg, $h_cod_local, $i, $fecha) {
    // Simulación de datos de arqueo
    return [
        "forma_pago_$i" => [
            "total_amount" => rand(1000, 5000),
            "count" => rand(1, 10)
        ]
    ];
}

function consumo_arqueo_abonos($token, $h_cod_neg, $h_cod_local, $i, $fecha) {
    // Simulación de datos de arqueo de abonos
    return [
        "total_amount" => rand(500, 3000),
        "count" => rand(1, 5)
    ];
}
?>
