<?php

$WhsCode = $_POST["WhsCode"];
$Quantity = $_POST["Quantity"];

include_once "bd_StoreControl.php";

$s1 = $db->prepare("
    declare @WhsCode int
    set @WhsCode = " . $WhsCode . "  
    declare @tomaCode nvarchar(20)
    set @tomaCode = (
        CONCAT(
            REPLACE((SELECT cod_almacen FROM Almacen WHERE id = @WhsCode), '-', ''),
            format(cast(getdate() as datetime), 'yyMMdd'),
            'TF'
        )
    )

    IF NOT EXISTS (SELECT * FROM StockCab WHERE tomaCode = @tomaCode) 
    BEGIN
        INSERT INTO [dbo].[StockCab]
            ([FK_ID_almacen], [tomaCode], [tipo])
        VALUES
            (@WhsCode, @tomaCode, 'TF');
        
        declare @lastId int
        set @lastId = (SELECT SCOPE_IDENTITY());
        
        DECLARE @DWW INT;
        SET @DWW = (SELECT DATEPART(WEEKDAY, GETDATE()));
      
        IF @DWW = 2 
        BEGIN
            INSERT INTO [dbo].[StockDet]
                ([FK_id_StockCab], [FK_ID_articulo], [stock])
            SELECT TOP " . $Quantity . " @lastId, ItemCode, Stock
            FROM (
                SELECT TOP 65 s.*, a.ID_articulo,
                    CONCAT(
                        SUBSTRING(a.nombreGrupo, 3, 1),
                        SUBSTRING(a.nombreGrupo, 5, 1),
                        RIGHT(a.nombreGrupo, 1)
                    ) AS a1
                FROM vw_stockDia_vs_veces s
                JOIN Articulo a ON s.ItemCode = a.id
                WHERE a.fechaCreacion < DATEADD(day, -30, GETDATE()) -- no se haya creado en los últimos 30 días
                  AND WhsCode = @WhsCode
                ORDER BY VECES, NEWID()
            ) x
            ORDER BY a1, ItemCode;
        END 
        ELSE IF @DWW = 3 
        BEGIN
            INSERT INTO [dbo].[StockDet]
                ([FK_id_StockCab], [FK_ID_articulo], [stock])
            SELECT TOP " . $Quantity . " @lastId, ItemCode, Stock
            FROM (
                SELECT TOP 65 s.*, a.ID_articulo,
                    CONCAT(
                        SUBSTRING(a.nombreGrupo, 5, 1),
                        RIGHT(a.nombreGrupo, 1),
                        SUBSTRING(a.nombreGrupo, 3, 1)
                    ) AS a2
                FROM vw_stockDia_vs_veces s
                JOIN Articulo a ON s.ItemCode = a.id
                WHERE a.fechaCreacion < DATEADD(day, -30, GETDATE()) -- no se haya creado en los últimos 30 días
                  AND WhsCode = @WhsCode
                ORDER BY VECES, NEWID()
            ) x
            ORDER BY a2, ItemCode;
        END 
        ELSE IF @DWW = 4 
        BEGIN
            INSERT INTO [dbo].[StockDet]
                ([FK_id_StockCab], [FK_ID_articulo], [stock])
            SELECT TOP " . $Quantity . " @lastId, ItemCode, Stock
            FROM (
                SELECT TOP 65 s.*, a.ID_articulo,
                    CONCAT(
                        RIGHT(a.nombreGrupo, 1),
                        SUBSTRING(a.nombreGrupo, 3, 1),
                        SUBSTRING(a.nombreGrupo, 5, 1)
                    ) AS a3
                FROM vw_stockDia_vs_veces s
                JOIN Articulo a ON s.ItemCode = a.id
                WHERE a.fechaCreacion < DATEADD(day, -30, GETDATE()) -- no se haya creado en los últimos 30 días
                  AND WhsCode = @WhsCode
                ORDER BY VECES, NEWID()
            ) x
            ORDER BY a3, ItemCode;
        END 
        ELSE IF @DWW = 5 
        BEGIN
            INSERT INTO [dbo].[StockDet]
                ([FK_id_StockCab], [FK_ID_articulo], [stock])
            SELECT TOP " . $Quantity . " @lastId, ItemCode, Stock
            FROM (
                SELECT TOP 65 s.*, a.ID_articulo,
                    CONCAT(
                        SUBSTRING(a.nombreGrupo, 2, 1),
                        SUBSTRING(a.nombreGrupo, 1, 1),
                        SUBSTRING(a.nombreGrupo, 5, 1)
                    ) AS a4
                FROM vw_stockDia_vs_veces s
                JOIN Articulo a ON s.ItemCode = a.id
                WHERE a.fechaCreacion < DATEADD(day, -30, GETDATE()) -- no se haya creado en los últimos 30 días
                  AND WhsCode = @WhsCode
                ORDER BY VECES, NEWID()
            ) x
            ORDER BY a4, ItemCode;
        END
        ELSE 
        BEGIN
            INSERT INTO [dbo].[StockDet]
                ([FK_id_StockCab], [FK_ID_articulo], [stock])
            SELECT TOP " . $Quantity . " @lastId, ItemCode, Stock
            FROM (
                SELECT TOP 65 s.*, a.ID_articulo, a.nombreGrupo AS ng
                FROM vw_stockDia_vs_veces s
                JOIN Articulo a ON s.ItemCode = a.id
                WHERE a.fechaCreacion < DATEADD(day, -30, GETDATE()) -- no se haya creado en los últimos 30 días
                  AND WhsCode = @WhsCode
                ORDER BY VECES, NEWID()
            ) x
            ORDER BY x.ng, ItemCode;
        END
    END  
    SELECT @tomaCode AS toma;
");
$s1->execute();

$result = $s1->fetch(PDO::FETCH_OBJ);
$res = $result->toma;

echo $res;

?>