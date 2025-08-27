<?php
include_once "php/bd_desarrollo.php";
$itemcode = $_POST['itemcode'] ?? '';
$whscode = $_POST['whscode'] ?? '';
$result = ['success' => false];

if ($itemcode && $whscode) {
    $sql = "SELECT TOP (1)
        [CodeBars], [ItemCode], [ItemName], [ClasificacionABC], arbol_nivel1 as [unidad], arbol_nivel2 as [categoria], arbol_nivel3 as [linea], [marca],
        [ultima_fecha_ingreso], [dias_ultima_fecha_ingreso], [VentaUltima], [VentaUltima]/30 as PromVenta30dias,
        [venta_90dias], [venta_90dias]/90 as PromVenta90dias, [OnHand],
        ISNULL(([total_Bodega] / NULLIF([VentaUltima], 0)) * 30, 0) AS diasInvActual,
        [total_Bodega], [total_Transitoria_Bodega], [MinStock], [MaxStock], [U_LEAD], [VentaPromedio], [CantidadTotalNoventaDias], [PromedioNoventaDias], CantidadTotalTreintaDias
        FROM [MODULOS_SC].[reposicion].[ProcesadosCache]
        WHERE [ItemCode]=? AND [WhsCode]=?";
    $stmt = $dbdev->prepare($sql);
    $stmt->execute([$itemcode, $whscode]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        $result['success'] = true;
        $result['data'] = $data;
    }
}
header('Content-Type: application/json');
echo json_encode($result);
