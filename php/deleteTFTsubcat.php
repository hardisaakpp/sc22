<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
$idconteo = $_GET["idcab"];
include_once "bd_StoreControl.php";

$sentencia = $db->prepare("
  delete from StockDet where id in (
  select
			d.id
        from StockCab c
			left join StockDet d on c.id=d.FK_id_StockCab 
			left join Articulo ar on d.FK_ID_articulo=ar.id
        where subcategoria='".$id."' and c.id=".$idconteo."
  )
;");
$sentencia->execute([]);
$result = $sentencia->rowCount();

echo $result;
