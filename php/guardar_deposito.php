<?php
include_once "bd_StoreControl.php";
session_start();

$idUsuario = $_SESSION['idU'] ?? null;
if (!$idUsuario) {
    echo "Sesión expirada o usuario no identificado.";
    exit();
}

// Recibir datos del formulario
$DepositDate       = $_POST["DepositDate"];
$DepositAccount    = $_POST["DepositAccount"];
$DepositCurrency   = $_POST["DepositCurrency"];
$DepositType       = $_POST["DepositType"];
$AllocationAccount = $_POST["AllocationAccount"];
$TotalLC           = $_POST["TotalLC"];
$U_Fecha           = $_POST["U_Fecha"];
$U_WhsCode         = $_POST["U_WhsCode"];
$U_Ref_Bancar      = $_POST["U_Ref_Bancar"];
$Responsable       = $_POST["Responsable"];
$accion            = $_POST["accion"];

// Guardar en tabla local
if ($accion === "guardar_local") {
    $stmt = $db->prepare("
        INSERT INTO DepositosTiendas (
            DepositDate, DepositAccount, DepositCurrency, DepositType,
            AllocationAccount, TotalLC, U_Fecha, U_WhsCode,
            U_Ref_Bancar, Responsable, fk_id_user, creadoSAP
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
    ");

    $ok = $stmt->execute([
        $DepositDate, $DepositAccount, $DepositCurrency, $DepositType,
        $AllocationAccount, $TotalLC, $U_Fecha, $U_WhsCode,
        $U_Ref_Bancar, $Responsable, $idUsuario
    ]);

    if ($ok) {
        echo "<script>alert('Depósito guardado localmente.'); window.location.href='form_deposito.php';</script>";
    } else {
        echo "<h4>Error al guardar localmente.</h4>";
    }
    exit();
}

// Si la acción es enviar a SAP
if ($accion === "enviar_sap") {
    $data = [
        "DepositDate"       => $DepositDate,
        "DepositAccount"    => $DepositAccount,
        "DepositCurrency"   => $DepositCurrency,
        "DepositType"       => $DepositType,
        "AllocationAccount" => $AllocationAccount,
        "TotalLC"           => floatval($TotalLC),
        "U_Fecha"           => $U_Fecha,
        "U_WhsCode"         => $U_WhsCode,
        "U_Ref_Bancar"      => $U_Ref_Bancar
    ];

    // Enviar al Service Layer
    $url = "https://<tu-servidor-sap>/b1s/v1/Deposits"; // Reemplaza con tu URL real
    $usuario = "tu_usuario";
    $contrasena = "tu_contraseña";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_USERPWD, "$usuario:$contrasena");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201 || $httpCode == 200) {
        echo "<script>alert('Depósito enviado a SAP correctamente.'); window.location.href='form_deposito.php';</script>";
    } else {
        echo "<h4>Error al enviar a SAP</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>
