<?php
    header('Content-Type: application/json');
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])){
        $query = trim($_POST['query']);
        $query_escapado = intval($query);
        include_once "../cx/bd_scs.php";
        $q="SELECT c.DocDate,c.DocNum,c.Filler,c.DocNum_Tr,c.ToWhsCode,c.LineStatus,c.Comments,a.fk_emp,a.nombre
            FROM STORECONTROL.dbo.SotCab_MT AS c 
            INNER JOIN STORECONTROL.dbo.Almacen AS a ON a.cod_almacen=c.ToWHSCode
            WHERE c.Comments LIKE '%$query_escapado%'";
        $r=resp_simdim($q);
        if(!empty($r)){ echo json_encode($r); }else{ echo json_encode([]); }    
    }
?>