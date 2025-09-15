<?php
include_once "bd_StoreControl.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['ids'] ?? '';
    $nombreGrupo = $_POST['nombre_grupo'] ?? '';
    $idUsuario = $_POST['idU'] ?? 0;

    if (empty($ids) || empty($nombreGrupo)) {
        echo "<script>alert('Faltan datos para guardar el grupo.'); window.history.back();</script>";
        exit;
    }

    try {
        $estado = 1;
        $comentario = $nombreGrupo;

        // Preparar y ejecutar el procedimiento almacenado con OUTPUT
        $stmt = $db->prepare("DECLARE @new_id INT;
                              EXEC sp_insert_group_and_sotsCE @estado = ?, @comentario = ?, @fk_iduser = ?, @fk_docnumsotcab_list = ?, @new_group_id = @new_id OUTPUT;
                              SELECT @new_id AS new_id;
                              INSERT INTO [dbo].[ced_groupsotdetCE]
                                    ([ItemCode]
                                    ,[Quantity]
                                    ,LineNum
                                    ,[DocNum_Sot]
                                    ,[DocEntry_Sot]
                                    ,[Dscription]
                                    ,[CodeBars]
                                    ,fk_idgroup)
                                SELECT sd.[ItemCode]
                                    ,sd.[Quantity]
                                    ,sd.LineNum
                                    ,sd.[DocNum_Sot]
                                    ,sd.[DocEntry_Sot]
                                    ,sd.[Dscription]
                                    ,sd.[CodeBars] 
                                    ,@new_id   
                                FROM SotDet_CE sd 
                                    join SotCab_CE sc on sd.DocNum_Sot =sc.DocNum
                                WHERE sc.DocStatus = 'O' and DocNum_Sot IN (
                                    SELECT fk_docnumsotcab 
                                    FROM ced_groupsotCE
                                    WHERE fk_idgroup = @new_id);
                              
                INSERT INTO [dbo].[ced_grouprecolCE]
                        ([ItemCode]
                        ,[Quantity]
                        ,[Scan]
                        ,[Dscription]
                        ,[CodeBars]
                        ,[fk_idgroup])
                            SELECT ItemCode,
                                    SUM(Quantity) AS stock, 
                                    0,
                                    Dscription AS descripcion,
                                    CodeBars AS codigoBarras, 
                                    @new_id
                                FROM SotDet_CE 
                                WHERE DocNum_Sot IN (
                                    SELECT fk_docnumsotcab 
                                    FROM ced_groupsotCE 
                                    WHERE fk_idgroup =@new_id
                                )
                                GROUP BY CodeBars, Dscription, ItemCode
                              
                              ");
        $stmt->execute([$estado, $comentario, $idUsuario, $ids]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nuevoId = $result['new_id'] ?? 0;

        // Redirigir con el ID del grupo
        header("Location: ../cediGrpLdidCE.php?idcab=$nuevoId");
        exit;
    } catch (PDOException $e) {
        echo "Error al guardar el grupo: " . $e->getMessage();
    }
} else {
    echo "Acceso no permitido.";
}
?>
