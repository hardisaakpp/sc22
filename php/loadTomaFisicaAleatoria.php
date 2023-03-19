<?php

$WhsCode = $_POST["WhsCode"];
$Quantity = $_POST["Quantity"];

include_once "bd_StoreControl.php";


    $s1 = $db->prepare("
    declare @WhsCode int
    set @WhsCode=".$WhsCode."  
    declare @tomaCode nvarchar(20)
    set @tomaCode=(CONCAT( REPLACE((SELECT cod_almacen from Almacen where id=@WhsCode),'-','')
    ,format(cast(getdate() as datetime),'yyMMdd'),'TF'))

    IF NOT EXISTS (select * from StockCab WHERE tomaCode = @tomaCode) 
    BEGIN
        INSERT INTO [dbo].[StockCab]
                  ([FK_ID_almacen]
                  ,[tomaCode]
                  ,[tipo])
            VALUES
                  (@WhsCode, @tomaCode,'TF');
        
        declare @lastId int
          set @lastId = (SELECT SCOPE_IDENTITY());
        
          DECLARE @DWW INT;
          SET @DWW =(SELECT DATEPART(WEEKDAY, GETDATE()));
      
          IF	@DWW=2 
            BEGIN
                INSERT INTO [dbo].[StockDet]
                        ([FK_id_StockCab]
                        ,[FK_ID_articulo]
                        ,[stock])
              select top ".$Quantity." @lastId , ItemCode, Stock 
              from
              (select top 150 s.*, a.ID_articulo 
              ,concat(SUBSTRING(a.nombreGrupo, 3, 1),SUBSTRING(a.nombreGrupo, 5, 1), RIGHT(a.nombreGrupo, 1)) as a1 
                from vw_stockDia_vs_veces  s
                  join Articulo a on s.ItemCode=a.id
                where a.fechaCreacion< DATEADD(day,-30,GETDATE()) --no se haya creado en los ultimos 30 dias
                  and WhsCode=@WhsCode 
                order by VECES, newid()  
              ) x ORDER BY a1, ItemCode;
            END 
          else 		IF	@DWW=3 
            BEGIN
                INSERT INTO [dbo].[StockDet]
                        ([FK_id_StockCab]
                        ,[FK_ID_articulo]
                        ,[stock])
              select top ".$Quantity." @lastId , ItemCode, Stock 
              from
              (select top 150 s.*, a.ID_articulo  
              ,concat(SUBSTRING(a.nombreGrupo, 5, 1), RIGHT(a.nombreGrupo, 1),SUBSTRING(a.nombreGrupo, 3, 1)) as a2
                from vw_stockDia_vs_veces  s
                  join Articulo a on s.ItemCode=a.id
                where a.fechaCreacion< DATEADD(day,-30,GETDATE()) --no se haya creado en los ultimos 30 dias
                  and WhsCode=@WhsCode 
                order by VECES, newid()  
              ) x ORDER BY a2, ItemCode;
            END 
          else 		IF	@DWW=4 
            BEGIN
                INSERT INTO [dbo].[StockDet]
                        ([FK_id_StockCab]
                        ,[FK_ID_articulo]
                        ,[stock])
              select top ".$Quantity." @lastId , ItemCode, Stock 
              from
              (select top 150 s.*, a.ID_articulo 
              ,concat(RIGHT(a.nombreGrupo, 1),SUBSTRING(a.nombreGrupo, 3, 1),SUBSTRING(a.nombreGrupo, 5, 1)) as a3
                from vw_stockDia_vs_veces  s
                  join Articulo a on s.ItemCode=a.id
                where a.fechaCreacion< DATEADD(day,-30,GETDATE()) --no se haya creado en los ultimos 30 dias
                  and WhsCode=@WhsCode 
                order by VECES, newid()  
              ) x ORDER BY a3, ItemCode;
            END 
            else 		IF	@DWW=5 
            BEGIN
                INSERT INTO [dbo].[StockDet]
                        ([FK_id_StockCab]
                        ,[FK_ID_articulo]
                        ,[stock])
              select top ".$Quantity." @lastId , ItemCode, Stock 
              from
              (select top 150 s.*, a.ID_articulo 
              ,concat(SUBSTRING(a.nombreGrupo, 2, 1),SUBSTRING(a.nombreGrupo, 1, 1),SUBSTRING(a.nombreGrupo, 5, 1)) as a4
                --	ItemCode, Stock  ---!!! EL NUMERO DE ITEMS
                from vw_stockDia_vs_veces  s
                  join Articulo a on s.ItemCode=a.id
                where a.fechaCreacion< DATEADD(day,-30,GETDATE()) --no se haya creado en los ultimos 30 dias
                  and WhsCode=@WhsCode 
                order by VECES, newid()  
              ) x ORDER BY a4, ItemCode;
            END
            else 		 
            BEGIN
                INSERT INTO [dbo].[StockDet]
                        ([FK_id_StockCab]
                        ,[FK_ID_articulo]
                        ,[stock])
              select top ".$Quantity." @lastId , ItemCode, Stock 
              from
              (select top 150 s.*, a.ID_articulo , a.nombreGrupo AS ng
                from vw_stockDia_vs_veces  s
                  join Articulo a on s.ItemCode=a.id
                where a.fechaCreacion< DATEADD(day,-30,GETDATE()) --no se haya creado en los ultimos 30 dias
                  and WhsCode=@WhsCode 
                order by VECES, newid()  
              ) x ORDER BY x.ng, ItemCode;
            END
    END  
    SELECT @tomaCode as toma;
    ");
    $s1->execute();

    $result = $s1->fetch(PDO::FETCH_OBJ);
      $res =$result->toma;

    echo $res;


?>