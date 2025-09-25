<?php
header('Content-Type: application/json');

// Incluir conexión a base de datos
include_once "php/bd_desarrollo.php"; // Para acceder a MODULOS_SC

// Verificar que se recibieron los parámetros
if (!isset($_POST['almacen']) || !isset($_POST['articulo'])) {
    echo json_encode(['success' => false, 'error' => 'Parámetros faltantes']);
    exit;
}

$almacen = trim($_POST['almacen']);
$articulo = trim($_POST['articulo']);

try {
    // Consulta principal para buscar el artículo por ItemCode o CodeBars
    $sql = "SELECT TOP 1
                ItemCode, 
                ItemName,
                CodeBars,
                VentaPromedio,
                VentaUltima,
                total_Transitoria_Tienda,
                total_Bodega,
                total_Tienda,
                arbol_nivel1,
                arbol_nivel2,
                arbol_nivel3,
                marca_producto,
                categoria,
                marca,
                dias_ultima_fecha_ingreso,
                venta_90dias,
                CantidadTotalTreintaDias,
                CantidadTotalNoventaDias,
                DiasUltimaFechaIngreso,
                WhsCode,
                WhsName
            FROM [MODULOS_SC].[reposicion].[ProcesadosCache]
            WHERE (ItemCode = ? OR CodeBars = ?) 
            AND WhsCode = ?";
    
    $stmt = $dbdev->prepare($sql);
    $stmt->execute([$articulo, $articulo, $almacen]);
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        // Consulta adicional para obtener información de precios
        $sqlPrecios = "SELECT TOP 1
                        [ItemCode],
                        [WhsCode],
                        [CodeBars],
                        [AvgPrice],
                        [Price]
                    FROM [TEMPORALES].[transicion].[tbl_modelo_abastecimiento_tiendas_desarrollo]
                    WHERE (ItemCode = ? OR CodeBars = ?) 
                    AND WhsCode = ?";
        
        $stmtPrecios = $dbdev->prepare($sqlPrecios);
        $stmtPrecios->execute([$articulo, $articulo, $almacen]);
        
        $precios = $stmtPrecios->fetch(PDO::FETCH_ASSOC);
        
        // Combinar los resultados
        if ($precios) {
            $resultado['AvgPrice'] = $precios['AvgPrice'];
            $resultado['Price'] = $precios['Price'];
        } else {
            // Si no se encuentran precios, agregar valores por defecto
            $resultado['AvgPrice'] = null;
            $resultado['Price'] = null;
        }
        
        echo json_encode([
            'success' => true,
            'articulo' => $resultado
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Artículo no encontrado'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la consulta: ' . $e->getMessage()
    ]);
}
?>